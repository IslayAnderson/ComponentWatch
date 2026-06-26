#!/usr/bin/env bash
set -e

cd "$(dirname "$0")"

PHP=/usr/bin/php8.1
$PHP /usr/local/bin/composer install --no-dev --optimize-autoloader

# Dashboard runs migrations for the shared DB — skip here unless deploying API first
# $PHP artisan migrate --force

$PHP artisan config:cache
$PHP artisan route:cache

chmod -R 775 storage bootstrap/cache
