#!/bin/sh
set -e

mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Compiled Blade files are disposable and can be left behind by root-run artisan
# commands, which prevents Apache's www-data user from updating them.
find storage/framework/views -type f ! -name ".gitignore" -delete 2>/dev/null || true

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true

exec docker-php-entrypoint "$@"
