# ---- Stage 1: build frontend assets (evita instalar Node no container PHP) ----
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
RUN mkdir -p public && npm run build

# ---- Stage 2: app PHP ----
FROM php:8.3-fpm

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

COPY . .

# Trazer assets já compilados do stage frontend (não precisa de npm no container PHP)
COPY --from=frontend /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]

EXPOSE 9000
