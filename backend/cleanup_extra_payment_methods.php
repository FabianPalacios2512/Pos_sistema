<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "🧹 Limpiando métodos de pago extras...\n\n";

    // Primero, verificar qué métodos existen
    $existing = DB::table('payment_methods')->get(['id', 'code', 'name', 'active']);

    if ($existing->isEmpty()) {
        echo "⚠️  No hay métodos de pago en la base de datos.\n";
        echo "💡 Ejecuta: php populate_payment_methods.php\n";
        exit(0);
    }

    echo "📋 Métodos actuales:\n";
    foreach ($existing as $method) {
        $status = $method->active ? '✅' : '❌';
        echo "   {$status} {$method->name} ({$method->code})\n";
    }
    echo "\n";

    // Eliminar métodos extras (Nequi, Daviplata, Crédito)
    $deleted = DB::table('payment_methods')
        ->whereIn('code', ['nequi', 'daviplata', 'credito'])
        ->delete();

    if ($deleted > 0) {
        echo "✅ Eliminados {$deleted} métodos de pago extras\n\n";
    } else {
        echo "ℹ️  No se encontraron métodos extras para eliminar\n\n";
    }

    // Verificar métodos restantes
    $remaining = DB::table('payment_methods')
        ->orderBy('sort_order')
        ->get(['name', 'code', 'active']);

    echo "📋 Métodos finales:\n";
    foreach ($remaining as $method) {
        $status = $method->active ? '✅' : '❌';
        echo "   {$status} {$method->name} ({$method->code})\n";
    }

    echo "\n✅ Limpieza completada\n";
    echo "ℹ️  Solo quedan: Efectivo, Tarjeta, Transferencia\n";
    echo "ℹ️  Creditienda se agrega automáticamente cuando está activo\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 Archivo: " . $e->getFile() . "\n";
    echo "📍 Línea: " . $e->getLine() . "\n\n";

    // Si no existe la tabla, sugerir crear los métodos
    if (strpos($e->getMessage(), "doesn't exist") !== false) {
        echo "💡 La tabla 'payment_methods' no existe.\n";
        echo "💡 Ejecuta: php populate_payment_methods.php\n";
    }

    exit(1);
}
