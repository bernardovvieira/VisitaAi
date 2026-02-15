# Visita Aí - Controle de Visitas Epidemiológicas

Sistema desenvolvido para a gestão de visitas epidemiológicas, utilizando o framework Laravel.

> **Desenvolvido por:** Bitwise Technologies  
> **CNPJ:** 49.973.865/0001-23

---

## ⚠️ Requisitos

O projeto é executado com **Docker**. Você precisará de:

- **Docker**
- **Docker Compose**

Não é necessário instalar PHP, Composer, Node ou MySQL na máquina local.

---

## 🛠️ Instalação (Docker)

Clone o repositório:

```bash
git clone https://github.com/bernardovvieira/VisitaAi.git
cd VisitaAi
```

Crie o arquivo de ambiente a partir do exemplo:

```bash
cp .env.example .env
```

No `.env`, defina a senha do banco (usada pela aplicação e pelo MySQL no Docker). A conexão já vem compatível com o Docker:

- `DB_HOST=db`
- `DB_PORT=3306`
- `DB_DATABASE=visita_ai`
- `DB_USERNAME=visita`
- `DB_PASSWORD=` — **obrigatório:** defina uma senha forte; o `docker-compose` usa esse valor para o MySQL. Nunca commite o `.env`.

Suba os containers:

```bash
docker compose up -d --build
```

Gere a chave da aplicação e rode as migrações **dentro** do container da aplicação:

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed   # opcional
```

Acesse no navegador: [http://localhost](http://localhost) (porta 80, servida pelo Nginx).

---

## 🔁 Comandos úteis (Docker)

```bash
# Subir os serviços
docker compose up -d

# Ver logs
docker compose logs -f app

# Executar artisan no container
docker compose exec app php artisan migrate
docker compose exec app php artisan tinker

# Parar tudo
docker compose down
```

**Serviços:** `app` (PHP-FPM), `db` (MySQL 8, porta 3307 no host), `web` (Nginx na porta 80).

Detalhes de **login (use_email/use_cpf)** e **2FA** (ativar sem senha, desativar com senha): ver `docs/AUTH.md`.

---

## 🚀 Deploy em produção (push → VPS)

Existe um ambiente de **demo** instanciado para testes.

**Com Docker (Coolify, etc.):** o `entrypoint.sh` já roda `migrate`, `route:clear` e `config:clear` quando o container sobe. Push e o deploy faz o resto.

#### Post-deploy: migrations e seeds

**Importante:** não use `migrate:fresh` no post-deploy de produção — toda subida de build apagaria o banco. Use apenas migrações incrementais.

| Instância | URL | Post-deploy (a cada build) |
|-----------|-----|----------------------------|
| **Base / Municípios** | visitaai.cloud, ibirapuita.visitaai.cloud | `php artisan migrate --force` |
| **Demo** | demo.visitaai.cloud | `php artisan migrate --force` (ou `migrate:fresh` + seed se quiser resetar a demo a cada deploy) |

**Primeira vez (nova instância):** rode uma vez manualmente, antes de ir para produção:
```bash
php artisan migrate:fresh --force && php artisan db:seed --class=AdminBaseSeeder --force   # base/municípios
php artisan migrate:fresh --force && php artisan db:seed --force                           # demo
```

#### Nome da aplicação

Formato: `Visita Aí - {prefixo} - Sistema de Apoio à Vigilância Epidemiológica Municipal`

| Instância | APP_INSTANCE_TYPE | Prefixo exibido |
|-----------|-------------------|-----------------|
| Base | `base` | Base |
| Demo | `demo` | Demo |
| Local (com cidade cadastrada) | omitir | Nome da cidade do 1º Local |
| Local (sem cidade) | omitir | Local |

Alternativa: use `APP_NAME=Base`, `APP_NAME=Demo` ou `APP_NAME=Soledade` (ex.).

**Sem Docker (ex.: Hostinger com git pull):** após cada `git pull`, rode na VPS:
```bash
chmod +x deploy.sh   # só na primeira vez
./deploy.sh
```
Ou manualmente: `php artisan migrate --force`, `php artisan route:clear`, `php artisan config:clear`.

---

## 🫱🏽‍🫲🏼 Contribuição

Este repositório possui **licença restrita** e a colaboração é limitada.  
Caso tenha interesse em contribuir, entre em contato com a Bitwise Technologies.

---

## 📃 Licença

Este projeto é de **uso restrito**.
**Não é permitido distribuição, modificação ou uso comercial sem autorização da Bitwise Technologies**.
Todos os direitos reservados.

---

## 📱 Contato e Suporte

**Bitwise Technologies** · [bitwise.dev.br](https://bitwise.dev.br)  
Suporte técnico do Visita Aí:

- **Site:** [bitwise.dev.br](https://bitwise.dev.br)
- **E-mail:** bernardo@bitwise.dev.br
- **LinkedIn:** [linkedin.com/in/bernardovivianvieira](https://www.linkedin.com/in/bernardovivianvieira)
