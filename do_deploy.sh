#!/bin/bash
set -e

cp /mnt/c/Users/Admin/.ssh/id_rsa /tmp/deploy_key
chmod 600 /tmp/deploy_key
SSH_KEY=/tmp/deploy_key
VPS=root@72.61.73.245
RSYNC_SSH="ssh -i $SSH_KEY -o StrictHostKeyChecking=no -o BatchMode=yes"
PROJ='/mnt/c/Users/Admin/Documents/105 pos/Pos_sistema'

echo "📤 Subiendo frontend (solo cambios)..."
rsync -az --checksum -e "$RSYNC_SSH" "$PROJ/backend/public/" $VPS:/var/www/105pos/backend/public/
rsync -az --checksum -e "$RSYNC_SSH" "$PROJ/dist/" $VPS:/var/www/105pos/dist/

echo "📄 Subiendo backend (solo cambios)..."
rsync -az --checksum \
  --exclude 'node_modules' --exclude 'vendor' --exclude 'dist' \
  --exclude '.git' --exclude 'storage' --exclude '.env' \
  -e "$RSYNC_SSH" \
  "$PROJ/backend/" $VPS:/var/www/105pos/backend/

echo "⚙️  Limpiando cache en VPS..."
ssh -i "$SSH_KEY" -o StrictHostKeyChecking=no $VPS \
  "cd /var/www/105pos/backend && \
   php artisan config:clear && php artisan cache:clear && \
   php artisan route:clear && php artisan view:clear && \
   php artisan config:cache && php artisan route:cache && \
   sudo systemctl restart php8.3-fpm && echo 'CACHE_OK'"

rm /tmp/deploy_key
echo "✅ Deploy completado!"
echo "🌐 https://maria.105pos.pro  (Ctrl+Shift+R para limpiar cache)"
