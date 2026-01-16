<?php

/**
 * Script de Validación y Reparación Automática de Esquemas de Tenants
 *
 * Este script:
 * 1. Detecta todos los tenants activos
 * 2. Verifica que tengan todas las columnas necesarias
 * 3. Aplica las correcciones automáticamente
 * 4. Registra un log detallado
 *
 * USO: php validate_and_fix_tenant_schemas.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Configurar colores para la consola
$colors = [
    'reset' => "\033[0m",
    'red' => "\033[31m",
    'green' => "\033[32m",
    'yellow' => "\033[33m",
    'blue' => "\033[34m",
    'cyan' => "\033[36m",
    'bold' => "\033[1m",
];

function colorize($text, $color, $bold = false) {
    global $colors;
    $prefix = $bold ? $colors['bold'] : '';
    return $prefix . $colors[$color] . $text . $colors['reset'];
}

echo "\n" . colorize("═══════════════════════════════════════════════", 'cyan', true) . "\n";
echo colorize("   VALIDACIÓN Y REPARACIÓN DE ESQUEMAS TENANTS", 'cyan', true) . "\n";
echo colorize("═══════════════════════════════════════════════", 'cyan', true) . "\n\n";

// Esquema esperado de la tabla customers
$expectedSchema = [
    'credit_photo' => [
        'type' => 'text',
        'nullable' => true,
        'after' => 'credit_active',
        'migration' => 'ALTER TABLE customers ADD COLUMN credit_photo TEXT NULL AFTER credit_active;'
    ],
    // Agregar aquí más columnas cuando se detecten problemas futuros
];

try {
    // Obtener todos los tenants
    $tenants = DB::table('tenants')->where('active', 1)->get();

    if ($tenants->isEmpty()) {
        echo colorize("❌ No se encontraron tenants activos", 'red', true) . "\n\n";
        exit(1);
    }

    echo colorize("📊 Tenants encontrados: " . count($tenants), 'blue') . "\n\n";

    $summary = [
        'total' => count($tenants),
        'ok' => 0,
        'fixed' => 0,
        'errors' => 0
    ];

    foreach ($tenants as $tenant) {
        $dbName = $tenant->tenancy_db_name;
        echo colorize("┌─ Validando: ", 'cyan') . colorize($dbName, 'bold') . "\n";

        try {
            // Verificar que la base de datos existe
            $databases = DB::select("SHOW DATABASES LIKE '$dbName'");
            if (empty($databases)) {
                echo colorize("│  ⚠️  Base de datos no existe, saltando...", 'yellow') . "\n";
                echo colorize("└─", 'cyan') . "\n\n";
                $summary['errors']++;
                continue;
            }

            // Obtener columnas actuales
            $currentColumns = DB::select("SELECT COLUMN_NAME
                                         FROM INFORMATION_SCHEMA.COLUMNS
                                         WHERE TABLE_SCHEMA = '$dbName'
                                         AND TABLE_NAME = 'customers'");

            if (empty($currentColumns)) {
                echo colorize("│  ❌ Tabla 'customers' no existe", 'red') . "\n";
                echo colorize("└─", 'cyan') . "\n\n";
                $summary['errors']++;
                continue;
            }

            $currentColumnNames = array_map(fn($col) => $col->COLUMN_NAME, $currentColumns);

            // Verificar cada columna esperada
            $needsFix = false;
            $fixesApplied = [];

            foreach ($expectedSchema as $columnName => $columnInfo) {
                if (!in_array($columnName, $currentColumnNames)) {
                    $needsFix = true;
                    echo colorize("│  ⚠️  Falta columna: ", 'yellow') . colorize($columnName, 'bold') . "\n";

                    // Aplicar la migración
                    try {
                        DB::statement("USE `$dbName`");
                        DB::statement($columnInfo['migration']);

                        echo colorize("│  ✅ Columna agregada: ", 'green') . colorize($columnName, 'bold') . "\n";
                        $fixesApplied[] = $columnName;
                    } catch (\Exception $e) {
                        echo colorize("│  ❌ Error al agregar '$columnName': ", 'red') . $e->getMessage() . "\n";
                    }
                }
            }

            // Regresar a la conexión por defecto
            DB::statement("USE `105pos`");

            if (!$needsFix) {
                echo colorize("│  ✅ Esquema correcto", 'green') . "\n";
                $summary['ok']++;
            } elseif (!empty($fixesApplied)) {
                echo colorize("│  🔧 Correcciones aplicadas: " . count($fixesApplied), 'green', true) . "\n";
                $summary['fixed']++;
            } else {
                $summary['errors']++;
            }

        } catch (\Exception $e) {
            echo colorize("│  ❌ Error: ", 'red') . $e->getMessage() . "\n";
            $summary['errors']++;
        }

        echo colorize("└─", 'cyan') . "\n\n";
    }

    // Resumen final
    echo colorize("═══════════════════════════════════════════════", 'cyan', true) . "\n";
    echo colorize("                  RESUMEN FINAL", 'cyan', true) . "\n";
    echo colorize("═══════════════════════════════════════════════", 'cyan', true) . "\n\n";

    echo colorize("Total tenants:       ", 'blue') . colorize($summary['total'], 'bold') . "\n";
    echo colorize("✅ Sin problemas:   ", 'green') . colorize($summary['ok'], 'bold') . "\n";
    echo colorize("🔧 Corregidos:      ", 'yellow') . colorize($summary['fixed'], 'bold') . "\n";
    echo colorize("❌ Con errores:     ", 'red') . colorize($summary['errors'], 'bold') . "\n\n";

    if ($summary['fixed'] > 0) {
        echo colorize("✨ Se aplicaron correcciones exitosamente", 'green', true) . "\n\n";
    }

    if ($summary['errors'] > 0) {
        echo colorize("⚠️  Algunos tenants requieren atención manual", 'yellow', true) . "\n\n";
        exit(1);
    }

    echo colorize("🎉 ¡Todos los esquemas están sincronizados!", 'green', true) . "\n\n";

} catch (\Exception $e) {
    echo "\n" . colorize("❌ ERROR CRÍTICO: ", 'red', true) . $e->getMessage() . "\n\n";
    echo colorize($e->getTraceAsString(), 'red') . "\n\n";
    exit(1);
}
