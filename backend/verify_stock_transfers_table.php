<?php

/**
 * Script para verificar la tabla stock_transfers en tenant
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== VERIFICACIÓN DE TABLA stock_transfers ===\n\n";

// Verificar en tenant105_pos_pro
$tenantDb = 'tenant105_pos_pro';

try {
    // Cambiar a la base de datos tenant
    config(['database.connections.mysql.database' => $tenantDb]);
    DB::purge('mysql');
    DB::reconnect('mysql');

    echo "Conectado a: $tenantDb\n\n";

    // Verificar si la tabla existe
    $tableExists = DB::select("SHOW TABLES LIKE 'stock_transfers'");
    if (empty($tableExists)) {
        echo "❌ ERROR: La tabla stock_transfers NO existe en $tenantDb\n";
        exit(1);
    }

    echo "✅ Tabla stock_transfers existe\n\n";

    // Mostrar estructura
    echo "=== ESTRUCTURA DE LA TABLA ===\n";
    $columns = DB::select("DESCRIBE stock_transfers");
    foreach ($columns as $col) {
        echo sprintf("%-30s %-20s %s\n",
            $col->Field,
            $col->Type,
            ($col->Null === 'YES' ? 'NULL' : 'NOT NULL')
        );
    }

    echo "\n=== REGISTROS EN LA TABLA ===\n";
    $count = DB::table('stock_transfers')->count();
    echo "Total de traslados: $count\n\n";

    if ($count > 0) {
        $transfers = DB::table('stock_transfers')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        echo "Últimos 5 traslados:\n";
        foreach ($transfers as $t) {
            echo sprintf(
                "ID: %d | Ref: %s | Status: %s | From: %d → To: %d | Created: %s\n",
                $t->id,
                $t->reference_number,
                $t->status,
                $t->source_warehouse_id,
                $t->destination_warehouse_id,
                $t->created_at
            );
        }
    } else {
        echo "⚠️  No hay traslados registrados aún\n";
    }

    echo "\n=== VERIFICACIÓN DE ITEMS ===\n";
    $itemCount = DB::table('stock_transfer_items')->count();
    echo "Total de items de traslado: $itemCount\n";

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n✅ Verificación completada\n";
