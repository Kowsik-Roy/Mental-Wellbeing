#!/bin/sh
set -e

echo "Starting Laravel application..."

# Ensure cache and session drivers are set to file (override any cached config)
export CACHE_STORE=file
export SESSION_DRIVER=file
export QUEUE_CONNECTION=sync

# Clear config cache first to ensure environment variables are picked up
echo "Clearing configuration cache..."
php artisan config:clear || true

# Now clear other caches (config:clear must happen first)
echo "Clearing route and view caches..."
php artisan route:clear || true
php artisan view:clear || true

# Clear application cache (this might fail if DB isn't ready, but that's OK)
# We'll use file cache going forward, so this is just cleanup
echo "Clearing application cache..."
php artisan cache:clear || echo "Cache clear skipped (database may not be ready yet)"

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

