#!/bin/sh
set -e

LOCAL_CA_CERT="/var/www/html/docker/certs/proxy-root.crt"
if [ -f "$LOCAL_CA_CERT" ]; then
    install -m 0644 "$LOCAL_CA_CERT" /usr/local/share/ca-certificates/expense-claim-proxy-root.crt
    update-ca-certificates
fi

mkdir -p \
    storage/framework/cache/data \
    storage/framework/cache/laravel-excel \
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
