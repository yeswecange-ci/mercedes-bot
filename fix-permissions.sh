#!/bin/bash

echo "Fixing Laravel permissions for production..."

# Create directories if they don't exist
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set correct permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# If running as root, set proper ownership
if [ "$EUID" -eq 0 ]; then
    chown -R www-data:www-data storage
    chown -R www-data:www-data bootstrap/cache
fi

echo "Permissions fixed successfully!"
