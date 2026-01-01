#!/bin/sh
set -e

echo "Starting Laravel application..."

# Clear all caches to ensure environment variables are picked up
echo "Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Wait for database if DB_HOST is set
if [ -n "$DB_HOST" ] && [ "$DB_HOST" != "null" ]; then
    echo "Waiting for database connection..."
    until php artisan migrate:status > /dev/null 2>&1; do
        echo "Database not ready, waiting..."
        sleep 2
    done
    
    echo "Running database migrations..."
    php artisan migrate --force
fi

echo "Starting supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

