#!/bin/bash
set -e

echo "Installing Node dependencies..."
npm install

echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "Building frontend assets..."
npm run build

echo "Clearing Laravel caches..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "Build completed successfully!"
