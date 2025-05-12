<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-^12.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel Version">
  <img src="https://img.shields.io/badge/Licença-Restrita-red?style=for-the-badge" alt="License">
</p>

<br>

# Visita Aí - Controle de Visitas Epidemiológicas

Sistema acadêmico desenvolvido para a gestão de visitas epidemiológicas, utilizando o framework Laravel.

> **Desenvolvedor:** Bernardo Vivian Vieira  
> **Disciplina:** Laboratório de Engenharia de Software — UPF (2025/1)

---

## ⚠️ Requisitos

Para rodar este projeto, você precisará de:

- **PHP 8.2 ou superior**
- **Composer** (gerenciador de pacotes PHP)
- **Node.js 18 ou superior** (necessário para compilar os assets do frontend)
- **NPM** (instalado automaticamente com o Node.js)
- **MySQL ou equivalente** (banco de dados relacional)
- **Servidor Web** (use o servidor embutido com `php artisan serve` ou configure Apache/Nginx)

> ⚡ Algumas extensões do PHP podem ser necessárias (`intl`, `pdo`, `mbstring`, `openssl`, `fileinfo`, entre outras).

---

## 🛠️ Instalação

Clone o repositório:

```bash
git clone https://github.com/bernardovvieira/visita-ai.git
cd visita-ai
```

Instale as dependências do projeto:

```bash
composer install
npm install
npm run dev
```

Copie e edite o arquivo de ambiente:

```bash
cp .env.example .env
```

Gere a chave da aplicação:

```bash
php artisan key:generate
```

Configure o banco de dados no arquivo `.env`, então crie o banco manualmente (no MySQL):

```sql
CREATE DATABASE visita_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Execute as migrações:

```bash
php artisan migrate
php artisan db:seed  # (opcional, se houver dados iniciais)
```

Inicie os servidores da aplicação:

```bash
php artisan serve
```
```bash
npm run dev
```
> Deixe ambos os comandos rodando em terminais separados.

Abra o navegador e acesse: [http://localhost:8000](http://localhost:8000)

---

## 🔁 Instalação — Todos os Comandos Resumidos

```bash
git clone https://github.com/bernardovvieira/visita-ai.git
cd visita-ai

composer install
npm install

cp .env.example .env
php artisan key:generate

# Criar banco no MySQL com:
# CREATE DATABASE visita_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

php artisan migrate
php artisan db:seed  # se necessário

php artisan serve
npm run dev
```

---

## 🫱🏽‍🫲🏼 Contribuição

Este repositório possui **licença restrita** e a colaboração é limitada.  
Caso tenha interesse em contribuir, entre em contato diretamente com o autor.

---

## 📃 Licença

Este projeto é **de uso acadêmico e restrito**.  
**Não está autorizado para distribuição, modificação ou uso comercial.**  
Todos os direitos reservados ao autor.

---

## 📱 Contato

Para mais informações:

> Bernardo Vivian Vieira  
> E-mail: 179835@upf.br  
> LinkedIn: [linkedin.com/in/bernardovivianvieira](https://www.linkedin.com/in/bernardovivianvieira)
