<?php

/**
 * Script para actualizar "Cliente General" a "Cliente Final" en todos los tenants
 *
 * Normativa DIAN Colombia:
 * - Cliente Final con NIT genérico: 222222222222
 * - Reemplaza el antiguo "Cliente General"
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Facades\Tenancy;

echo "🔄 Iniciando actualización: Cliente General → Cliente Final\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Obtener todos los tenants
$tenants = \App\Models\Tenant::all();

$totalUpdated = 0;
$totalTenants = $tenants->count();

foreach ($tenants as $tenant) {
    try {
        $tenantName = isset($tenant->data['name']) ? $tenant->data['name'] : 'Sin nombre';
        echo "📦 Procesando tenant: {$tenant->id} ({$tenantName})\n";

        // Inicializar el tenant
        Tenancy::initialize($tenant);

        // Contar clientes con "Cliente General"
        $count = DB::table('customers')
            ->where('name', 'Cliente General')
            ->count();

        if ($count > 0) {
            // Actualizar todos los registros
            $updated = DB::table('customers')
                ->where('name', 'Cliente General')
                ->update([
                    'name' => 'Cliente Final',
                    'document_type' => 'NIT',
                    'document_number' => '222222222222',
                    'updated_at' => now()
                ]);

            echo "   ✅ Actualizado {$updated} cliente(s)\n";
            $totalUpdated += $updated;
        } else {
            echo "   ℹ️  No se encontraron clientes con 'Cliente General'\n";
        }

        // Finalizar contexto del tenant
        Tenancy::end();

    } catch (\Exception $e) {
        echo "   ❌ Error en tenant {$tenant->id}: {$e->getMessage()}\n";
        Tenancy::end();
        continue;
    }

    echo "\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✨ Actualización completada\n";
echo "   Tenants procesados: {$totalTenants}\n";
echo "   Clientes actualizados: {$totalUpdated}\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
