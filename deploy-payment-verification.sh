#!/bin/bash

# Script de deployment para sistema de verificación seguro de pagos
# VPS: 72.61.73.245

echo "🚀 Iniciando deployment al VPS..."

# Variables
VPS="root@72.61.73.245"
BACKEND_PATH="/var/www/105pos/backend"
LOCAL_BACKEND="/home/kali/Escritorio/definitivo/01_POS_BASICO (2)/backend"

# 1. Copiar controlador actualizado
echo "📦 Copiando EPaycoPaymentController..."
scp "$LOCAL_BACKEND/app/Http/Controllers/EPaycoPaymentController.php" \
  "$VPS:$BACKEND_PATH/app/Http/Controllers/" 2>/dev/null || \
  echo "⚠️  No se pudo copiar el controlador (verificar ruta en VPS)"

# 2. Copiar routes/api.php actualizado
echo "📦 Copiando rutas API..."
scp "$LOCAL_BACKEND/routes/api.php" \
  "$VPS:$BACKEND_PATH/routes/" 2>/dev/null || \
  echo "⚠️  No se pudo copiar api.php (verificar ruta en VPS)"

# 3. Copiar migración
echo "📦 Copiando migración de verification_token..."
scp "$LOCAL_BACKEND/database/migrations/2026_01_01_204022_add_verification_token_to_pending_payments_table.php" \
  "$VPS:$BACKEND_PATH/database/migrations/" 2>/dev/null || \
  echo "⚠️  No se pudo copiar migración (verificar ruta en VPS)"

# 4. Ejecutar migración en el VPS
echo "🔧 Ejecutando migración..."
ssh "$VPS" "cd $BACKEND_PATH && php artisan migrate --force" 2>/dev/null || \
  echo "⚠️  No se pudo ejecutar migración (verificar path de artisan)"

# 5. Limpiar cache
echo "🧹 Limpiando cache..."
ssh "$VPS" "cd $BACKEND_PATH && php artisan config:clear && php artisan route:clear && php artisan cache:clear" 2>/dev/null || \
  echo "⚠️  No se pudo limpiar cache"

# 6. Reiniciar servicios
echo "🔄 Reiniciando servicios..."
ssh "$VPS" "systemctl restart php8.2-fpm nginx" 2>/dev/null || \
ssh "$VPS" "systemctl restart php8.1-fpm nginx" 2>/dev/null || \
ssh "$VPS" "systemctl restart php-fpm nginx" 2>/dev/null || \
  echo "⚠️  No se pudo reiniciar servicios (verificar versión PHP)"

echo ""
echo "✅ Deployment completado!"
echo ""
echo "📋 Siguiente paso:"
echo "   1. Probar en localhost primero"
echo "   2. Verificar que el pago se confirme automáticamente"
echo "   3. Revisar logs: ssh $VPS 'tail -f $BACKEND_PATH/storage/logs/laravel.log'"
