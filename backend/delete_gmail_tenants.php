<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

echo "🔍 Buscando tenants y usuarios creados con Gmail...\n\n";

// Obtener todos los tenants
$allTenants = Tenant::all();

$tenantsToDelete = [];

foreach ($allTenants as $tenant) {
    // Buscar usuarios con email de Gmail en cada tenant
    try {
        $tenant->run(function () use ($tenant, &$tenantsToDelete) {
            $gmailUsers = DB::table('users')
                ->where('email', 'LIKE', '%@gmail.com')
                ->get();

            if ($gmailUsers->count() > 0) {
                $tenantsToDelete[] = [
                    'tenant' => $tenant,
                    'gmail_users' => $gmailUsers
                ];
            }
        });
    } catch (\Exception $e) {
        // Si hay error (base de datos no existe), continuar
        continue;
    }
}

if (empty($tenantsToDelete)) {
    echo "✅ No se encontraron tenants con usuarios de Gmail\n";
    exit(0);
}

echo "📋 TENANTS CON USUARIOS GMAIL ENCONTRADOS:\n";
echo str_repeat('=', 70) . "\n";

foreach ($tenantsToDelete as $item) {
    $tenant = $item['tenant'];
    $gmailUsers = $item['gmail_users'];

    echo "\n🏢 Tenant: {$tenant->id}\n";
    echo "   Nombre: {$tenant->business_name}\n";
    echo "   Plan: {$tenant->plan}\n";
    echo "   👥 Usuarios Gmail:\n";

    foreach ($gmailUsers as $user) {
        echo "      - {$user->name} ({$user->email})\n";
    }
}

echo "\n" . str_repeat('=', 70) . "\n";
echo "⚠️  ¿Deseas ELIMINAR estos " . count($tenantsToDelete) . " tenants?\n";
echo "⚠️  Esto borrará:\n";
echo "   - Las bases de datos de cada tenant\n";
echo "   - Los registros en la base de datos central\n";
echo "   - TODA la información es IRREVERSIBLE\n\n";

echo "Escribe 'SI' para confirmar: ";
$handle = fopen("php://stdin", "r");
$confirmation = trim(fgets($handle));

if (strtoupper($confirmation) !== 'SI') {
    echo "\n❌ Operación cancelada\n";
    exit(0);
}

echo "\n🗑️  ELIMINANDO TENANTS...\n\n";

foreach ($tenantsToDelete as $item) {
    $tenant = $item['tenant'];

    try {
        echo "🗑️  Eliminando: {$tenant->id} ({$tenant->business_name})...\n";

        // Eliminar base de datos del tenant
        $databaseName = 'tenant' . $tenant->id;
        DB::statement("DROP DATABASE IF EXISTS `{$databaseName}`");

        // Eliminar registro del tenant
        $tenant->delete();

        echo "   ✅ Eliminado exitosamente\n\n";

    } catch (\Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n\n";
    }
}

echo "✅ Proceso completado\n";
