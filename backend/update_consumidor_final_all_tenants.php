<?php

/**
 * Script para actualizar "Cliente General" a "Consumidor Final" en todos los tenants
 * Y crear "Consumidor Final" si no existe
 *
 * Normativa DIAN Colombia:
 * - Consumidor Final con NIT genérico: 222222222222 (12 veces el 2)
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Facades\Tenancy;

echo "🔄 Iniciando actualización: Cliente General → Consumidor Final\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Obtener todos los tenants
$tenants = \App\Models\Tenant::all();

$totalUpdated = 0;
$totalCreated = 0;
$totalTenants = $tenants->count();

foreach ($tenants as $tenant) {
    try {
        $tenantName = $tenant->business_name ?? 'Sin nombre';
        echo "📦 Procesando tenant: {$tenant->id} ({$tenantName})\n";

        // Inicializar el tenant
        Tenancy::initialize($tenant);

        // 1. ACTUALIZAR: Buscar clientes con "Cliente General" y actualizarlos
        $countGeneral = DB::table('customers')
            ->where('name', 'Cliente General')
            ->count();

        if ($countGeneral > 0) {
            // Actualizar todos los registros
            $updated = DB::table('customers')
                ->where('name', 'Cliente General')
                ->update([
                    'name' => 'Consumidor Final',
                    'document_type' => 'NIT',
                    'document_number' => '222222222222',
                    'email' => 'consumidor@sistema.local',
                    'phone' => '000-000-0000',
                    'address' => 'Dirección General',
                    'city' => 'No registrada',
                    'updated_at' => now()
                ]);

            echo "   ✅ Actualizado {$updated} cliente(s) 'Cliente General' → 'Consumidor Final'\n";
            $totalUpdated += $updated;
        } else {
            echo "   ℹ️  No se encontraron clientes con 'Cliente General'\n";
        }

        // 2. CREAR: Verificar si existe "Consumidor Final" con NIT 222222222222
        $existingConsumidor = DB::table('customers')
            ->where('document_number', '222222222222')
            ->first();

        if (!$existingConsumidor) {
            // Crear el consumidor final
            DB::table('customers')->insert([
                'name' => 'Consumidor Final',
                'document_type' => 'NIT',
                'document_number' => '222222222222',
                'email' => 'consumidor@sistema.local',
                'phone' => '000-000-0000',
                'address' => 'Dirección General',
                'city' => 'No registrada',
                'credit_limit' => 0.00,
                'current_debt' => 0.00,
                'total_purchases' => 0.00,
                'total_orders' => 0,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            echo "   ✅ Creado 'Consumidor Final' con NIT 222222222222\n";
            $totalCreated++;
        } else {
            echo "   ℹ️  'Consumidor Final' ya existe (ID: {$existingConsumidor->id})\n";
        }

        // Finalizar el tenant
        Tenancy::end();

    } catch (\Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
        Tenancy::end();
    }

    echo "\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Proceso completado\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 Tenants procesados: {$totalTenants}\n";
echo "🔄 Clientes actualizados: {$totalUpdated}\n";
echo "➕ Clientes creados: {$totalCreated}\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
