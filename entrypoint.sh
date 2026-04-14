#!/bin/sh
set -e
# Garante que storage e bootstrap/cache existam e sejam graváveis pelo PHP (www-data)
mkdir -p /var/www/html/storage/framework/{sessions,views,cache} \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# .env não entra na imagem (ver .dockerignore); para stack local, cria a partir do exemplo.
if [ ! -f /var/www/html/.env ] && [ -f /var/www/html/.env.example ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi
# Sem cache de rotas/config: controller e config resolvidos pelo container a cada request (binding ConfirmPassword)
php artisan route:clear 2>/dev/null || true
php artisan config:clear 2>/dev/null || true
# Em ambientes Docker sem APP_KEY, evita 500 global por MissingAppKeyException.
if ! php artisan tinker --execute='echo config("app.key") ? "ok" : "missing";' 2>/dev/null | grep -q "ok"; then
    php artisan key:generate --force --no-interaction 2>/dev/null || true
fi
# Migrations ao subir o container (evita post-deploy separado que pode dar timeout no Coolify)
php artisan migrate --force --no-interaction 2>/dev/null || true
exec "$@"
