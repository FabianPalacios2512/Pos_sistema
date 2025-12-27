<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

echo "🗑️  ELIMINANDO TENANTS RESTANTES SIN BASE DE DATOS\n\n";

$tenants = Tenant::all();

foreach ($tenants as $tenant) {
    echo "🗑️  Eliminando: {$tenant->id} ({$tenant->business_name})...\n";

    try {
        // Intentar eliminar base de datos
        $databaseName = 'tenant' . $tenant->id;
        try {
            DB::statement("DROP DATABASE IF EXISTS `{$databaseName}`");
        } catch (\Exception $e) {
            echo "   ⚠️  Aviso DB: " . $e->getMessage() . "\n";
        }

        // Eliminar registro del tenant
        $tenant->delete();

        echo "   ✅ Eliminado exitosamente\n\n";

    } catch (\Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n\n";
    }
}

echo "✅ Todos los tenants han sido eliminados\n";
echo "📊 Tenants restantes: " . Tenant::count() . "\n";
