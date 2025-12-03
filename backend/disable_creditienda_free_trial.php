<?php

/**
 * Script para desactivar Creditienda en todos los tenants con plan free_trial
 * Ejecutar: php disable_creditienda_free_trial.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

echo "\n🔒 DESACTIVANDO CREDITIENDA EN TENANTS FREE_TRIAL\n";
echo "===================================================\n\n";

try {
    // Obtener todos los tenants con plan free_trial
    $freeTrialTenants = DB::connection('mysql')
        ->table('tenants')
        ->where('plan', 'free_trial')
        ->get();

    echo "📋 Tenants encontrados con plan free_trial: " . $freeTrialTenants->count() . "\n\n";

    $updated = 0;
    $alreadyDisabled = 0;

    foreach ($freeTrialTenants as $tenantData) {
        echo "🔍 Procesando tenant: {$tenantData->id} ({$tenantData->business_name})\n";

        try {
            $tenant = Tenant::find($tenantData->id);

            if (!$tenant) {
                echo "   ⚠️  Tenant no encontrado en el sistema\n\n";
                continue;
            }

            $tenant->run(function() use ($tenantData, &$updated, &$alreadyDisabled) {
                $settings = DB::table('system_settings')->first();

                if (!$settings) {
                    echo "   ⚠️  No hay system_settings en este tenant\n\n";
                    return;
                }

                if ($settings->creditienda_enabled) {
                    DB::table('system_settings')
                        ->where('id', $settings->id)
                        ->update(['creditienda_enabled' => false]);

                    echo "   ✅ Creditienda DESACTIVADO\n\n";
                    $updated++;
                } else {
                    echo "   ℹ️  Creditienda ya estaba desactivado\n\n";
                    $alreadyDisabled++;
                }
            });

        } catch (\Exception $e) {
            echo "   ❌ Error procesando tenant: " . $e->getMessage() . "\n\n";
        }
    }

    echo "===================================================\n";
    echo "✅ Proceso completado!\n";
    echo "   - Tenants actualizados: $updated\n";
    echo "   - Tenants ya desactivados: $alreadyDisabled\n";
    echo "   - Total procesados: " . ($updated + $alreadyDisabled) . "\n";
    echo "===================================================\n\n";

} catch (\Exception $e) {
    echo "❌ ERROR GENERAL: " . $e->getMessage() . "\n";
    exit(1);
}
