<?php

/**
 * Script agresivo para eliminar TODAS las bases de datos que empiecen con "tenant"
 * ⚠️ PELIGRO: Esto eliminará PERMANENTEMENTE todas las bases de datos de tenant
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  ⚠️  ELIMINACIÓN TOTAL DE BASES DE DATOS (Pattern: tenant*)  ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

try {
    // Obtener TODAS las bases de datos que empiecen con "tenant"
    $databases = DB::connection('mysql')->select("SHOW DATABASES LIKE 'tenant%'");

    if (empty($databases)) {
        echo "✅ No hay bases de datos con patrón 'tenant*'. Sistema limpio.\n\n";
        exit(0);
    }

    $count = count($databases);
    echo "🔍 Bases de datos encontradas: {$count}\n";
    echo str_repeat("─", 70) . "\n\n";

    // Listar todas las bases de datos a eliminar
    foreach ($databases as $db) {
        $dbName = array_values((array)$db)[0];
        echo "   • {$dbName}\n";
    }

    echo "\n" . str_repeat("─", 70) . "\n";
    echo "⚠️  ESTA ACCIÓN NO SE PUEDE DESHACER\n";
    echo "⚠️  Se eliminarán {$count} bases de datos permanentemente\n";
    echo str_repeat("─", 70) . "\n\n";

    echo "¿Continuar? (escribe 'SI' para confirmar): ";
    $handle = fopen("php://stdin", "r");
    $confirmation = trim(fgets($handle));
    fclose($handle);

    if (strtoupper($confirmation) !== 'SI') {
        echo "\n❌ Operación cancelada por el usuario.\n\n";
        exit(0);
    }

    echo "\n🗑️  Eliminando bases de datos...\n\n";

    $deleted = 0;
    $errors = 0;

    foreach ($databases as $db) {
        $dbName = array_values((array)$db)[0];

        try {
            DB::connection('mysql')->statement("DROP DATABASE `{$dbName}`");
            echo "✅ {$dbName}\n";
            $deleted++;
        } catch (\Exception $e) {
            echo "❌ {$dbName} - Error: " . $e->getMessage() . "\n";
            $errors++;
        }
    }

    // Limpiar tablas de tenants
    echo "\n" . str_repeat("─", 70) . "\n";
    echo "🧹 Limpiando tablas de sistema...\n";

    try {
        DB::connection('mysql')->table('tenants')->truncate();
        echo "✅ Tabla 'tenants' limpiada\n";
    } catch (\Exception $e) {
        echo "⚠️  Tabla 'tenants': " . $e->getMessage() . "\n";
    }

    try {
        DB::connection('mysql')->table('domains')->truncate();
        echo "✅ Tabla 'domains' limpiada\n";
    } catch (\Exception $e) {
        // Tabla no existe, ignorar
    }

    echo "\n" . str_repeat("═", 70) . "\n";
    echo "\n📋 RESUMEN FINAL:\n";
    echo "   ✅ Bases de datos eliminadas: {$deleted}\n";
    echo "   ❌ Errores: {$errors}\n";
    echo "   🧹 Tablas de sistema limpiadas\n";
    echo "\n✅ Limpieza completada. Sistema listo para producción.\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR CRÍTICO:\n";
    echo "   " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . "\n";
    echo "   Línea: " . $e->getLine() . "\n\n";
    exit(1);
}
