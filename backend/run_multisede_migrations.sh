#!/bin/bash

# Script para ejecutar solo las migraciones de multisede en todos los tenants
echo "🏢 Ejecutando migraciones de multisede..."

cd "/home/kali/Escritorio/definitivo/01_POS_BASICO (2)/backend"

# Array de migraciones en orden
migrations=(
    "2025_11_30_000001_create_warehouses_table.php"
    "2025_11_30_000002_create_product_warehouse_table.php"
    "2025_11_30_000003_create_stock_transfers_table.php"
    "2025_11_30_000004_create_stock_transfer_items_table.php"
    "2025_11_30_000005_add_warehouse_id_to_cash_sessions_table.php"
    "2025_11_30_000006_add_warehouse_id_to_inventory_movements_table.php"
    "2025_11_30_000007_migrate_existing_stock_to_warehouses.php"
)

for migration in "${migrations[@]}"
do
    echo ""
    echo "📦 Ejecutando: $migration"
    php artisan tenants:run "migrate --path=database/migrations/tenant/$migration --force"

    if [ $? -ne 0 ]; then
        echo "❌ Error en $migration"
        exit 1
    fi
done

echo ""
echo "✅ Todas las migraciones de multisede ejecutadas exitosamente"
