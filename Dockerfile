# ---- Stage 1: build frontend assets (evita instalar Node no container PHP) ----
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
RUN mkdir -p public && npm run build

# ---- Stage 2: app PHP ----
# Symfony 8.x (via Laravel 12) exige PHP ≥ 8.4; 8.3 faz composer install falhar (exit 2).
FROM php:8.4-fpm AS app

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

# Copiar só dependências primeiro (--no-scripts: artisan ainda não existe)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --optimize-autoloader

# Resto do app e assets; regenerar autoload e discovery
COPY . .
COPY --from=frontend /app/public/build ./public/build
RUN composer dump-autoload --optimize

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]

EXPOSE 9000

# ---- Stage 3: web nginx (usa public do app para servir estáticos) ----
FROM nginx:latest AS web
COPY nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/html/public /var/www/html/public
