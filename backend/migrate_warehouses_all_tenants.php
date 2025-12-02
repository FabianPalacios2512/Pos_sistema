<?php

/**
 * Script para ejecutar la migración de warehouses en todos los tenants
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Obtener todos los tenants
$databases = DB::select("SHOW DATABASES LIKE 'tenant%'");

$successCount = 0;
$errorCount = 0;
$errors = [];

echo "\n🏪 Ejecutando migración de warehouses en todos los tenants...\n\n";

foreach ($databases as $database) {
    $dbName = $database->{'Database (tenant%)'};

    echo "📦 Procesando: $dbName\n";

    try {
        // Cambiar a la base de datos del tenant
        DB::purge('tenant');
        config(['database.connections.tenant.database' => $dbName]);
        DB::reconnect('tenant');
        DB::connection('tenant')->getPdo();

        // Verificar si ya existe la tabla warehouses
        $hasWarehouses = Schema::connection('tenant')->hasTable('warehouses');

        if ($hasWarehouses) {
            echo "  ⏭️  Ya tiene tabla warehouses, omitiendo...\n";
            $successCount++;
            continue;
        }

        // 1. Crear tabla warehouses
        Schema::connection('tenant')->create('warehouses', function ($table) {
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

        echo "  ✅ Tabla warehouses creada\n";

        // Crear bodega principal por defecto
        DB::connection('tenant')->table('warehouses')->insert([
            'name' => 'Sede Principal',
            'is_default' => true,
            'active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        echo "  ✅ Bodega principal creada\n";

        // 2. Crear tabla pivot product_warehouse
        Schema::connection('tenant')->create('product_warehouse', function ($table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->integer('stock')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'warehouse_id']);
            $table->index('warehouse_id');
            $table->index('stock');
        });

        echo "  ✅ Tabla product_warehouse creada\n";

        // Migrar stock existente
        $defaultWarehouse = DB::connection('tenant')->table('warehouses')->where('is_default', true)->first();

        if ($defaultWarehouse) {
            $products = DB::connection('tenant')->table('products')->select('id', 'stock')->get();

            foreach ($products as $product) {
                DB::connection('tenant')->table('product_warehouse')->insert([
                    'product_id' => $product->id,
                    'warehouse_id' => $defaultWarehouse->id,
                    'stock' => $product->stock ?? 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            echo "  ✅ Stock migrado: {$products->count()} productos\n";
        }

        // 3. Agregar warehouse_id a cash_sessions si existe y no tiene la columna
        if (Schema::connection('tenant')->hasTable('cash_sessions') &&
            !Schema::connection('tenant')->hasColumn('cash_sessions', 'warehouse_id')) {

            Schema::connection('tenant')->table('cash_sessions', function ($table) {
                $table->foreignId('warehouse_id')->nullable()->after('user_id')->constrained('warehouses')->onDelete('set null');
                $table->index('warehouse_id');
            });

            echo "  ✅ Columna warehouse_id agregada a cash_sessions\n";

            // Asignar bodega por defecto a sesiones existentes
            if ($defaultWarehouse) {
                DB::connection('tenant')->table('cash_sessions')->whereNull('warehouse_id')->update([
                    'warehouse_id' => $defaultWarehouse->id
                ]);
                echo "  ✅ Sesiones existentes actualizadas con bodega por defecto\n";
            }
        }

        echo "  ✅ $dbName completado exitosamente\n\n";
        $successCount++;

    } catch (\Exception $e) {
        $errorCount++;
        $errors[] = [
            'database' => $dbName,
            'error' => $e->getMessage()
        ];
        echo "  ❌ Error en $dbName: " . $e->getMessage() . "\n\n";
    }
}

echo "\n";
echo "=".str_repeat("=", 60)."=\n";
echo "  RESUMEN\n";
echo "=".str_repeat("=", 60)."=\n";
echo "✅ Exitosos: $successCount\n";
echo "❌ Errores: $errorCount\n";

if (!empty($errors)) {
    echo "\n📋 ERRORES DETALLADOS:\n";
    foreach ($errors as $error) {
        echo "  • {$error['database']}: {$error['error']}\n";
    }
}

echo "\n✨ Proceso completado!\n\n";
