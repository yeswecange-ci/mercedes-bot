#!/bin/bash

echo "==================================="
echo "Laravel Diagnostic Script"
echo "==================================="
echo ""

echo "1. Checking PHP version..."
php -v
echo ""

echo "2. Checking Laravel installation..."
php artisan --version
echo ""

echo "3. Checking environment configuration..."
php artisan config:show | head -20
echo ""

echo "4. Checking storage permissions..."
ls -la storage/
ls -la storage/logs/
echo ""

echo "5. Checking bootstrap/cache permissions..."
ls -la bootstrap/cache/
echo ""

echo "6. Checking if .env file exists..."
if [ -f .env ]; then
    echo ".env file exists"
    echo "APP_ENV: $(grep APP_ENV .env)"
    echo "APP_DEBUG: $(grep APP_DEBUG .env)"
    echo "APP_URL: $(grep APP_URL .env)"
    echo "DB_CONNECTION: $(grep DB_CONNECTION .env)"
else
    echo "ERROR: .env file not found!"
fi
echo ""

echo "7. Checking public/build directory..."
if [ -d public/build ]; then
    echo "public/build exists"
    ls -la public/build/
    if [ -f public/build/manifest.json ]; then
        echo "manifest.json exists:"
        cat public/build/manifest.json
    else
        echo "WARNING: manifest.json not found!"
    fi
else
    echo "ERROR: public/build directory not found!"
fi
echo ""

echo "8. Checking recent Laravel logs..."
if [ -f storage/logs/laravel.log ]; then
    echo "Last 50 lines of laravel.log:"
    tail -50 storage/logs/laravel.log
else
    echo "No laravel.log file found"
fi
echo ""

echo "9. Testing database connection..."
php artisan migrate:status 2>&1 || echo "Database connection failed!"
echo ""

echo "==================================="
echo "Diagnostic complete!"
echo "==================================="
