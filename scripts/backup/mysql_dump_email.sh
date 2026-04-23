#!/usr/bin/env bash

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ENV_FILE="${ENV_FILE:-${SCRIPT_DIR}/.env}"

if [[ -f "${ENV_FILE}" ]]; then
  set -a
  # shellcheck disable=SC1090
  source "${ENV_FILE}"
  set +a
fi

require_var() {
  local var_name="$1"
  if [[ -z "${!var_name:-}" ]]; then
    echo "[ERRO] Variavel obrigatoria ausente: ${var_name}" >&2
    exit 1
  fi
}

# Opcional
AUTO_DISCOVER_DB_CONTAINERS="${AUTO_DISCOVER_DB_CONTAINERS:-false}"

# Banco de dados (modo unico legado OU modo multi OU auto-discovery)
if [[ -z "${DB_TARGETS:-}" && "${AUTO_DISCOVER_DB_CONTAINERS}" != "true" ]]; then
  require_var DB_HOST
  require_var DB_PORT
  require_var DB_NAME
  require_var DB_USER
  require_var DB_PASS
fi

# E-mail (SMTP)
require_var SMTP_HOST
require_var SMTP_PORT
require_var SMTP_USER
require_var SMTP_PASS
require_var EMAIL_FROM
require_var EMAIL_TO

APP_INSTANCE="${APP_INSTANCE:-visitai}"
BACKUP_DIR="${BACKUP_DIR:-/tmp/visitaai-backups}"
RETENTION_DAYS="${RETENTION_DAYS:-7}"
SMTP_USE_TLS="${SMTP_USE_TLS:-true}"
EMAIL_SUBJECT_PREFIX="${EMAIL_SUBJECT_PREFIX:-[VisitaAi Backup]}"
EMAIL_MAX_ATTACHMENTS="${EMAIL_MAX_ATTACHMENTS:-20}"
EMAIL_INCLUDE_ATTACHMENTS="${EMAIL_INCLUDE_ATTACHMENTS:-true}"

mkdir -p "${BACKUP_DIR}"

timestamp="$(date +%Y%m%d_%H%M%S)"
tmp_list_file="${BACKUP_DIR}/.${APP_INSTANCE}_backup_list_${timestamp}.txt"
: > "${tmp_list_file}"

run_dump_from_target() {
  local target_app="$1"
  local target_db="$2"
  local target_host="$3"
  local target_port="$4"
  local target_user="$5"
  local target_pass="$6"
  local target_container="$7"

  local safe_app safe_db base_name sql_file gz_file
  safe_app="$(echo "${target_app}" | tr -cs '[:alnum:]_-' '_')"
  safe_db="$(echo "${target_db}" | tr -cs '[:alnum:]_-' '_')"
  base_name="${safe_app}_${safe_db}_${timestamp}"
  sql_file="${BACKUP_DIR}/${base_name}.sql"
  gz_file="${sql_file}.gz"

  echo "[INFO] Gerando dump: app=${target_app} db=${target_db}"

  if [[ -n "${target_container}" ]]; then
    if ! command -v docker >/dev/null 2>&1; then
      echo "[ERRO] docker nao encontrado para o target ${target_app}/${target_db}" >&2
      return 1
    fi

    docker exec "${target_container}" sh -lc \
      "MYSQL_PWD='${target_pass}' mysqldump --host='${target_host}' --port='${target_port}' --user='${target_user}' --single-transaction --quick --routines --triggers --set-gtid-purged=OFF --no-tablespaces '${target_db}'" \
      > "${sql_file}"
  else
    MYSQL_PWD="${target_pass}" mysqldump \
      --host="${target_host}" \
      --port="${target_port}" \
      --user="${target_user}" \
      --single-transaction \
      --quick \
      --routines \
      --triggers \
      --set-gtid-purged=OFF \
      --no-tablespaces \
      "${target_db}" > "${sql_file}"
  fi

  gzip -9 "${sql_file}"

  local checksum size_human
  if command -v sha256sum >/dev/null 2>&1; then
    checksum="$(sha256sum "${gz_file}" | awk '{print $1}')"
  else
    checksum="$(shasum -a 256 "${gz_file}" | awk '{print $1}')"
  fi

  size_human="$(du -h "${gz_file}" | awk '{print $1}')"

  echo "[INFO] Dump pronto: ${gz_file} (${size_human})"
  echo "[INFO] SHA-256: ${checksum}"
  echo "${target_app}|${target_db}|${gz_file}|${size_human}|${checksum}" >> "${tmp_list_file}"
}

get_container_env_var() {
  local container_name="$1"
  local key="$2"

  docker inspect -f '{{range .Config.Env}}{{println .}}{{end}}' "${container_name}" \
    | awk -v key="${key}" 'index($0, key "=") == 1 { print substr($0, length(key) + 2); exit }'
}

discover_and_dump_coolify_dbs() {
  if ! command -v docker >/dev/null 2>&1; then
    echo "[ERRO] docker nao encontrado para auto-discovery" >&2
    exit 1
  fi

  echo "[INFO] Usando modo auto-discovery (containers db-*)"

  local containers
  containers="$(docker ps --format '{{.Names}}' | rg '^db-' || true)"
  if [[ -z "${containers}" ]]; then
    echo "[ERRO] Nenhum container db-* em execucao foi encontrado" >&2
    exit 1
  fi

  while IFS= read -r container_name; do
    [[ -z "${container_name}" ]] && continue

    local target_db target_user target_pass target_port fqdn target_app
    target_db="$(get_container_env_var "${container_name}" "MYSQL_DATABASE")"
    [[ -z "${target_db}" ]] && target_db="$(get_container_env_var "${container_name}" "DB_DATABASE")"

    target_user="$(get_container_env_var "${container_name}" "MYSQL_USER")"
    [[ -z "${target_user}" ]] && target_user="$(get_container_env_var "${container_name}" "DB_USERNAME")"

    target_pass="$(get_container_env_var "${container_name}" "MYSQL_PASSWORD")"
    [[ -z "${target_pass}" ]] && target_pass="$(get_container_env_var "${container_name}" "DB_PASSWORD")"

    target_port="$(get_container_env_var "${container_name}" "DB_PORT")"
    [[ -z "${target_port}" ]] && target_port="3306"

    fqdn="$(get_container_env_var "${container_name}" "SERVICE_FQDN_WEB")"
    target_app="${fqdn%%.*}"
    if [[ -z "${target_app}" || "${target_app}" == "${fqdn}" ]]; then
      target_app="$(get_container_env_var "${container_name}" "COOLIFY_RESOURCE_UUID")"
    fi
    [[ -z "${target_app}" ]] && target_app="${container_name}"

    if [[ -z "${target_db}" || -z "${target_user}" || -z "${target_pass}" ]]; then
      echo "[WARN] Pulando ${container_name}: faltam variaveis MYSQL_DATABASE/MYSQL_USER/MYSQL_PASSWORD" >&2
      continue
    fi

    run_dump_from_target "${target_app}" "${target_db}" "127.0.0.1" "${target_port}" "${target_user}" "${target_pass}" "${container_name}"
  done <<< "${containers}"
}

if [[ -n "${DB_TARGETS:-}" ]]; then
  echo "[INFO] Usando modo multi-target (DB_TARGETS)"

  IFS=';' read -r -a targets <<< "${DB_TARGETS}"
  for raw_target in "${targets[@]}"; do
    trimmed="$(echo "${raw_target}" | xargs)"
    [[ -z "${trimmed}" ]] && continue

    IFS='|' read -r target_app target_db target_host target_port target_user target_pass target_container <<< "${trimmed}"

    if [[ -z "${target_app:-}" || -z "${target_db:-}" || -z "${target_host:-}" || -z "${target_port:-}" || -z "${target_user:-}" || -z "${target_pass:-}" ]]; then
      echo "[ERRO] Target invalido em DB_TARGETS: '${trimmed}'" >&2
      echo "[ERRO] Formato esperado: app|db|host|port|user|pass|container(opcional)" >&2
      exit 1
    fi

    run_dump_from_target "${target_app}" "${target_db}" "${target_host}" "${target_port}" "${target_user}" "${target_pass}" "${target_container:-}"
  done
elif [[ "${AUTO_DISCOVER_DB_CONTAINERS}" == "true" ]]; then
  discover_and_dump_coolify_dbs
else
  run_dump_from_target "${APP_INSTANCE}" "${DB_NAME}" "${DB_HOST}" "${DB_PORT}" "${DB_USER}" "${DB_PASS}" "${DB_CONTAINER:-}"
fi

if [[ ! -s "${tmp_list_file}" ]]; then
  echo "[ERRO] Nenhum backup foi gerado" >&2
  exit 1
fi

export TARGETS_FILE="${tmp_list_file}"
export APP_INSTANCE
export BACKUP_TIMESTAMP="${timestamp}"
export SMTP_HOST SMTP_PORT SMTP_USER SMTP_PASS SMTP_USE_TLS
export EMAIL_FROM EMAIL_TO EMAIL_SUBJECT_PREFIX
export EMAIL_MAX_ATTACHMENTS EMAIL_INCLUDE_ATTACHMENTS

python3 <<'PY'
import os
import smtplib
from email.message import EmailMessage
from pathlib import Path

subject_prefix = os.environ["EMAIL_SUBJECT_PREFIX"]
app_instance = os.environ["APP_INSTANCE"]
timestamp = os.environ["BACKUP_TIMESTAMP"]
targets_file = Path(os.environ["TARGETS_FILE"])
max_attachments = int(os.environ.get("EMAIL_MAX_ATTACHMENTS", "20"))
include_attachments = os.environ.get("EMAIL_INCLUDE_ATTACHMENTS", "true").lower() == "true"

targets = []
for line in targets_file.read_text(encoding="utf-8").splitlines():
    app, db_name, gz_file, size_human, checksum = line.split("|", 4)
    targets.append(
        {
            "app": app,
            "db_name": db_name,
            "gz_file": Path(gz_file),
            "size_human": size_human,
            "checksum": checksum,
        }
    )

msg = EmailMessage()
msg["Subject"] = f"{subject_prefix} {app_instance} / {timestamp} / {len(targets)} banco(s)"
msg["From"] = os.environ["EMAIL_FROM"]
msg["To"] = os.environ["EMAIL_TO"]
body_lines = [
    "Backups MySQL gerados com sucesso.",
    f"Instancia: {app_instance}",
    f"Timestamp: {timestamp}",
    f"Total de bancos: {len(targets)}",
    "",
]

for idx, t in enumerate(targets, start=1):
    body_lines.extend(
        [
            f"{idx}. App: {t['app']}",
            f"   Banco: {t['db_name']}",
            f"   Arquivo: {t['gz_file'].name}",
            f"   Tamanho: {t['size_human']}",
            f"   SHA-256: {t['checksum']}",
            "",
        ]
    )

attach_targets = []
if include_attachments:
    attach_targets = targets[:max_attachments]
    if len(targets) > max_attachments:
        body_lines.append(f"Aviso: limite de anexos atingido ({max_attachments}).")

msg.set_content("\n".join(body_lines))

for t in attach_targets:
    with t["gz_file"].open("rb") as f:
        payload = f.read()

    msg.add_attachment(
        payload,
        maintype="application",
        subtype="gzip",
        filename=t["gz_file"].name,
    )

host = os.environ["SMTP_HOST"]
port = int(os.environ["SMTP_PORT"])
user = os.environ["SMTP_USER"]
password = os.environ["SMTP_PASS"]
use_tls = os.environ.get("SMTP_USE_TLS", "true").lower() == "true"

if use_tls:
    with smtplib.SMTP(host, port, timeout=60) as server:
        server.starttls()
        server.login(user, password)
        server.send_message(msg)
else:
    with smtplib.SMTP_SSL(host, port, timeout=60) as server:
        server.login(user, password)
        server.send_message(msg)
PY

echo "[INFO] E-mail enviado para ${EMAIL_TO}"
rm -f "${tmp_list_file}"

echo "[INFO] Limpando backups antigos (>${RETENTION_DAYS} dias) em ${BACKUP_DIR}"
find "${BACKUP_DIR}" -type f -name '*.sql.gz' -mtime "+${RETENTION_DAYS}" -delete

echo "[OK] Backup concluido"
