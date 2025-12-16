#!/bin/bash

# Script para corregir las rutas de web-catalog en producción
# Ejecutar en el VPS dentro del directorio /var/www/105pos.pro/backend

echo "🔧 Reparando rutas de Web Catalog..."
echo ""

# 1. Limpiar cache de rutas
echo "1️⃣ Limpiando cache de rutas..."
php artisan route:clear

# 2. Limpiar cache de configuración
echo "2️⃣ Limpiando cache de configuración..."
php artisan config:clear

# 3. Limpiar cache de aplicación
echo "3️⃣ Limpiando cache de aplicación..."
php artisan cache:clear

# 4. Limpiar vistas compiladas
echo "4️⃣ Limpiando vistas compiladas..."
php artisan view:clear

# 5. Recargar configuración optimizada
echo "5️⃣ Optimizando configuración..."
php artisan config:cache

# 6. Recargar rutas optimizadas
echo "6️⃣ Optimizando rutas..."
php artisan route:cache

# 7. Verificar rutas de web-catalog
echo ""
echo "7️⃣ Verificando rutas de web-catalog:"
php artisan route:list --path=web-catalog

echo ""
echo "✅ Proceso completado."
echo ""
echo "📌 Si aún ves el error 404, verifica:"
echo "   - Que el archivo routes/tenant_api.php esté actualizado en el servidor"
echo "   - Que el controlador WebCatalogConfigController esté presente"
echo "   - Que el middleware de tenancy esté funcionando correctamente"
echo "   - Reinicia PHP-FPM: sudo systemctl restart php8.2-fpm"
