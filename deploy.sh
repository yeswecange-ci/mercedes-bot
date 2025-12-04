#!/bin/bash
set -e

echo "🚀 Starting deployment process..."
echo ""

# 1. Install Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
echo "✅ Composer dependencies installed"
echo ""

# 2. Install NPM dependencies
echo "📦 Installing NPM dependencies..."
npm install
echo "✅ NPM dependencies installed"
echo ""

# 3. Build frontend assets
echo "🔨 Building frontend assets..."
npm run build
echo "✅ Frontend assets built"
echo ""

# 4. Verify build output
echo "🔍 Verifying build output..."
if [ -f "public/build/manifest.json" ]; then
    echo "✅ manifest.json found"
    cat public/build/manifest.json
else
    echo "❌ ERROR: manifest.json not found!"
    exit 1
fi
echo ""

# 5. Fix permissions
echo "🔧 Fixing permissions..."
chmod -R 775 storage bootstrap/cache public/build
echo "✅ Permissions fixed"
echo ""

# 6. Clear caches
echo "🧹 Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo "✅ Caches cleared"
echo ""

# 7. Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force --no-interaction || echo "⚠️  Migration skipped or failed"
echo ""

# 8. Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Application optimized"
echo ""

# 9. Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link || echo "⚠️  Storage link already exists"
echo ""

echo "✨ Deployment completed successfully!"
