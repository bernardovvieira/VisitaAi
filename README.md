# Visita Aí - Controle de Visitas (Vigilância Entomológica e Controle Vetorial)

Sistema desenvolvido para a gestão de visitas de vigilância entomológica e controle vetorial, utilizando o framework Laravel.

**Perfis no sistema (conformes ao MS):** Gestor municipal; ACE (Agente de Combate às Endemias); ACS (Agente Comunitário de Saúde), Lei nº 11.350/2006 e Diretrizes Nacionais para Atuação Integrada dos ACE e ACS.

### Funcionalidades em resumo

- **Locais e visitas** de vigilância entomológica / PNCD (atividades 1 a 8, LIRAa para ACS onde aplicável).
- **Doenças monitoradas**, pendências, relatórios, consulta pública por código do imóvel, QR Code.
- **Ocupantes do imóvel (Visita Aí):** registro opcional vinculado a **locais**, com dados operacionais municipais agregados no painel. **Não** substitui e-SUS APS, PEC, Ficha de Visita Domiciliar e Territorial nem e-SUS Território. Textos legais e opções: `config/visitaai_municipio.php`.

### Rotas HTTP

- `routes/web.php`: ponto de entrada; inclui `routes/web/public.php` (público / consulta / ping) e `routes/web/authenticated.php` (área logada).
- Dentro da área autenticada, rotas agrupadas por módulo: `routes/web/pncd.php` (operação PNCD / ACE e ACS), `routes/web/gestao.php` (painel do gestor, inclui indicadores agregados de ocupantes em `gestor/indicadores/ocupantes`), `routes/web/conta.php` (perfil e preferências).
- Autenticação Fortify/Breeze: `routes/auth.php`.

> **Desenvolvido por:** Bitwise Technologies  
> **CNPJ:** 49.973.865/0001-23

## ⚠️ Requisitos

- **Produção (Coolify):** imagem definida no **`Dockerfile`** (PHP 8.4 + assets); não precisas de PHP na VPS manualmente.
- **Desenvolvimento local típico:** **Docker** + **Docker Compose** (só MySQL no `docker-compose.yml`) e, na máquina, **PHP 8.4**, **Composer** e **Node** para `artisan` e Vite; ver secção *Instalação local*.

---

## 🛠️ Instalação local

O ficheiro **`docker-compose.yml` deste repo** sobe **apenas MySQL** (desenvolvimento). PHP, Composer e Node correm **na tua máquina** (ou usas **Coolify / imagem** do `Dockerfile` para stack completa).

### 1) Clonar e dependências PHP/JS

```bash
git clone https://github.com/bernardovvieira/VisitaAi.git
cd VisitaAi
cp .env.example .env
```

No `.env`, para o MySQL do Compose use por exemplo:

- `DB_HOST=127.0.0.1` se o PHP corre **no host** (porta mapeada `3306:3306`), ou `DB_HOST=db` se o PHP estiver noutro container na mesma rede.
- `DB_PORT=3306`, `DB_DATABASE=visita_ai`, `DB_USERNAME=visita`, `DB_PASSWORD=`: alinha com `MYSQL_*` do `docker-compose.yml`.

```bash
composer install
npm ci
```

### 2) Subir só a base

```bash
docker compose up -d
```

O serviço `db` pode montar `docker/mysql/init/`: scripts `.sql` correm **só na primeira criação** do volume. Não há `MYSQL_ROOT_PASSWORD` fixo no `docker-compose.yml`: usa-se `MYSQL_RANDOM_ROOT_PASSWORD` (a password de `root` aparece nos logs na primeira subida). Para o dia a dia usa o user `visita` (`DB_*`). Volume antigo → SQL manual com esse user ou recria o volume.

### 3) Laravel (no host)

```bash
php artisan key:generate
php artisan migrate
php artisan db:seed   # opcional
npm run dev           # noutro terminal: assets Vite; ou `composer run dev` (serve + vite + fila + logs)
```

Abre a URL do `php artisan serve` (ex. `http://127.0.0.1:8000`) ou a que configurares.

**Produção / stack PHP+Nginx:** usa o **`Dockerfile`** multi-stage (build Vite + PHP-FPM + imagem `web`); no Coolify isso costuma estar definido no serviço, não neste `docker-compose.yml`.

---

## 🔁 Comandos úteis (Docker só MySQL)

```bash
docker compose up -d          # MySQL
docker compose logs -f db
docker compose exec db mysql -u visita -p visita_ai
docker compose down
```

### Testes automatizados

O `phpunit.xml` configura **SQLite em memória** para que `php artisan test` rode na máquina local sem MySQL/Docker. Em produção e no Docker, o banco continua sendo o MySQL definido no `.env`. Para forçar outro banco nos testes, use `.env.testing` ou variáveis de ambiente.

---

## 🚀 Deploy em produção (Coolify / VPS)

### Uma aplicação por cliente (recomendado)

Cada município tem **o seu recurso** no Coolify: **mesma branch Git**, **URL própria** (`APP_URL`), **`APP_KEY` próprio**, **MySQL dedicado** (`DB_*`).

**Novo cliente:** criar/duplicar a app, apontar o Git, configurar domínio + secrets, `migrate` (o `entrypoint.sh` já corre `migrate` ao arrancar). Opcional no painel: `php artisan migrate --force --no-interaction`.

**Sandbox vs produção:** duas instâncias (ex. `sbx.*` e produção), bases **separadas**; nunca a mesma base.

**Post-deploy:** não uses `migrate:fresh` com dados reais. Opcional: `php artisan config:clear && php artisan route:clear`.

`Dockerfile` + `entrypoint.sh`: build Vite + PHP-FPM. O `entrypoint.sh` corre `migrate`, `route:clear` e `config:clear` ao arrancar. O `docker-compose.yml` do repo sobe **só MySQL** para dev local.

#### Post-deploy: migrations e seeds

**Importante:** não use `migrate:fresh` no post-deploy de produção: toda subida de build apagaria o banco. Use apenas migrações incrementais.

| Cenário | Post-deploy típico |
|---------|---------------------|
| **Uma app por cliente** | `php artisan migrate --force --no-interaction` |
| **Demo / reset controlado** | `migrate` incremental; só `migrate:fresh` + seed se for política explícita dessa instância |

**Primeira vez (nova instância):** rode uma vez manualmente, antes de ir para produção:
```bash
php artisan migrate:fresh --force && php artisan db:seed --class=AdminBaseSeeder --force   # base/municípios
php artisan migrate:fresh --force && php artisan db:seed --force                           # demo
```

#### Nome da aplicação

Formato: `Visita Aí - {prefixo} - Sistema de Apoio à Vigilância Entomológica e Controle Vetorial Municipal`

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
