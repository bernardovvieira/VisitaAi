#!/bin/sh
# Rode após git pull quando a VPS NÃO usa Docker (ex.: Hostinger).
# Com Docker/Coolify o entrypoint.sh já faz isso ao subir o container.
set -e
cd "$(dirname "$0")"
php artisan migrate --force --no-interaction
php artisan route:clear
php artisan config:clear
echo "Deploy concluído: migrate + route:clear + config:clear"
