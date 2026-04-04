#!/bin/bash

# 🚀 Deploy Manual 105 POS Pro — Rápido (solo envía cambios)
set -e

SSH_KEY="$HOME/.ssh/id_rsa"
VPS="root@72.61.73.245"
RSYNC_SSH="ssh -i $SSH_KEY -o StrictHostKeyChecking=no -o BatchMode=yes"

echo "🎨 Compilando frontend..."
npm run build 2>&1 | tail -5

echo ""
echo "📦 Copiando dist → backend/public..."
cp -r dist/* backend/public/

echo ""
echo "📤 Subiendo frontend (solo cambios)..."
rsync -az --checksum -e "$RSYNC_SSH" \
  backend/public/ $VPS:/var/www/105pos/backend/public/
rsync -az --checksum -e "$RSYNC_SSH" \
  dist/ $VPS:/var/www/105pos/dist/

echo ""
echo "🔄 Subiendo backend (solo cambios)..."
rsync -az --checksum \
  --exclude 'node_modules' --exclude 'vendor' --exclude 'dist' \
  --exclude '.git' --exclude 'storage' --exclude '.env' \
  -e "$RSYNC_SSH" \
  backend/ $VPS:/var/www/105pos/backend/

echo ""
echo "⚙️  Limpiando cache en VPS..."
ssh -i "$SSH_KEY" -o StrictHostKeyChecking=no $VPS \
  "cd /var/www/105pos/backend && \
   php artisan config:clear && php artisan cache:clear && \
   php artisan route:clear && php artisan view:clear && \
   php artisan config:cache && php artisan route:cache && \
   sudo systemctl restart php8.3-fpm && echo 'CACHE OK'"

echo ""
echo "✅ Deploy completado!"
echo "🌐 https://maria.105pos.pro/pos  (Ctrl+Shift+R para limpiar cache)"
echo ""
echo "💡 Recuerda hacer Ctrl+Shift+R para limpiar el cache del navegador"
