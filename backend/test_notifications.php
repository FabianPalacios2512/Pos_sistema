<?php

/**
 * Script de prueba para crear movimientos de inventario y probar notificaciones
 * Ejecutar: php backend/test_notifications.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Bootstrap de Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Obtener el tenant actual (ajustar según tu sistema)
$tenantId = 'fabianda'; // Cambiar por tu tenant de prueba
\Stancl\Tenancy\Facades\Tenancy::initialize($tenantId);

echo "🚀 Creando movimientos de prueba para notificaciones...\n\n";

try {
    // Obtener algunos productos aleatorios
    $products = DB::table('products')->limit(5)->get();

    if ($products->isEmpty()) {
        echo "❌ No hay productos en la base de datos. Crea algunos productos primero.\n";
        exit(1);
    }

    // Obtener usuario admin
    $user = DB::table('users')->first();

    if (!$user) {
        echo "❌ No hay usuarios en la base de datos.\n";
        exit(1);
    }

    $movements = [];
    $now = Carbon::now();

    // Crear diferentes tipos de movimientos
    $movementTypes = [
        ['type' => 'in', 'reason' => 'purchase', 'qty' => 50],
        ['type' => 'out', 'reason' => 'sale', 'qty' => 10],
        ['type' => 'in', 'reason' => 'adjustment_positive', 'qty' => 5],
        ['type' => 'out', 'reason' => 'adjustment_negative', 'qty' => 3],
        ['type' => 'out', 'reason' => 'damaged', 'qty' => 2],
        ['type' => 'in', 'reason' => 'returned', 'qty' => 1],
    ];

    foreach ($movementTypes as $index => $movType) {
        $product = $products[$index % $products->count()];
        $currentStock = $product->current_stock ?? 100;
        $quantity = $movType['qty'];

        if ($movType['type'] === 'in') {
            $newStock = $currentStock + $quantity;
        } else {
            $newStock = max(0, $currentStock - $quantity);
        }

        $movement = [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => $movType['type'],
            'reason' => $movType['reason'],
            'quantity' => $movType['type'] === 'in' ? $quantity : -$quantity,
            'previous_stock' => $currentStock,
            'new_stock' => $newStock,
            'unit_cost' => $product->cost_price ?? 1000,
            'total_cost' => ($product->cost_price ?? 1000) * $quantity,
            'unit_price' => $product->price ?? 2000,
            'total_value' => ($product->price ?? 2000) * $quantity,
            'movement_date' => $now->copy()->subMinutes(rand(5, 120)),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $movements[] = $movement;

        echo "✅ Movimiento creado: {$movType['type']} - {$movType['reason']} - {$quantity} unidades de {$product->name}\n";
    }

    // Insertar movimientos
    DB::table('inventory_movements')->insert($movements);

    echo "\n🎉 ¡{count($movements)} movimientos creados exitosamente!\n";
    echo "🔔 Ahora puedes abrir el header y ver las notificaciones.\n\n";

    // Mostrar URL de la API
    echo "📡 Endpoint de notificaciones: /api/inventory/notifications\n";
    echo "🌐 Prueba en: http://localhost:8080/api/inventory/notifications\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
