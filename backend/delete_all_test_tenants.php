<?php

/**
 * Script para eliminar TODAS las bases de datos de tenants de prueba
 * ⚠️ USAR SOLO EN DESARROLLO - Elimina PERMANENTEMENTE las bases de datos
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  🗑️  ELIMINACIÓN MASIVA DE BASES DE DATOS DE TENANTS         ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

try {
    // 1. Obtener todos los tenants de la base de datos central
    $tenants = DB::connection('mysql')->table('tenants')->get();

    if ($tenants->isEmpty()) {
        echo "✅ No hay tenants en el sistema. Base de datos limpia.\n\n";
        exit(0);
    }

    echo "📊 Tenants encontrados: " . $tenants->count() . "\n";
    echo str_repeat("─", 70) . "\n\n";

    $deleted = 0;
    $errors = 0;

    foreach ($tenants as $tenant) {
        $dbName = $tenant->tenancy_db_name ?? "tenant{$tenant->id}";

        echo "🔍 Procesando: {$tenant->id}\n";
        echo "   └─ Base de datos: {$dbName}\n";

        try {
            // Verificar si la base de datos existe
            $databases = DB::connection('mysql')->select("SHOW DATABASES LIKE '{$dbName}'");

            if (!empty($databases)) {
                // Eliminar la base de datos
                DB::connection('mysql')->statement("DROP DATABASE `{$dbName}`");
                echo "   ✅ Base de datos eliminada\n";
                $deleted++;
            } else {
                echo "   ⚠️  Base de datos no existe (ya estaba eliminada)\n";
            }

        } catch (\Exception $e) {
            echo "   ❌ Error al eliminar base de datos: " . $e->getMessage() . "\n";
            $errors++;
        }

        echo "\n";
    }

    echo str_repeat("─", 70) . "\n";

    // 2. Limpiar la tabla de tenants
    echo "\n🧹 Limpiando tabla de tenants...\n";
    DB::connection('mysql')->table('tenants')->truncate();
    echo "✅ Tabla 'tenants' limpiada\n\n";

    // 3. Limpiar tabla de dominios si existe
    try {
        DB::connection('mysql')->table('domains')->truncate();
        echo "✅ Tabla 'domains' limpiada\n\n";
    } catch (\Exception $e) {
        // Tabla no existe, ignorar
    }

    // Resumen
    echo str_repeat("═", 70) . "\n";
    echo "\n📋 RESUMEN:\n";
    echo "   ✅ Bases de datos eliminadas: {$deleted}\n";
    echo "   ⚠️  Errores: {$errors}\n";
    echo "   🧹 Tabla 'tenants' limpiada\n";
    echo "\n";
    echo "✅ Proceso completado. Sistema listo para nuevos tenants.\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR CRÍTICO:\n";
    echo "   " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . "\n";
    echo "   Línea: " . $e->getLine() . "\n\n";
    exit(1);
}
