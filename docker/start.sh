#!/bin/sh
# Don't exit on errors - we want to handle them gracefully
set +e

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

# Start supervisor in the background FIRST so port is bound immediately
# This prevents Render from timing out due to no open ports
echo "Starting supervisor (Nginx/PHP-FPM) to bind port..."
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf &
SUPERVISOR_PID=$!

# Give supervisor a moment to start
sleep 2

# Wait for database if DB_HOST is set (in background so it doesn't block)
if [ -n "$DB_HOST" ] && [ "$DB_HOST" != "null" ] && [ "$DB_HOST" != "" ]; then
    echo "Waiting for database connection..."
    echo "Database config: DB_HOST=$DB_HOST, DB_DATABASE=$DB_DATABASE, DB_USERNAME=$DB_USERNAME"
    MAX_ATTEMPTS=30
    ATTEMPT=0
    
    while [ $ATTEMPT -lt $MAX_ATTEMPTS ]; do
        if php artisan migrate:status > /dev/null 2>&1; then
            echo "Database connection successful!"
            echo "Running database migrations..."
            php artisan migrate --force || echo "Migration failed, continuing anyway..."
            break
        fi
        ATTEMPT=$((ATTEMPT + 1))
        if [ $ATTEMPT -ge $MAX_ATTEMPTS ]; then
            echo "WARNING: Database connection failed after $MAX_ATTEMPTS attempts (60 seconds)."
            echo "Continuing without database connection. Migrations will be skipped."
            echo "Please verify your database configuration in Render dashboard."
            break
        fi
        echo "Database not ready, waiting... (attempt $ATTEMPT/$MAX_ATTEMPTS)"
        sleep 2
    done
else
    echo "No database host configured (DB_HOST not set), skipping database operations"
fi

# Keep the script running and wait for supervisor
echo "Application is ready. Supervisor PID: $SUPERVISOR_PID"
wait $SUPERVISOR_PID

