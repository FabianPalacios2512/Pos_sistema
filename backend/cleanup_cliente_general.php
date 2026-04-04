<?php

/**
 * Script para limpiar el cliente "Cliente General" duplicado en todos los tenants.
 * 
 * El sistema solo debe tener "Consumidor Final" (NIT 222222222222) como cliente por defecto.
 * "Cliente General" fue creado erróneamente por PosCompleto.vue y debe eliminarse.
 * 
 * Este script:
 * 1. Busca "Cliente General" (email: general@sistema.local o document_number: 00000000000)
 * 2. Reasigna sus facturas/ventas al "Consumidor Final" real
 * 3. Elimina el cliente duplicado
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Facades\Tenancy;

echo "🧹 Limpieza: Eliminar 'Cliente General' duplicado de todos los tenants\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$tenants = \App\Models\Tenant::all();
$totalDeleted = 0;
$totalReassigned = 0;

foreach ($tenants as $tenant) {
    try {
        $tenantName = $tenant->business_name ?? 'Sin nombre';
        echo "📦 Tenant: {$tenant->id} ({$tenantName})\n";

        Tenancy::initialize($tenant);

        // Buscar el Consumidor Final real
        $consumidorFinal = DB::table('customers')
            ->where('document_number', '222222222222')
            ->first();

        if (!$consumidorFinal) {
            echo "   ⚠️  No tiene Consumidor Final - saltando\n";
            Tenancy::end();
            continue;
        }

        // Buscar clientes "Cliente General" duplicados
        $duplicados = DB::table('customers')
            ->where(function ($query) {
                $query->where('email', 'general@sistema.local')
                      ->orWhere('document_number', '00000000000')
                      ->orWhere(function ($q) {
                          $q->where('name', 'Cliente General')
                            ->where('document_number', '!=', '222222222222');
                      });
            })
            ->get();

        if ($duplicados->isEmpty()) {
            echo "   ✅ Limpio - no hay duplicados\n";
            Tenancy::end();
            continue;
        }

        foreach ($duplicados as $duplicado) {
            echo "   🔍 Encontrado: '{$duplicado->name}' (ID: {$duplicado->id}, doc: {$duplicado->document_number})\n";

            // Reasignar facturas (invoices) al Consumidor Final
            $tablesWithCustomerId = ['invoices', 'sales', 'orders', 'quotes'];
            
            foreach ($tablesWithCustomerId as $table) {
                try {
                    if (DB::getSchemaBuilder()->hasTable($table) && DB::getSchemaBuilder()->hasColumn($table, 'customer_id')) {
                        $count = DB::table($table)
                            ->where('customer_id', $duplicado->id)
                            ->update(['customer_id' => $consumidorFinal->id]);
                        
                        if ($count > 0) {
                            echo "     ↳ Reasignados {$count} registros en '{$table}'\n";
                            $totalReassigned += $count;
                        }
                    }
                } catch (\Exception $e) {
                    // Tabla no existe o no tiene la columna, continuar
                }
            }

            // Eliminar el cliente duplicado
            DB::table('customers')->where('id', $duplicado->id)->delete();
            echo "   🗑️  Eliminado '{$duplicado->name}' (ID: {$duplicado->id})\n";
            $totalDeleted++;
        }

        Tenancy::end();

    } catch (\Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
        Tenancy::end();
    }
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Completado: {$totalDeleted} clientes eliminados, {$totalReassigned} registros reasignados\n";
