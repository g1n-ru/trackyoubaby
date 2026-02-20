#!/bin/sh
set -e

php artisan optimize
php artisan migrate --force
php artisan storage:link 2>/dev/null || true

exec "$@"
