#!/bin/bash

# Exit on error
set -e

PROJECT_PATH="/var/www/pos_system"
BACKEND_PATH="$PROJECT_PATH/backend"
DB_PASS="105codeF1001504182."

echo "🚀 Finalizing Deployment on Server..."

# 1. Build Frontend
echo "📦 Building Frontend..."
cd $PROJECT_PATH
npm install
npm run build

# 2. Configure Backend
echo "⚙️ Configuring Backend..."
cd $BACKEND_PATH

# Permissions
echo "🔒 Setting permissions..."
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/logs
mkdir -p bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Dependencies (MUST BE BEFORE ARTISAN COMMANDS)
echo "📦 Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev

# Environment File
echo "📄 Configuring .env..."
# Always overwrite .env to ensure correct config
cat > .env <<EOF
APP_NAME="POS System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://72.61.65.31

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_system
DB_USERNAME=pos_user
DB_PASSWORD=${DB_PASS}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="\${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="\${APP_NAME}"
EOF

# Generate Key (if not set or if we just overwrote it)
php artisan key:generate

# Database Migration
echo "🗄 Running Migrations..."
# Use migrate:fresh to avoid "Duplicate column" errors on re-runs and ensure clean state
php artisan migrate:fresh --seed --force

# Optimization
echo "🚀 Optimizing..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Deployment Finalized!"
