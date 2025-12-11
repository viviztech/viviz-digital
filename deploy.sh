#!/bin/bash

# Deploy Script for AuraAssets

echo "🚀 Starting Deployment..."

# 1. Pull latest changes
# git pull origin main

# 2. Install PHP Dependencies (Optimize autoloader)
echo "📦 Installing PHP Dependencies..."
composer install --no-dev --optimize-autoloader

# 3. Install NPM Dependencies & Build Assets
echo "🎨 Building Frontend Assets..."
npm install
npm run build

# 4. Run Migrations (Force for production)
echo "🗄️ Running Migrations..."
php artisan migrate --force

# 5. Clear & Cache Config/Routes/Views
echo "🧹 Optimizing Caches..."
php artisan optimize:clear
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# 6. Restart Queue Worker (if using Supervisor)
# echo "🔄 Restarting Queue..."
# php artisan queue:restart

echo "✅ Deployment Complete! AuraAssets is live."
