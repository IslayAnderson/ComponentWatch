#!/usr/bin/env bash
set -e

cd "$(dirname "$0")"

composer install --no-dev --optimize-autoloader

# Dashboard runs migrations for the shared DB — skip here unless deploying API first
# php artisan migrate --force

php artisan config:cache
php artisan route:cache

chmod -R 775 storage bootstrap/cache
