# Backup MySQL + E-mail (Coolify)

Script principal: `backup/mysql_dump_email.sh`

Este guia documenta toda a configuracao para reinstalar ou recriar o backup no futuro.

## Objetivo

- Gerar dump MySQL (`.sql.gz`) de um ou varios bancos.
- Enviar e-mail com resumo e anexos do backup.
- Limpar arquivos antigos automaticamente.
- Rodar por `cron` em `terca/quinta/sabado as 02:00`.

## Pre-requisitos

- `python3`
- `docker` (para modo auto-discovery no Coolify)
- `mysqldump` no host (somente se usar modo sem container)
- Acesso SMTP valido (host, usuario, senha)

## Estrutura esperada

```bash
/home/ubuntu/backup/mysql_dump_email.sh
/home/ubuntu/backup/.env
```

## Passo a passo rapido (reconfiguracao)

1. Garanta que o script exista em `backup/mysql_dump_email.sh`.
2. Crie/atualize `backup/.env` (modelo abaixo).
3. Ajuste permissao do `.env`:

```bash
chmod 600 /home/ubuntu/backup/.env
```

4. Teste manual:

```bash
cd /home/ubuntu
/bin/bash /home/ubuntu/backup/mysql_dump_email.sh
```

5. Configure o cron:

```bash
0 2 * * 2,4,6 /bin/bash -lc 'cd /home/ubuntu && /bin/bash /home/ubuntu/backup/mysql_dump_email.sh >> /var/log/mysql-backup.log 2>&1'
```

## Configuracao recomendada (`.env`) - Auto-discovery

Use este modo para pegar automaticamente novos bancos (`db-*`) criados no Coolify.

```bash
APP_INSTANCE=SEU_AMBIENTE
BACKUP_DIR=/tmp/mysql-backups
RETENTION_DAYS=7
EMAIL_SUBJECT_PREFIX='[Coolify Backup]'
EMAIL_INCLUDE_ATTACHMENTS=true
EMAIL_MAX_ATTACHMENTS=20

# Modo automatico: descobre todos os containers db-* em execucao
AUTO_DISCOVER_DB_CONTAINERS=true

SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=seu_email@dominio.com
SMTP_PASS=SENHA_SMTP
SMTP_USE_TLS=true
EMAIL_FROM=seu_email@dominio.com
EMAIL_TO=destino@dominio.com
```

## Modos de operacao

O script escolhe o modo nesta ordem:

1. `DB_TARGETS` definido -> modo manual multi-banco
2. `AUTO_DISCOVER_DB_CONTAINERS=true` -> modo automatico Coolify
3. Variaveis `DB_HOST/DB_NAME/...` -> modo unico legado

### Modo manual multi-banco (`DB_TARGETS`)

Formato:

`app|db|host|port|user|pass|container(opcional);app2|db2|host2|port2|user2|pass2|container2`

Exemplo:

```bash
DB_TARGETS='demo|visita_ai|127.0.0.1|3306|visita|SENHA_DB|db-demo-xxxxx;barroscassal|visita_ai|127.0.0.1|3306|visita|SENHA_DB|db-barroscassal-yyyyy'
```

### Modo unico legado

Use quando precisar rodar um unico banco sem auto-discovery:

```bash
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=seu_banco
DB_USER=seu_usuario
DB_PASS=sua_senha
DB_CONTAINER=db-nome-opcional
```

## Como o auto-discovery nomeia cada app

Para cada container `db-*`, o script tenta:

1. `SERVICE_FQDN_WEB` (ex.: `soledade.visitaai.cloud` -> app `soledade`)
2. `COOLIFY_RESOURCE_UUID`
3. Nome do proprio container (fallback)

## Logs e validacao

- Log cron: `/var/log/mysql-backup.log`
- Ultimas linhas:

```bash
tail -n 50 /var/log/mysql-backup.log
```

Sinais de sucesso:

- `[INFO] Usando modo auto-discovery (containers db-*)`
- `[INFO] Dump pronto: ...`
- `[INFO] E-mail enviado para ...`
- `[OK] Backup concluido`

## Resolucao de problemas

- `mysqldump: command not found`
  - Use modo com `DB_CONTAINER` ou auto-discovery via Docker.
- `No such container`
  - Nome do container mudou; prefira `AUTO_DISCOVER_DB_CONTAINERS=true`.
- E-mail nao enviado
  - Revise `SMTP_HOST`, `SMTP_PORT`, `SMTP_USER`, `SMTP_PASS`, `SMTP_USE_TLS`.
- Nao achou bancos no auto-discovery
  - Verifique se existem containers `db-*` ativos em `docker ps`.

## Seguranca

- Nunca versionar `backup/.env` com segredos.
- Manter `chmod 600` no `.env`.
- Evitar senhas no comando de cron (usar sempre `.env`).
