<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Crear tabla warehouses (bodegas/tiendas)
        if (!Schema::hasTable('warehouses')) {
            Schema::create('warehouses', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('address')->nullable();
                $table->string('phone')->nullable();
                $table->boolean('is_default')->default(false);
                $table->boolean('active')->default(true);
                $table->timestamps();

                $table->index('is_default');
                $table->index('active');
            });

            // Crear bodega principal por defecto
            DB::table('warehouses')->insert([
                'name' => 'Sede Principal',
                'is_default' => true,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 2. Crear tabla pivot product_warehouse
        if (!Schema::hasTable('product_warehouse')) {
            Schema::create('product_warehouse', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
                $table->integer('stock')->default(0);
                $table->timestamps();

                $table->unique(['product_id', 'warehouse_id']);
                $table->index('warehouse_id');
                $table->index('stock');
            });

            // Migrar stock existente de products a product_warehouse
            $defaultWarehouse = DB::table('warehouses')->where('is_default', true)->first();

            if ($defaultWarehouse) {
                $products = DB::table('products')->select('id', 'stock')->get();

                foreach ($products as $product) {
                    DB::table('product_warehouse')->insert([
                        'product_id' => $product->id,
                        'warehouse_id' => $defaultWarehouse->id,
                        'stock' => $product->stock ?? 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

        // 3. Agregar warehouse_id a cash_sessions si no existe
        if (Schema::hasTable('cash_sessions') && !Schema::hasColumn('cash_sessions', 'warehouse_id')) {
            Schema::table('cash_sessions', function (Blueprint $table) {
                $table->foreignId('warehouse_id')->nullable()->after('user_id')->constrained('warehouses')->onDelete('set null');
                $table->index('warehouse_id');
            });

            // Asignar bodega por defecto a sesiones existentes
            $defaultWarehouse = DB::table('warehouses')->where('is_default', true)->first();
            if ($defaultWarehouse) {
                DB::table('cash_sessions')->whereNull('warehouse_id')->update([
                    'warehouse_id' => $defaultWarehouse->id
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar warehouse_id de cash_sessions
        if (Schema::hasColumn('cash_sessions', 'warehouse_id')) {
            Schema::table('cash_sessions', function (Blueprint $table) {
                $table->dropForeign(['warehouse_id']);
                $table->dropColumn('warehouse_id');
            });
        }

        // Eliminar tablas
        Schema::dropIfExists('product_warehouse');
        Schema::dropIfExists('warehouses');
    }
};
