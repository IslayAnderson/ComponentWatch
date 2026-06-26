#!/usr/bin/env bash
set -e

cd "$(dirname "$0")"

PHP=/usr/bin/php8.1
$PHP /usr/local/bin/composer install --no-dev --optimize-autoloader

export PATH="$HOME/.nodenv/shims:$HOME/.nodenv/bin:$PATH"
npm ci
npm run build

$PHP artisan migrate --force
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache

chmod -R 775 storage bootstrap/cache
