#!/usr/bin/env bash
set -e

cd "$(dirname "$0")"

composer install --no-dev --optimize-autoloader

npm ci
npm run build

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

chmod -R 775 storage bootstrap/cache
