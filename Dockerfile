# ---- Stage 1: build frontend assets (evita instalar Node no container PHP) ----
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
RUN mkdir -p public && npm run build

# ---- Stage 2: web nginx (mesmo Dockerfile para Coolify não concatenar dois arquivos) ----
FROM nginx:latest AS web
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# ---- Stage 3: app PHP ----
FROM php:8.3-fpm AS app

# Menos pacotes e --no-install-recommends para reduzir tempo e uso de memória no build
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    curl \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libpng-dev \
    zip \
    ca-certificates \
    && docker-php-ext-install pdo_mysql mbstring zip xml intl gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/html

# Copiar só dependências primeiro para não usar cache antigo quando composer.lock mudar
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Resto do app e assets
COPY . .
COPY --from=frontend /app/public/build ./public/build

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]

EXPOSE 9000
