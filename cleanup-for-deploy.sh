#!/bin/bash

# 🧹 Script de Limpieza para Deploy - POS Sistema
# Este script elimina archivos de debug y test antes del deploy

echo "🚀 Iniciando limpieza para deploy..."

# Directorio base del proyecto
PROJECT_DIR="/home/kali/Escritorio/definitivo/01_POS_BASICO (2)"
cd "$PROJECT_DIR"

echo "📍 Directorio: $PROJECT_DIR"

# Función para eliminar archivos si existen
remove_if_exists() {
    if [ -f "$1" ]; then
        rm -f "$1"
        echo "✅ Eliminado: $1"
    else
        echo "ℹ️  No existe: $1"
    fi
}

# Función para eliminar directorios si existen
remove_dir_if_exists() {
    if [ -d "$1" ]; then
        rm -rf "$1"
        echo "✅ Eliminado directorio: $1"
    else
        echo "ℹ️  No existe directorio: $1"
    fi
}

echo ""
echo "🗄️ Limpiando archivos de debug..."

# Archivos de debug en el root
remove_if_exists "debug_permissions.html"
remove_if_exists "simple_api_test.html"
remove_if_exists "test_frontend_hourly.html"
remove_if_exists "test_stock_debug.html"
remove_if_exists "test_stock_critical.html"

# Archivos PHP de debug en backend
remove_if_exists "backend/analyze_discounts.php"
remove_if_exists "backend/analyze_inventory_values.php"
remove_if_exists "backend/check_customers.php"
remove_if_exists "backend/check_products_structure.php"
remove_if_exists "backend/debug_timezone.php"
remove_if_exists "backend/investigate_sofia.php"
remove_if_exists "backend/populate_new_tables.php"

# Archivos temporales y de sesión
remove_if_exists "backend/whatsapp_qr.txt"
remove_if_exists "vite.pid"
remove_if_exists "whatsapp.pid"

# Directorio de sesiones WhatsApp (puede contener datos sensibles)
remove_dir_if_exists "backend/whatsapp_session"

echo ""
echo "🧹 Limpiando archivos temporales..."

# Node modules y archivos de build (se regeneran)
remove_dir_if_exists "node_modules"
remove_dir_if_exists "dist"
remove_if_exists "package-lock.json"

# Archivos de logs y cache
remove_dir_if_exists "backend/storage/logs"
remove_dir_if_exists "backend/storage/framework/cache"
remove_dir_if_exists "backend/storage/framework/sessions"
remove_dir_if_exists "backend/storage/framework/views"

echo ""
echo "🔒 Limpiando archivos sensibles..."

# Archivos que pueden contener información sensible
remove_if_exists ".env.local"
remove_if_exists ".env.development"
remove_if_exists "backend/.env.backup"

echo ""
echo "📝 Limpiando documentación de desarrollo..."

# Archivos de documentación que no son necesarios en producción
remove_if_exists "DEMO.md"
remove_if_exists "DESIGN_GUIDE.md"
remove_if_exists "DESIGN_SYSTEM.md"
remove_if_exists "OPTIMIZACION_PERFORMANCE_COMPLETADA.md"
remove_if_exists "PERMISSIONS_STRUCTURE.md"
remove_if_exists "PERSONALIZACION.md"
remove_if_exists "README_SISTEMA.md"
remove_if_exists "REPORTES_CAJA_IMPLEMENTADO.md"
remove_if_exists "TABLAS_DOCUMENTACION.md"
remove_if_exists "WHATSAPP_INVOICE_FIXES.md"

echo ""
echo "🎯 Creando archivos necesarios para producción..."

# Crear .env.example si no existe (para referencia)
if [ ! -f "backend/.env.example" ]; then
    cat > "backend/.env.example" << 'EOF'
APP_NAME="POS Sistema"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_sistema
DB_USERNAME=root
DB_PASSWORD=

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
MAIL_FROM_NAME="${APP_NAME}"

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

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
EOF
    echo "✅ Creado: backend/.env.example"
fi

# Crear README.md para producción
cat > "README.md" << 'EOF'
# 105 POS Pro - Sistema de Punto de Venta

Sistema integral de punto de venta desarrollado con Laravel + Vue.js.

## 🚀 Características

- ✅ Sistema POS completo
- ✅ Gestión de inventario inteligente
- ✅ Reportes en tiempo real
- ✅ Panel de administración
- ✅ Sistema de caja
- ✅ Facturación electrónica
- ✅ Integración WhatsApp
- ✅ Multi-usuario con roles

## 📋 Requisitos del Sistema

- PHP 8.1+
- MySQL 8.0+
- Node.js 18+
- Composer
- Extensiones PHP: mysql, gd, curl, openssl, mbstring

## 🛠️ Instalación

### Backend (Laravel)
```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

### Frontend (Vue.js)
```bash
npm install
npm run build
```

## 🔧 Configuración

1. Configurar base de datos en `.env`
2. Ejecutar migraciones
3. Crear usuario administrador
4. Configurar permisos de archivos

## 📞 Soporte

Sistema desarrollado para operaciones comerciales profesionales.

## 📄 Licencia

Propietario - Todos los derechos reservados
EOF

echo "✅ Creado: README.md para producción"

echo ""
echo "🔍 Verificando estructura final..."

# Mostrar estructura limpia
echo "📁 Estructura después de limpieza:"
find . -maxdepth 2 -type f -name "*.html" -o -name "*.php" -name "*debug*" -o -name "*test*" | head -10

echo ""
echo "✅ ¡Limpieza completada!"
echo "🎯 El proyecto está listo para deploy"
echo ""
echo "📋 Próximos pasos recomendados:"
echo "   1. Revisar configuración de .env para producción"
echo "   2. Ejecutar npm run build"
echo "   3. Configurar servidor web (Apache/Nginx)"
echo "   4. Configurar SSL/HTTPS"
echo "   5. Configurar backups automáticos"
echo ""
echo "🚀 ¡Deploy Ready!"