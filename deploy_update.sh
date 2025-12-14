#!/bin/bash

# =============================================================================
# 🔄 Script de Actualización Incremental - 105 POS Pro
# =============================================================================
# Solo sube cambios nuevos sin reinstalar dependencias
# Ideal para updates rápidos de código
# =============================================================================

set -e

VPS_HOST="root@72.61.73.245"
VPS_PATH="/var/www/105pos"
LOCAL_PATH="."

echo "🔄 Actualización incremental de 105 POS Pro"
echo "============================================"
echo ""

# -----------------------------------------------------------------------------
# 1. Verificar conexión
# -----------------------------------------------------------------------------
echo "📡 [1/5] Verificando conexión SSH..."
if ssh -o ConnectTimeout=5 $VPS_HOST "echo 'OK'" > /dev/null 2>&1; then
    echo "✅ Conexión SSH OK"
else
    echo "❌ Error: No se puede conectar al VPS"
    exit 1
fi

# -----------------------------------------------------------------------------
# 2. Backup rápido (solo .env y base de datos)
# -----------------------------------------------------------------------------
echo ""
echo "💾 [2/5] Creando backup de seguridad..."
ssh $VPS_HOST << 'ENDSSH'
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
mkdir -p /root/backups

# Backup .env
cp /var/www/105pos/backend/.env /root/backups/.env_$TIMESTAMP 2>/dev/null || true

echo "✅ Backup creado: /root/backups/"
ENDSSH

# -----------------------------------------------------------------------------
# 3. Sincronizar código frontend (solo cambios)
# -----------------------------------------------------------------------------
echo ""
echo "📤 [3/5] Sincronizando código frontend..."

# Crear directorios si no existen
ssh $VPS_HOST "mkdir -p $VPS_PATH/frontend/src"

rsync -avz --progress \
    --exclude 'node_modules' \
    --exclude 'dist' \
    --exclude '.git' \
    $LOCAL_PATH/src/ $VPS_HOST:$VPS_PATH/frontend/src/

rsync -avz --progress \
    $LOCAL_PATH/index.html \
    $LOCAL_PATH/package.json \
    $LOCAL_PATH/vite.config.js \
    $LOCAL_PATH/tailwind.config.js \
    $VPS_HOST:$VPS_PATH/frontend/

echo "✅ Frontend actualizado"

# -----------------------------------------------------------------------------
# 4. Sincronizar código backend (solo cambios)
# -----------------------------------------------------------------------------
echo ""
echo "📤 [4/5] Sincronizando código backend..."
rsync -avz --progress \
    --exclude 'vendor' \
    --exclude 'node_modules' \
    --exclude 'storage/logs/*' \
    --exclude '.env' \
    --exclude 'bootstrap/cache/*' \
    $LOCAL_PATH/backend/ $VPS_HOST:$VPS_PATH/backend/

echo "✅ Backend actualizado"

# -----------------------------------------------------------------------------
# 5. Rebuild y restart servicios
# -----------------------------------------------------------------------------
echo ""
echo "🔨 [5/5] Rebuilding frontend y reiniciando servicios..."
ssh $VPS_HOST << 'ENDSSH'
cd /var/www/105pos/frontend

# Rebuild frontend (usa node_modules existentes)
echo "📦 Instalando dependencias nuevas (si hay)..."
npm install --silent

echo "🏗️ Construyendo frontend..."
npm run build

# Copiar build a public del backend
echo "📋 Copiando build a backend..."
rm -rf /var/www/105pos/backend/public/build
cp -r dist/* /var/www/105pos/backend/public/

# Optimizar Laravel
cd /var/www/105pos/backend
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Permisos
chown -R www-data:www-data /var/www/105pos
chmod -R 755 /var/www/105pos/backend/storage
chmod -R 755 /var/www/105pos/backend/bootstrap/cache

echo "✅ Servicios actualizados"
ENDSSH

echo ""
echo "✅ ACTUALIZACIÓN COMPLETADA!"
echo "🌍 Sitio actualizado: http://72.61.73.245"
echo ""
echo "💡 Tip: Abre el navegador en modo incógnito para ver cambios sin cache"
