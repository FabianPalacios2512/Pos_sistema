#!/bin/bash

# Script para implementar el sistema multisede
# Fecha: 30 de noviembre de 2025

echo "🏢 Sistema Multisede - Implementación"
echo "====================================="
echo ""

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    echo "❌ Error: Este script debe ejecutarse desde el directorio backend/"
    exit 1
fi

echo "📋 Verificando migraciones existentes..."
php artisan migrate:status

echo ""
echo "⚠️  IMPORTANTE: Este proceso va a:"
echo "   1. Crear tablas nuevas (warehouses, product_warehouse, stock_transfers)"
echo "   2. Agregar columnas a tablas existentes (cash_sessions, inventory_movements)"
echo "   3. Migrar el stock actual a una 'Sede Principal'"
echo ""
read -p "¿Deseas continuar? (s/n): " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Ss]$ ]]; then
    echo "❌ Implementación cancelada"
    exit 1
fi

echo ""
echo "🔄 Ejecutando migraciones..."

# Ejecutar migraciones
php artisan migrate --force

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Migraciones ejecutadas exitosamente"
    echo ""
    echo "📊 Verificando datos migrados..."
    echo ""

    # Verificar que se creó la sede principal
    php artisan tinker --execute="echo 'Sedes creadas: ' . App\Models\Warehouse::count(); echo PHP_EOL;"

    echo ""
    echo "✅ Sistema Multisede implementado correctamente"
    echo ""
    echo "🎯 Próximos pasos:"
    echo "   1. Verifica los datos en la base de datos"
    echo "   2. Prueba crear una nueva sede desde el frontend"
    echo "   3. Revisa el archivo MULTISEDE_IMPLEMENTACION.md para más detalles"
    echo ""
else
    echo ""
    echo "❌ Error al ejecutar migraciones"
    echo "   Revisa los logs en storage/logs/laravel.log"
    exit 1
fi
