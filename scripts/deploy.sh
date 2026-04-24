#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

echo "🚀 Starting Deployment..."

# 1. Put into maintenance mode
echo "🚧 Maintenance mode ON..."
php artisan down || true

# 2. Pull the latest code
echo "📥 Pulling latest code from git..."
git pull origin master

# 3. Install/Update PHP dependencies
# echo "📦 Installing composer dependencies..."
# composer install --no-interaction --prefer-dist --optimize-autoloader

# 4. Migrate database
echo "🗄️ Running migrations..."
php artisan migrate --force

# 5. Clear and Cache Configuration/Routes/Views
echo "🧹 Clearing and caching..."
php artisan optimize:clear
php artisan optimize
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# 6. Build Frontend Assets
echo "🎨 Building frontend assets..."
# npm install
npm run build

# 7. Restart Queue Workers
echo "🔄 Restarting queue workers..."
php artisan queue:restart

# 8. Maintenance mode OFF
echo "✅ Maintenance mode OFF..."
php artisan up

echo "✨ Deployment Finished Successfully!"
