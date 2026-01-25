#!/bin/bash

# 🚀 Script de Deploy Manual para 105 POS Pro
# Usar cuando GitHub Actions falla o no se actualiza

set -e  # Exit on error

echo "🎨 Building frontend locally..."
npm run build

echo ""
echo "📦 Copying frontend to backend/public..."
cp -r dist/* backend/public/

echo ""
echo "📤 Uploading frontend to VPS (backend/public)..."
rsync -avz --progress --delete backend/public/ root@72.61.73.245:/var/www/105pos/backend/public/

echo ""
echo "📤 Uploading frontend to VPS (dist folder for subdomains)..."
rsync -avz --progress --delete dist/ root@72.61.73.245:/var/www/105pos/dist/

echo ""
echo "🔄 Uploading backend files (sin node_modules ni vendor)..."
rsync -avz --progress \
  --exclude 'node_modules' \
  --exclude 'vendor' \
  --exclude 'dist' \
  --exclude '.git' \
  --exclude 'storage' \
  --exclude '.env' \
  backend/ root@72.61.73.245:/var/www/105pos/backend/
  
echo ""
echo "🔄 Uploading src/ (Vue components)..."
rsync -avz --progress src/ root@72.61.73.245:/var/www/105pos/src/

echo ""
echo "🔄 Uploading public/ files..."
rsync -avz --progress public/ root@72.61.73.245:/var/www/105pos/public/

echo ""
echo "🔄 Uploading root config files..."
rsync -avz --progress \
  index.html \
  package.json \
  vite.config.js \
  tailwind.config.js \
  postcss.config.js \
  root@72.61.73.245:/var/www/105pos/

echo ""
echo "⚙️  Running Laravel commands on VPS..."
ssh root@72.61.73.245 "cd /var/www/105pos/backend && \
  php artisan config:clear && \
  php artisan cache:clear && \
  php artisan route:clear && \
  php artisan view:clear && \
  php artisan config:cache && \
  php artisan route:cache && \
  sudo systemctl restart php8.3-fpm"

echo ""
echo "🔗 Fixing tenant storage symlinks..."
ssh root@72.61.73.245 "cd /var/www/105pos/backend && php fix_tenant_symlinks.php"

echo ""
echo "✅ Deploy manual completado!"
echo "🌐 Verifica en: https://maria.105pos.pro/pos"
echo ""
echo "💡 Recuerda hacer Ctrl+Shift+R para limpiar el cache del navegador"
