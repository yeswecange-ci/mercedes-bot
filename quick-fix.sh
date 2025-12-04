#!/bin/bash

echo "🔧 Quick Fix for Vite Assets Not Loading"
echo ""

# 1. Verify APP_ENV
echo "1. Checking APP_ENV..."
if grep -q "APP_ENV=production" .env 2>/dev/null; then
    echo "   ✓ APP_ENV is set to production"
else
    echo "   ⚠️  APP_ENV is NOT production"
    echo "   Add this to your Coolify Environment Variables:"
    echo "   APP_ENV=production"
fi
echo ""

# 2. Verify manifest exists
echo "2. Checking manifest..."
if [ -f "public/build/manifest.json" ]; then
    echo "   ✓ Manifest exists"
    cat public/build/manifest.json
else
    echo "   ✗ Manifest NOT found"
fi
echo ""

# 3. Clear all caches
echo "3. Clearing all Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo "   ✓ Caches cleared"
echo ""

# 4. Rebuild cache
echo "4. Rebuilding cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "   ✓ Cache rebuilt"
echo ""

# 5. Check permissions
echo "5. Checking permissions..."
chmod -R 755 public/build
echo "   ✓ Permissions set"
echo ""

# 6. Run PHP diagnostic
echo "6. Running PHP diagnostic..."
php check-vite.php
echo ""

echo "✨ Quick fix complete!"
echo ""
echo "Now:"
echo "1. Check the output above for any ✗ marks"
echo "2. If APP_ENV is not production, set it in Coolify"
echo "3. Reload your browser with Ctrl+Shift+R (hard refresh)"
