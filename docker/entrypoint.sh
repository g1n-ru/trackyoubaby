#!/bin/sh
set -e

if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    composer install --no-dev --optimize-autoloader --no-interaction
fi

php artisan optimize
php artisan migrate --force
php artisan storage:link 2>/dev/null || true

exec "$@"
