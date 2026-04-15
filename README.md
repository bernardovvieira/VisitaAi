# Visita Aí - Plataforma Municipal de Operação Territorial e Indicadores

Sistema desenvolvido para gestão municipal de operação territorial, indicadores e transparência pública, com módulos especializados de vigilância em saúde quando aplicável, utilizando o framework Laravel.

## Estrutura atual dos repositórios

- Este repositório (`application`) contém a aplicação principal em Laravel (operações, gestão, autenticação, API e regras de negócio).
- A landing institucional roda em repositório separado (`website`), em Nuxt 3.
- Imagens e screenshots usados na landing são mantidos em `website/public/images/*`. A remoção desses ativos no Laravel não afeta o build da landing.

## Escopo do produto (resumo)

- **Núcleo municipal (sempre ativo):** cadastro territorial, operação de campo, painéis/indicadores, relatórios e transparência por código/QR.
- **Especializações em saúde (quando contratadas):** vigilância entomológica, LIRAa, PNCD e agravos monitorados.
- **Módulos complementares (opcionais):** cadastro socioeconômico/ocupantes e recortes agregados adicionais.

Toda a documentação funcional, arquitetural e de testes deste repositório está centralizada neste README principal.

**Perfis no sistema (conformes ao MS):** Gestor municipal; ACE (Agente de Combate às Endemias); ACS (Agente Comunitário de Saúde), Lei nº 11.350/2006 e Diretrizes Nacionais para Atuação Integrada dos ACE e ACS.

### Funcionalidades em resumo

- **Locais e visitas** de vigilância entomológica / PNCD (atividades 1 a 8, LIRAa para ACS onde aplicável).
- **Doenças monitoradas**, pendências, relatórios, consulta pública por código do imóvel, QR Code.
- **Ocupantes do imóvel (Visita Aí):** registro opcional vinculado a **locais**, com dados operacionais municipais agregados no painel. **Não** substitui e-SUS APS, PEC, Ficha de Visita Domiciliar e Territorial nem e-SUS Território. Textos legais e opções: `config/visitaai_municipio.php`.

## Arquitetura de Produto e Mensagem

## Atualizações recentes (abril/2026)

- Padronização ampla de formulários Blade com componentes reutilizáveis: `x-form-field` e `x-empty-state`.
- Contratos de exportação de indicadores de ocupantes ajustados para CSV textual (`text/csv`), com rótulos de interface alinhados.
- Bloco de alto risco de locais (mapa/CEP/geolocalização) fechado em `create` e `edit`, removendo handler inline de geolocalização e mantendo paridade de IDs usados por scripts.
- Bloco socioeconômico ampliado com confrontantes e total de banheiros calculado automaticamente (cliente e persistência).
- Regressão rápida completa executada com sucesso via `php artisan test`.

### Objetivo do produto
Visita Aí é uma plataforma municipal para organizar operação territorial e transformar dados de campo em indicadores para decisão, gestão e transparência.

### Estado atual da solução (abril/2026)
- `application` (Laravel): sistema principal da operação municipal.
- `website` (Nuxt): landing institucional e comercial.
- Os dois repositórios compartilham a mesma mensagem de produto, com ciclos de deploy independentes.

### Arquitetura de escopo

1. **Núcleo municipal (sempre ativo)**
- Cadastro territorial de locais/imóveis
- Operação de campo (visitas e pendências)
- Painéis e indicadores para gestão
- Relatórios e exportações
- Consulta pública por código/QR sem dados sensíveis
- Governança de acesso por perfil e auditoria

2. **Especializações em saúde (quando contratadas)**
- Vigilância entomológica
- LIRAa
- PNCD
- Cadastro e leitura de doenças/agravos monitorados

3. **Módulos complementares (opcionais)**
- Cadastro de ocupantes/socioeconômico
- Indicadores agregados complementares para planejamento local

### Proposta de valor
- Para gestor: indicadores acionáveis, leitura de território e prestação de contas
- Para coordenação técnica: rotina padronizada, rastreabilidade e continuidade
- Para equipe de campo: captura simples, fluxo direto e sincronização
- Para município/cidadão: transparência operacional com consulta pública segura

### Linguagem oficial recomendada
Usar sempre:
- Núcleo municipal
- Especializações em saúde
- Módulos complementares

Evitar:
- Definir o produto apenas como sistema de dengue
- Misturar objetivo principal com módulo opcional

### Regra de comunicação
Em qualquer tela, proposta comercial ou material institucional, responder em ordem:
1. Qual problema municipal resolvemos?
2. Qual o núcleo obrigatório da plataforma?
3. Quais especializações e opcionais podem ser ativados?

### Observação de conformidade
Quando houver uso de módulo socioeconômico ou ocupantes, manter a comunicação de que não substitui os sistemas nacionais obrigatórios (como e-SUS/PEC, quando aplicável).

## Padrão de UI e Conteúdo

### Objetivo
Garantir telas simples, elegantes e funcionais, com linguagem consistente e foco em decisão.

### Estrutura obrigatória de tela
- Título curto orientado a benefício
- Subtítulo de uma frase com contexto
- Bloco principal de conteúdo
- Uma ação primária em destaque
- Ações secundárias discretas

### Regras de texto
- Frases curtas e diretas
- Evitar repetição de conceitos
- Evitar jargão técnico sem necessidade
- Preferir verbos de ação: Registrar, Consultar, Exportar, Sincronizar
- Nomear módulos conforme arquitetura oficial: Núcleo, Especializações, Complementares

### Hierarquia visual
- Espaçamento consistente entre seções
- Títulos e subtítulos com escala tipográfica fixa
- Cartões com mesma lógica de raio, sombra e contraste
- Estados visuais padronizados: vazio, carregando, erro, sucesso

### Checklist de revisão de tela
- O usuário entende em 5 segundos o objetivo da tela?
- A ação principal está clara?
- Existe excesso de texto ou elementos decorativos?
- Os termos usados batem com o glossário do produto?
- A tela ajuda decisão ou só exibe informação?

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

## Suíte de testes de integração

### Resumo
Existe uma suíte abrangente de testes de integração para fluxos realistas do sistema com múltiplos usuários e operações.

Status de referência (abril/2026, comando `php artisan test`):
- 109 testes passando
- 441 asserções

Arquivo principal:
- `tests/Feature/CompleteSystemWorkflowIntegrationTest.php`

### Fluxos cobertos

1. Registro e aprovação de usuário
- Novo agente submete registro
- Gestor aprova usuário pendente

2. Autenticação e inatividade (2 meses)
- Login atualiza use_ultimo_login_em
- Usuário inativo por mais de 2 meses é inativado automaticamente
- Comando diário users:inactivate-inactive mantém sincronização

3. Atualização de perfil
- Alteração de nome, e-mail e tema
- Validação de e-mail único

4. Controle de acesso por perfil
- Gestor acessa área de gestão
- Agente não acessa área de gestor
- ACS acessa área de saúde

5. Upload e download de documentos de morador
- Upload de PDF/imagem
- Armazenamento de metadados
- Download autenticado e autorizado
- Validação de tipos e limite de tamanho (10MB)

6. Anonimização de usuário
- Usuário anonimiza a própria conta com confirmação de senha
- Gestor pode anonimizar outros usuários

7. Gestão de usuários pelo gestor
- Criação direta de usuários
- Listagem e administração de contas

### Comandos úteis de teste

Executar suíte de integração:
```bash
php artisan test tests/Feature/CompleteSystemWorkflowIntegrationTest.php
```

Executar um cenário específico:
```bash
php artisan test tests/Feature/CompleteSystemWorkflowIntegrationTest.php --filter="access_control"
```

Executar todos os testes:
```bash
php artisan test
```

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

**Primeira vez (nova instância):** rode migrações; crie o gestor municipal pela UI ou SQL conforme a política do município. Para ambiente de testes/demo com dados fictícios:
```bash
php artisan migrate:fresh --force && php artisan db:seed --force
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
