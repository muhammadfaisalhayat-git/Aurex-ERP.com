#!/bin/bash
set -e

echo "=== Aurex ERP Startup Script ==="
echo "Environment: ${APP_ENV:-local}"

# Create required directories if missing
mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         bootstrap/cache

chmod -R 775 storage bootstrap/cache

# Create storage symlink
php artisan storage:link --force 2>/dev/null || true

# Wait for database to be ready (max 60 seconds)
echo "Waiting for database connection..."
MAX_RETRIES=30
RETRY=0
until php artisan db:show --json 2>/dev/null | grep -q "name" || [ $RETRY -eq $MAX_RETRIES ]; do
    echo "DB not ready yet, retrying in 2s... ($RETRY/$MAX_RETRIES)"
    sleep 2
    RETRY=$((RETRY+1))
done

if [ $RETRY -eq $MAX_RETRIES ]; then
    echo "WARNING: Could not verify DB connection after ${MAX_RETRIES} retries. Continuing anyway..."
fi

# Run database migrations
echo "Running migrations..."
php artisan migrate --force || echo "Migrations failed or already up to date"

# Seed database only on first deployment (if users table is empty)
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null || echo "0")
if [ "$USER_COUNT" = "0" ]; then
    echo "Seeding database with default data..."
    php artisan db:seed --force || echo "Seeding failed or already seeded"
fi

# Cache config, routes, views for performance
echo "Caching application..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "=== Starting Apache ==="
exec apache2-foreground
