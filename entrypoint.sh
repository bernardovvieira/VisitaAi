#!/bin/sh
set -e
# Garante que storage e bootstrap/cache existam e sejam graváveis pelo PHP (www-data)
mkdir -p /var/www/html/storage/framework/{sessions,views,cache} \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
# Sem cache de rotas/config: controller e config resolvidos pelo container a cada request (binding ConfirmPassword)
php artisan route:clear 2>/dev/null || true
php artisan config:clear 2>/dev/null || true
# Migrations ao subir o container (evita post-deploy separado que pode dar timeout no Coolify)
php artisan migrate --force --no-interaction 2>/dev/null || true
exec "$@"
