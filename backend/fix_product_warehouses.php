<?php

/**
 * Script de reparación crítica: Restaurar relaciones product_warehouse
 *
 * PROBLEMA: Cuando se edita un producto, sync() elimina las relaciones si warehouse_stocks está vacío.
 * SOLUCIÓN: Crear relaciones para todos los productos con la warehouse predeterminada.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

echo "🔧 REPARACIÓN CRÍTICA: Restaurando relaciones product_warehouse\n";
echo "=================================================================\n\n";

// Obtener todos los tenants
$tenants = DB::connection('mysql')->table('tenants')->get();

foreach ($tenants as $tenant) {
    echo "🏢 Procesando tenant: {$tenant->id} ({$tenant->business_name})\n";

    // Extraer el nombre de la base de datos del JSON data
    $tenantData = json_decode($tenant->data, true);
    $dbName = $tenantData['tenancy_db_name'] ?? "tenant{$tenant->id}";

    echo "   📊 Base de datos: {$dbName}\n";

    // Verificar si la base de datos existe
    $databases = DB::select("SHOW DATABASES LIKE '{$dbName}'");
    if (empty($databases)) {
        echo "   ⚠️  Base de datos no existe, saltando...\n\n";
        continue;
    }

    // Cambiar a la base de datos del tenant
    try {
        tenancy()->initialize($tenant->id);
    } catch (\Exception $e) {
        echo "   ❌ Error inicializando tenant: " . $e->getMessage() . "\n\n";
        continue;
    }

    try {
        // Obtener la bodega predeterminada o la primera disponible
        $warehouse = Warehouse::where('is_default', true)->first();
        if (!$warehouse) {
            $warehouse = Warehouse::first();
        }

        if (!$warehouse) {
            echo "   ⚠️  No hay warehouses disponibles, saltando...\n\n";
            continue;
        }

        echo "   🏪 Warehouse predeterminada: {$warehouse->name} (ID: {$warehouse->id})\n";

        // Obtener productos SIN relaciones en product_warehouse
        $productsWithoutWarehouses = Product::whereDoesntHave('warehouses')->get();

        echo "   📦 Productos sin warehouse: " . $productsWithoutWarehouses->count() . "\n";

        foreach ($productsWithoutWarehouses as $product) {
            // Crear la relación con el stock actual del producto
            $product->warehouses()->attach($warehouse->id, [
                'stock' => $product->current_stock ?? 0
            ]);

            echo "   ✅ {$product->name} - stock: {$product->current_stock}\n";
        }

        // BONUS: Verificar productos CON relaciones pero con stock desincronizado
        $productsWithWarehouses = Product::has('warehouses')->get();
        echo "   🔍 Verificando {$productsWithWarehouses->count()} productos con warehouses...\n";

        foreach ($productsWithWarehouses as $product) {
            $totalStock = $product->warehouses()->sum('product_warehouse.stock');

            if ($totalStock != $product->current_stock) {
                echo "   ⚠️  {$product->name}: current_stock={$product->current_stock}, total_warehouses={$totalStock}\n";

                // Actualizar current_stock con el total de las warehouses
                $product->update(['current_stock' => $totalStock]);
                echo "   ✅ Actualizado a {$totalStock}\n";
            }
        }

    } catch (\Exception $e) {
        echo "   ❌ Error procesando tenant: " . $e->getMessage() . "\n";
    }

    echo "\n";
}

echo "✅ REPARACIÓN COMPLETADA\n";
