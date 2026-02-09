FROM php:8.3-fpm

# Instalar dependências do sistema e extensões PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    zip \
    npm \
    gnupg2 \
    lsb-release \
    ca-certificates \
    && docker-php-ext-install pdo_mysql mbstring zip xml intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/html

# Copiar todo o código primeiro
COPY . .

# Agora instalar dependências PHP
RUN composer install --no-dev --optimize-autoloader

# Instalar dependências Node
RUN npm install && npm run build

EXPOSE 9000