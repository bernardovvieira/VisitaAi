<p align="center">
  <img src="https://img.shields.io/badge/Laravel-^12.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel Version">
  <img src="https://img.shields.io/badge/Licença-Restrita-red?style=for-the-badge" alt="License">
</p>

<br>

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

No `.env`, confira a conexão com o banco (já compatível com o Docker):

- `DB_HOST=db`
- `DB_PORT=3306`
- `DB_DATABASE=visita_ai`
- `DB_USERNAME=visita`
- `DB_PASSWORD=` — use a mesma senha definida no `docker-compose.yaml` (ex.: `Melancia@13?` no exemplo do repositório; em produção, altere no compose e no `.env`).

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
