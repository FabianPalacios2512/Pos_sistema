#!/bin/bash

# 🚀 Comandos para ejecutar en el VPS para actualizar el código
# Copiar y pegar estos comandos en la terminal del VPS

echo "📥 Actualizando código desde Git..."
cd /var/www/105pos
git fetch origin main
git reset --hard origin/main
git clean -fd

echo ""
echo "🧹 Limpiando caches de Laravel..."
cd backend
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize:clear

echo ""
echo "♻️ Reconstruyendo caches..."
php artisan config:cache
php artisan route:cache

echo ""
echo "🔍 Verificando rutas de web-catalog..."
php artisan route:list | grep web-catalog

echo ""
echo "🔄 Reiniciando PHP-FPM..."
sudo systemctl restart php8.3-fpm

echo ""
echo "✅ Actualización completada!"
echo ""
echo "🧪 Ahora prueba estas URLs:"
echo "   https://sai-a.105pos.pro/api/test (debe mostrar version 1.1.0)"
echo "   https://sai-a.105pos.pro/api/web-catalog/debug-test (debe responder JSON)"
