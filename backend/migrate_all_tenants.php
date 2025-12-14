<?php

use Illuminate\Support\Facades\Artisan;

/**
 * Script para ejecutar migraciones en TODOS los tenants
 * Uso: php artisan tinker < migrate_all_tenants.php
 */

$tenants = \App\Models\Tenant::all();

echo "🏢 Encontrados " . $tenants->count() . " tenants\n";
echo "🚀 Iniciando migraciones...\n\n";

foreach ($tenants as $tenant) {
    echo "📦 Tenant: {$tenant->id}\n";

    try {
        $tenant->run(function () {
            Artisan::call('migrate', ['--force' => true]);
            echo "   ✅ Migración completada\n";
        });
    } catch (\Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
    }

    echo "\n";
}

echo "✅ Proceso completado\n";
