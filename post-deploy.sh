#!/bin/bash
set -e

echo "Running post-deployment tasks..."

# 1. Fix permissions
echo "1. Fixing permissions..."
./fix-permissions.sh

# 2. Clear all caches
echo "2. Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 3. Run migrations (safely)
echo "3. Running migrations..."
php artisan migrate --force --no-interaction || echo "Migration skipped or failed"

# 4. Optimize for production
echo "4. Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Create storage link
echo "5. Creating storage link..."
php artisan storage:link || echo "Storage link already exists"

echo "Post-deployment tasks completed successfully!"
