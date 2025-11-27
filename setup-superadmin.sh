#!/bin/bash

# ============================================
# Script de Setup - Panel de Super Admin
# ============================================

echo "🚀 Instalando Panel de Super Admin (God Mode)..."
echo ""

# Verificar que estamos en el directorio correcto
if [ ! -d "backend" ]; then
    echo "❌ Error: Este script debe ejecutarse desde la raíz del proyecto"
    exit 1
fi

echo "📦 Paso 1: Ejecutando migraciones..."
cd backend
php artisan migrate --force

if [ $? -ne 0 ]; then
    echo "❌ Error al ejecutar migraciones"
    exit 1
fi
echo "✅ Migraciones completadas"
echo ""

echo "📝 Paso 2: Verificando configuración de tenancy..."
if grep -q "central_domains" config/tenancy.php; then
    echo "✅ Configuración de tenancy OK"
else
    echo "⚠️  Advertencia: Verifica config/tenancy.php"
fi
echo ""

echo "🔧 Paso 3: Limpiando cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo "✅ Cache limpiado"
echo ""

cd ..

echo "🎨 Paso 4: Compilando assets frontend..."
if command -v npm &> /dev/null; then
    npm run build
    if [ $? -eq 0 ]; then
        echo "✅ Assets compilados"
    else
        echo "⚠️  Advertencia: Error al compilar assets, ejecuta 'npm run dev' manualmente"
    fi
else
    echo "⚠️  NPM no encontrado, ejecuta 'npm run build' manualmente"
fi
echo ""

echo "============================================"
echo "✅ INSTALACIÓN COMPLETADA"
echo "============================================"
echo ""
echo "📌 Próximos pasos:"
echo ""
echo "1. Accede al panel en: http://localhost/admin"
echo ""
echo "2. Para registrar uso de IA, agrega en tu AIController:"
echo "   use App\Models\Central\CentralAiUsageLog;"
echo "   CentralAiUsageLog::logUsage('chat', \$tokens, 'gpt-4o-mini');"
echo ""
echo "3. Revisa la documentación completa en:"
echo "   SUPER_ADMIN_PANEL_README.md"
echo ""
echo "⚠️  IMPORTANTE EN PRODUCCIÓN:"
echo "   - Implementa autenticación real en SuperAdminMiddleware.php"
echo "   - Configura dominios centrales en config/tenancy.php"
echo "   - Habilita HTTPS"
echo ""
echo "🎉 ¡Listo! El Panel de Super Admin está funcionando."
