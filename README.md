# Visita Aí - Controle de Visitas (Vigilância Entomológica e Controle Vetorial)

Sistema desenvolvido para a gestão de visitas de vigilância entomológica e controle vetorial, utilizando o framework Laravel.

**Perfis no sistema (conformes ao MS):** Gestor municipal; ACE (Agente de Combate às Endemias); ACS (Agente Comunitário de Saúde) — Lei nº 11.350/2006 e Diretrizes Nacionais para Atuação Integrada dos ACE e ACS.

### Funcionalidades em resumo

- **Locais e visitas** de vigilância entomológica / PNCD (atividades 1–8, LIRAa para ACS onde aplicável).
- **Doenças monitoradas**, pendências, relatórios, consulta pública por código do imóvel, QR Code.
- **Ocupantes do imóvel (Visita Aí):** registro opcional vinculado ao cadastro de **locais**, com dados operacionais municipais (faixas etárias agregadas no painel, escolaridade e renda em categorias). **Não** substitui e-SUS APS, PEC, Ficha de Visita Domiciliar e Territorial nem e-SUS Território — ver `docs/CONFORMIDADE-MS-FLUXO.md` (§8) e `docs/ESUS-SISPNCD-DIFERENCIACAO-E-MEDIDAS.md`. Textos e opções de formulário: `config/visitaai_municipio.php`.

### Rotas HTTP

- `routes/web.php` — ponto de entrada; inclui `routes/web/public.php` (público / consulta / ping) e `routes/web/authenticated.php` (área logada).
- Dentro da área autenticada, rotas agrupadas por módulo: `routes/web/pncd.php` (operação PNCD / ACE e ACS), `routes/web/gestao.php` (painel do gestor, inclui indicadores agregados de ocupantes em `gestor/indicadores/ocupantes`), `routes/web/conta.php` (perfil e preferências).
- Autenticação Fortify/Breeze: `routes/auth.php`.

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

### Testes automatizados

O `phpunit.xml` configura **SQLite em memória** para que `php artisan test` rode na máquina local sem MySQL/Docker. Em produção e no Docker, o banco continua sendo o MySQL definido no `.env`. Para forçar outro banco nos testes, use `.env.testing` ou variáveis de ambiente.

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

**Várias instâncias num único deploy (subdomínio → base MySQL):** ver a secção [Multi-tenant (registry): subdomínio → MySQL](#multi-tenant-registry-subdomínio--mysql) e o detalhe de arquitetura em `docs/MULTI-TENANT-SUBDOMINIO-DESIGN.md`.

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

## Multi-tenant (registry): subdomínio → MySQL

Um único deploy pode atender **vários municípios**: o **host** (`mun.visitaai.cloud`) escolhe o **slug**; o **registry** (`registry_tenants`) indica qual **schema MySQL** usar. O código liga a conexão `mysql` a esse schema antes de servir o pedido.

### O que vai no `.env` — **uma vez** (global)

Estas variáveis **habilitam** o modo registry e o provisionamento automático de **novas** bases. **Não** se adiciona um `.env` por cliente; **não** se cria variável `TENANT_*` por município.

| Variável | Função |
|----------|--------|
| `TENANT_REGISTRY_ENABLED=true` | Liga resolução por subdomínio + registry. |
| `REGISTRY_DB_DATABASE` | Base onde existe a tabela `registry_tenants` (pode ser a mesma que `DB_DATABASE` num modelo “uma base só” para demo/registry). |
| `TENANT_REGISTRY_BASE_DOMAINS` | Lista de FQDN (ex.: `visitaai.cloud`); o primeiro label do host vira slug. |
| `TENANT_REGISTRY_BOOTSTRAP` (*opcional*) | No arranque do container: garante uma linha inicial no registry (ex. `demo` → `visita_ai`). |
| `TENANT_PROVISION_ENABLED=true` (*recomendado para vários clientes*) | Permite **criar** schema MySQL (`CREATE DATABASE`), `GRANT` opcional e `migrate` ao **provisionar** um tenant novo (CLI ou UI). Requer utilizador com `CREATE DATABASE` na conexão `tenant_provision` (ver `.env.example`: `TENANT_PROVISION_DB_*` se o user da app não tiver esse privilégio). |
| `TENANT_DATABASE_PREFIX` | Prefixo do nome automático da base (ex.: `visita_` → slug `x` vira `visita_x`). |
| `REGISTRY_ADMIN_EMAILS` | E-mails (`users.use_email`) que acedem a `/system/tenant-registry`. |
| `DB_*` | Credenciais **por defeito** da app; cada linha do registry pode sobrescrever host/user/pass só desse tenant (opcional). |

No **Docker/Coolify**, o `entrypoint.sh` já executa `migrate`, `registry:migrate` e `tenant-registry:bootstrap` (quando ligado). Não é necessário repetir isso no `.env` por cliente.

### O que **não** resolve só o `.env` — **por cada cliente novo**

Adicionar cliente **não** é “editar o `.env` de novo”. É garantir três coisas:

1. **Routing / TLS** — O pedido HTTP tem de chegar à **mesma** app para o host certo (`https://mun.visitaai.cloud`).
2. **Registry + base de dados** — Tem de existir uma linha em `registry_tenants` (slug → nome do schema) e o schema tem de existir e estar migrado.
3. **Utilizadores na base desse cliente** — Gestores/ACE na base **daquele** tenant (seed ou cadastro), não “no ar” pelo `.env`.

### Coolify: **só “Domains” não basta**

- Colocar um **novo domínio** em **Domains** no Coolify só configura o **proxy reverso e o certificado** (HTTPS). **Não** cria a base MySQL nem a linha no registry.
- Se usares **wildcard** (`*.visitaai.cloud`) no DNS e no painel (quando suportado), **novos subdomínios** podem passar a resolver para a app **sem** acrescentar um domínio novo por cliente — mesmo assim precisas do passo de **provisionamento** (abaixo).

### Adicionar um **novo município** (checklist)

1. **Slug** — Ex.: `ibirapuita` → URL `https://ibirapuita.visitaai.cloud` (o slug segue a validação do registry: letras minúsculas, números e hífens).
2. **DNS / Coolify** — Sem wildcard: adiciona `https://ibirapuita.visitaai.cloud` no **mesmo** serviço que serve a app. Com wildcard: garante DNS `*.visitaai.cloud` → servidor.
3. **Provisionar base + registry + migrações** — **Uma** destas formas (com `TENANT_PROVISION_ENABLED=true` e credenciais adequadas):

   **Opção A — linha de comando** (típico em produção), dentro do contentor PHP:

   ```bash
   php artisan tenants:provision ibirapuita --environment=production --display-name="Município de Ibirapuita"
   ```

   Cria o schema (ex. `visita_ibirapuita`), corre `php artisan migrate` nesse schema e insere a linha no registry.

   **Opção B — interface web** — Utilizador cujo e-mail está em `REGISTRY_ADMIN_EMAILS`: acede a **`/system/tenant-registry`** → **Novo tenant** → preenche slug/ambiente e marca **“Criar base MySQL, permissões e migrações automaticamente”** (só aparece com provisionamento ligado).

   **Opção C — manual** — Criar o schema no MySQL à mão, correr `migrate` nesse schema, inserir a linha no registry pela UI **sem** marcar a opção automática (ou equivalente SQL).

4. **Dados iniciais** — Na base **desse** cliente, criar utilizadores (ex. gestor). Opcional: `php artisan tenants:seed --tenant=ibirapuita --force` se usares seeders por tenant.

5. **Depois de deploys com novas migrações** — Aplicar migrações em **todos** os tenants, por exemplo:

   ```bash
   php artisan tenants:migrate --force
   ```

   Ou agendar com `TENANT_REGISTRY_SCHEDULE_MIGRATE=true` e **cron** do Laravel (`schedule:run`).

### Resumo prático

| Pergunta | Resposta |
|----------|----------|
| Novo cliente = só alterar `.env`? | **Não.** O `.env` é global; cada cliente precisa de registry + base (CLI, UI ou manual). |
| Novo cliente = só domínio no Coolify? | **Não.** Domínio só encaminha tráfego; falta provisionar tenant. |
| Onde “clico” para criar o cliente? | **`tenants:provision {slug}`** ou **`/system/tenant-registry`**, não variáveis novas no `.env`. |

Documentação de arquitetura e decisões: `docs/MULTI-TENANT-SUBDOMINIO-DESIGN.md`. Exemplos de variáveis: `.env.example`.

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
