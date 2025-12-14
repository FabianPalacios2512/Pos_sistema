#!/bin/bash

# =============================================================================
# 🚀 Script de Despliegue Automatizado - 105 POS Pro
# =============================================================================
# VPS: 72.61.73.245
# Path: /var/www/105pos
# 
# ⚠️ PRECAUCIÓN: Este script NO tocará los servicios existentes:
#    - /var/www/paginaPrincipal
#    - /var/www/pos
#    - /var/www/reservas
# =============================================================================

set -e  # Salir si hay algún error

VPS_HOST="root@72.61.73.245"
VPS_PATH="/var/www/105pos"
LOCAL_PATH="/home/kali/Escritorio/definitivo/01_POS_BASICO (2)"

echo "🚀 Iniciando despliegue de 105 POS Pro"
echo "========================================"
echo ""

# -----------------------------------------------------------------------------
# 1. Verificar conexión SSH
# -----------------------------------------------------------------------------
echo "📡 [1/10] Verificando conexión SSH..."
if ssh -o ConnectTimeout=5 $VPS_HOST "echo 'Conexión exitosa'" > /dev/null 2>&1; then
    echo "✅ Conexión SSH OK"
else
    echo "❌ Error: No se puede conectar al VPS"
    exit 1
fi

# -----------------------------------------------------------------------------
# 2. Verificar servicios existentes (NO TOCAR)
# -----------------------------------------------------------------------------
echo ""
echo "🔍 [2/10] Verificando servicios existentes..."
ssh $VPS_HOST "ls -d /var/www/{paginaPrincipal,pos,reservas} 2>/dev/null" && echo "✅ Servicios existentes detectados (NO se tocarán)"

# -----------------------------------------------------------------------------
# 3. Instalar dependencias del sistema
# -----------------------------------------------------------------------------
echo ""
echo "📦 [3/10] Instalando dependencias del sistema..."
ssh $VPS_HOST << 'ENDSSH'
export DEBIAN_FRONTEND=noninteractive
apt-get update -qq
apt-get install -y -qq \
    php8.3 \
    php8.3-fpm \
    php8.3-mysql \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-curl \
    php8.3-zip \
    php8.3-gd \
    php8.3-bcmath \
    mysql-server \
    composer \
    nodejs \
    npm \
    git \
    unzip \
    curl > /dev/null 2>&1
echo "✅ Dependencias instaladas"
ENDSSH

# -----------------------------------------------------------------------------
# 4. Crear estructura de directorios
# -----------------------------------------------------------------------------
echo ""
echo "📁 [4/10] Creando estructura de directorios..."
ssh $VPS_HOST << 'ENDSSH'
mkdir -p /var/www/105pos/{backend,frontend}
chown -R www-data:www-data /var/www/105pos
echo "✅ Directorios creados"
ENDSSH

# -----------------------------------------------------------------------------
# 5. Subir código backend
# -----------------------------------------------------------------------------
echo ""
echo "📤 [5/10] Subiendo código backend..."
rsync -avz --progress \
    --exclude 'node_modules' \
    --exclude 'vendor' \
    --exclude 'storage/logs/*' \
    --exclude '.env' \
    $LOCAL_PATH/backend/ $VPS_HOST:$VPS_PATH/backend/

echo "✅ Backend subido"

# -----------------------------------------------------------------------------
# 6. Subir código frontend
# -----------------------------------------------------------------------------
echo ""
echo "📤 [6/10] Subiendo código frontend..."
rsync -avz --progress \
    --exclude 'node_modules' \
    --exclude 'dist' \
    --exclude 'dev-dist' \
    $LOCAL_PATH/src/ $VPS_HOST:$VPS_PATH/frontend/src/
    
rsync -avz --progress \
    $LOCAL_PATH/{package.json,vite.config.js,tailwind.config.js,postcss.config.js,index.html} \
    $VPS_HOST:$VPS_PATH/frontend/

echo "✅ Frontend subido"

# -----------------------------------------------------------------------------
# 7. Configurar Backend (Laravel)
# -----------------------------------------------------------------------------
echo ""
echo "⚙️  [7/10] Configurando backend Laravel..."
ssh $VPS_HOST << 'ENDSSH'
cd /var/www/105pos/backend

# Instalar dependencias de Composer
composer install --no-dev --optimize-autoloader -q

# Crear archivo .env
if [ ! -f .env ]; then
    cp .env.example .env 2>/dev/null || cat > .env << 'EOF'
APP_NAME="105 POS Pro"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://72.61.73.245

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_105
DB_USERNAME=pos_user
DB_PASSWORD=POS_Secure_Pass_2024!

LOG_CHANNEL=stack
LOG_LEVEL=error
EOF
fi

# Generar key si no existe
php artisan key:generate --force

# Configurar permisos
chown -R www-data:www-data /var/www/105pos/backend
chmod -R 755 /var/www/105pos/backend
chmod -R 775 /var/www/105pos/backend/storage
chmod -R 775 /var/www/105pos/backend/bootstrap/cache

echo "✅ Backend configurado"
ENDSSH

# -----------------------------------------------------------------------------
# 8. Configurar Base de Datos
# -----------------------------------------------------------------------------
echo ""
echo "🗄️  [8/10] Configurando base de datos..."
ssh $VPS_HOST << 'ENDSSH'
# Iniciar MySQL si no está corriendo
systemctl start mysql
systemctl enable mysql

# Crear base de datos y usuario
mysql -e "CREATE DATABASE IF NOT EXISTS pos_105 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS 'pos_user'@'localhost' IDENTIFIED BY 'POS_Secure_Pass_2024!';"
mysql -e "GRANT ALL PRIVILEGES ON pos_105.* TO 'pos_user'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

echo "✅ Base de datos configurada"
ENDSSH

# -----------------------------------------------------------------------------
# 9. Ejecutar migraciones
# -----------------------------------------------------------------------------
echo ""
echo "🔄 [9/10] Ejecutando migraciones..."
ssh $VPS_HOST << 'ENDSSH'
cd /var/www/105pos/backend
php artisan migrate --force
echo "✅ Migraciones ejecutadas"
ENDSSH

# -----------------------------------------------------------------------------
# 10. Construir Frontend
# -----------------------------------------------------------------------------
echo ""
echo "🏗️  [10/10] Construyendo frontend..."
ssh $VPS_HOST << 'ENDSSH'
cd /var/www/105pos/frontend
npm install --production
npm run build
echo "✅ Frontend construido"
ENDSSH

# -----------------------------------------------------------------------------
# 11. Configurar Nginx
# -----------------------------------------------------------------------------
echo ""
echo "🌐 Configurando Nginx..."
ssh $VPS_HOST << 'ENDSSH'
cat > /etc/nginx/sites-available/105pos << 'NGINX_CONF'
server {
    listen 80;
    server_name 72.61.73.245;
    root /var/www/105pos/frontend/dist;
    index index.html;

    # Frontend
    location / {
        try_files $uri $uri/ /index.html;
    }

    # API Backend
    location /api {
        alias /var/www/105pos/backend/public;
        try_files $uri $uri/ @backend;

        location ~ \.php$ {
            include fastcgi_params;
            fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
            fastcgi_param SCRIPT_FILENAME /var/www/105pos/backend/public/index.php;
        }
    }

    location @backend {
        rewrite /api/(.*)$ /index.php?/$1 last;
    }

    # Logs
    access_log /var/log/nginx/105pos_access.log;
    error_log /var/log/nginx/105pos_error.log;
}
NGINX_CONF

# Habilitar sitio
ln -sf /etc/nginx/sites-available/105pos /etc/nginx/sites-enabled/105pos

# Verificar configuración
nginx -t

# Recargar Nginx
systemctl reload nginx

echo "✅ Nginx configurado"
ENDSSH

# -----------------------------------------------------------------------------
# FINALIZADO
# -----------------------------------------------------------------------------
echo ""
echo "=========================================="
echo "✅ ¡Despliegue completado exitosamente!"
echo "=========================================="
echo ""
echo "📍 URL del sistema: http://72.61.73.245"
echo ""
echo "🔐 Acceso a Base de Datos:"
echo "   Host: localhost"
echo "   DB: pos_105"
echo "   User: pos_user"
echo "   Pass: POS_Secure_Pass_2024!"
echo ""
echo "📊 Verificar logs:"
echo "   ssh $VPS_HOST 'tail -f /var/log/nginx/105pos_error.log'"
echo ""
