#!/bin/bash

# Script simple para ejecutar las migraciones de multisede directamente en MySQL

echo "🏢 Ejecutando migraciones de multisede en todas las bases de datos tenant..."

# Obtener todas las bases de datos tenant
databases=$(mysql -u root -N -e "SHOW DATABASES LIKE 'tenant%';")

cd "/home/kali/Escritorio/definitivo/01_POS_BASICO (2)/backend"

for db in $databases; do
    echo ""
    echo "📦 Procesando: $db"

    # Cambiar temporalmente la base de datos por defecto
    export DB_DATABASE=$db

    # Ejecutar migraciones específicas
    php artisan migrate --database=mysql --path=database/migrations/tenant --force 2>&1 | grep -E "warehouses|stock_transfer|DONE|FAIL" || true

    echo "✅ $db completado"
done

echo ""
echo "✅ Migraciones completadas en todos los tenants"
