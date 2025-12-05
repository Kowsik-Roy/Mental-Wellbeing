#!/bin/bash
# Fix Laravel storage permissions

cd "$(dirname "$0")"

echo "Fixing Laravel storage permissions..."

# Make storage directories writable
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Ensure specific directories exist and are writable
mkdir -p storage/framework/views
mkdir -p storage/framework/sessions
mkdir -p storage/framework/cache
mkdir -p storage/logs

chmod -R 775 storage/framework/views
chmod -R 775 storage/framework/sessions
chmod -R 775 storage/framework/cache
chmod -R 775 storage/logs

echo "Permissions fixed!"
echo "If you still get permission errors, you may need to run:"
echo "sudo chmod -R 775 storage bootstrap/cache"

