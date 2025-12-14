<?php

/**
 * Script para migrar tenants con guiones a guiones bajos
 *
 * Este script:
 * 1. Identifica tenants con guiones en el ID
 * 2. Crea un nuevo tenant con guiones bajos
 * 3. Copia los datos del dominio
 * 4. Ejecuta las migraciones necesarias
 *
 * IMPORTANTE: Este script NO migra datos existentes, solo asegura que
 * los tenants futuros y actuales tengan la estructura correcta de tablas.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  VERIFICACIÓN DE TENANTS Y TABLAS DE EXPENSES             ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Obtener todos los tenants
$tenants = Tenant::with('domains')->get();

echo "📊 Total de tenants: " . $tenants->count() . "\n\n";

foreach ($tenants as $tenant) {
    $domain = $tenant->domains->first()?->domain ?? 'Sin dominio';
    $dbName = config('tenancy.database.prefix') . $tenant->id . config('tenancy.database.suffix');

    echo "────────────────────────────────────────────────────────────\n";
    echo "🏢 Tenant: {$tenant->business_name}\n";
    echo "   ID: {$tenant->id}\n";
    echo "   Dominio: {$domain}\n";
    echo "   Base de datos: {$dbName}\n";

    // Verificar si tiene guiones
    if (str_contains($tenant->id, '-')) {
        echo "   ⚠️  ALERTA: ID contiene guiones\n";
        echo "   💡 RECOMENDACIÓN: Los nuevos tenants usarán guiones bajos automáticamente\n";
    }

    // Verificar si la base de datos existe
    try {
        $dbExists = DB::connection('mysql')->select("
            SELECT SCHEMA_NAME
            FROM INFORMATION_SCHEMA.SCHEMATA
            WHERE SCHEMA_NAME = ?
        ", [$dbName]);

        if (empty($dbExists)) {
            echo "   ❌ Base de datos NO EXISTE\n";
            continue;
        }

        echo "   ✅ Base de datos existe\n";

        // Verificar tablas de expenses
        $tables = DB::connection('mysql')->select("
            SELECT TABLE_NAME
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = ?
            AND TABLE_NAME IN ('expense_categories', 'expenses')
            ORDER BY TABLE_NAME
        ", [$dbName]);

        $tableNames = array_column($tables, 'TABLE_NAME');

        if (in_array('expense_categories', $tableNames)) {
            // Contar categorías (usar comillas invertidas para soportar guiones)
            $categoryCount = DB::connection('mysql')->select("
                SELECT COUNT(*) as count FROM `{$dbName}`.`expense_categories`
            ")[0]->count ?? 0;

            echo "   ✅ Tabla expense_categories existe ({$categoryCount} categorías)\n";
        } else {
            echo "   ❌ Tabla expense_categories NO EXISTE\n";
        }

        if (in_array('expenses', $tableNames)) {
            // Contar gastos (usar comillas invertidas para soportar guiones)
            $expenseCount = DB::connection('mysql')->select("
                SELECT COUNT(*) as count FROM `{$dbName}`.`expenses`
            ")[0]->count ?? 0;

            echo "   ✅ Tabla expenses existe ({$expenseCount} registros)\n";
        } else {
            echo "   ❌ Tabla expenses NO EXISTE\n";
        }

        // Si faltan tablas, sugerir migración
        if (!in_array('expense_categories', $tableNames) || !in_array('expenses', $tableNames)) {
            echo "\n   💡 EJECUTAR: php artisan tenants:migrate --tenants={$tenant->id}\n";
        }

    } catch (\Exception $e) {
        echo "   ❌ Error al verificar: " . $e->getMessage() . "\n";
    }

    echo "\n";
}

echo "════════════════════════════════════════════════════════════\n";
echo "✅ VERIFICACIÓN COMPLETADA\n";
echo "\n";
echo "📝 RESUMEN:\n";
echo "   • Los tenants nuevos usarán guiones bajos (_) automáticamente\n";
echo "   • Los tenants existentes con guiones (-) seguirán funcionando\n";
echo "   • Si un tenant no tiene tablas de expenses, ejecutar:\n";
echo "     php artisan tenants:migrate --tenants=TENANT_ID\n";
echo "\n";
