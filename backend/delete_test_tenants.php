<?php

/**
 * Script para eliminar tenants de prueba
 * Uso: php delete_test_tenants.php tenant1 tenant2 tenant3
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

// Obtener los IDs de tenants a eliminar desde argumentos
$tenantsToDelete = array_slice($argv, 1);

if (empty($tenantsToDelete)) {
    echo "❌ Uso: php delete_test_tenants.php tenant1 tenant2 tenant3\n\n";
    echo "📋 Tenants disponibles hoy:\n";

    $tenants = Tenant::where('created_at', '>=', now()->startOfDay())
        ->orderBy('created_at', 'desc')
        ->get(['id', 'business_name', 'plan', 'created_at']);

    foreach ($tenants as $tenant) {
        echo sprintf(
            "  - %s (%s) - %s - %s\n",
            $tenant->id,
            $tenant->business_name,
            $tenant->plan,
            $tenant->created_at->format('H:i:s')
        );
    }

    echo "\n💡 Ejemplo: php delete_test_tenants.php la_maracas las_mamis\n";
    exit(1);
}

echo "🗑️  Eliminando " . count($tenantsToDelete) . " tenant(s)...\n\n";

foreach ($tenantsToDelete as $tenantId) {
    try {
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            echo "⚠️  Tenant '$tenantId' no encontrado - SALTANDO\n";
            continue;
        }

        echo "🔄 Eliminando tenant: $tenantId ({$tenant->business_name})\n";

        // 1. Eliminar base de datos del tenant
        $databaseName = 'tenant' . $tenantId;

        try {
            DB::statement("DROP DATABASE IF EXISTS `$databaseName`");
            echo "  ✅ Base de datos '$databaseName' eliminada\n";
        } catch (\Exception $e) {
            echo "  ⚠️  Error eliminando DB: " . $e->getMessage() . "\n";
        }

        // 2. Eliminar registro del tenant
        $tenant->delete();
        echo "  ✅ Registro del tenant eliminado\n";

        echo "  ✅ Tenant '$tenantId' eliminado completamente\n\n";

    } catch (\Exception $e) {
        echo "  ❌ Error eliminando tenant '$tenantId': " . $e->getMessage() . "\n\n";
    }
}

echo "✅ Proceso completado\n";
