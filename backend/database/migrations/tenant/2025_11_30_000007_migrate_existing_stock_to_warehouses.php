<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * CRÍTICO: Esta migración mueve el stock actual de products.current_stock
     * a la nueva tabla product_warehouse para cada tenant.
     */
    public function up(): void
    {
        // 1. Crear la bodega principal por defecto
        $warehouseId = DB::table('warehouses')->insertGetId([
            'name' => 'Sede Principal',
            'address' => 'Dirección principal',
            'is_default' => true,
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Migrar el stock de todos los productos a la bodega principal
        $products = DB::table('products')->select('id', 'current_stock')->get();

        foreach ($products as $product) {
            DB::table('product_warehouse')->insert([
                'product_id' => $product->id,
                'warehouse_id' => $warehouseId,
                'stock' => $product->current_stock,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Asignar la bodega principal a todas las sesiones de caja existentes
        DB::table('cash_sessions')
            ->whereNull('warehouse_id')
            ->update(['warehouse_id' => $warehouseId]);

        // 4. Asignar la bodega principal a todos los movimientos de inventario existentes
        DB::table('inventory_movements')
            ->whereNull('warehouse_id')
            ->update(['warehouse_id' => $warehouseId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurar el stock de products desde product_warehouse de la bodega por defecto
        $defaultWarehouse = DB::table('warehouses')->where('is_default', true)->first();

        if ($defaultWarehouse) {
            $productWarehouses = DB::table('product_warehouse')
                ->where('warehouse_id', $defaultWarehouse->id)
                ->get();

            foreach ($productWarehouses as $pw) {
                DB::table('products')
                    ->where('id', $pw->product_id)
                    ->update(['current_stock' => $pw->stock]);
            }
        }

        // Limpiar las asignaciones
        DB::table('cash_sessions')->update(['warehouse_id' => null]);
        DB::table('inventory_movements')->update(['warehouse_id' => null]);
    }
};
