<?php

/**
 * Script para limpiar y actualizar métodos de pago en TODOS los tenants
 *
 * Este script:
 * 1. Elimina métodos redundantes (Nequi, Daviplata)
 * 2. Elimina método "Crédito" si existe (no confundir con "Crédito en Tienda")
 * 3. Actualiza la descripción de "Transferencia Bancaria" para incluir todas las plataformas
 * 4. Agrega "Crédito en Tienda" si no existe
 * 5. Aplica cambios a TODOS los tenants del sistema SaaS
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

echo "\n🔧 Iniciando limpieza de métodos de pago en todos los tenants...\n\n";

// Obtener todos los tenants
$tenants = Tenant::all();
$totalTenants = $tenants->count();

echo "📊 Total de tenants encontrados: {$totalTenants}\n\n";

$successCount = 0;
$errorCount = 0;

foreach ($tenants as $index => $tenant) {
    $tenantNumber = $index + 1;
    $businessName = isset($tenant->data['business_name']) ? $tenant->data['business_name'] : 'Sin nombre';
    echo "[{$tenantNumber}/{$totalTenants}] Procesando tenant: {$tenant->id} ({$businessName})...\n";

    try {
        $tenant->run(function () use ($tenant) {
            DB::beginTransaction();

            try {
                // 1. Eliminar métodos redundantes (Nequi, Daviplata)
                $deletedRedundant = DB::table('payment_methods')
                    ->whereIn('code', ['nequi', 'daviplata'])
                    ->delete();

                if ($deletedRedundant > 0) {
                    echo "   ✅ Eliminados {$deletedRedundant} métodos redundantes (Nequi, Daviplata)\n";
                }

                // 2. Eliminar método "Crédito" genérico (NO "Crédito en Tienda")
                $deletedCredit = DB::table('payment_methods')
                    ->where('code', 'credito')
                    ->where('code', '!=', 'credito_tienda')
                    ->delete();

                if ($deletedCredit > 0) {
                    echo "   ✅ Eliminado método 'Crédito' genérico\n";
                }

                // 3. Actualizar "Transferencia Bancaria" con nueva descripción
                $updatedTransfer = DB::table('payment_methods')
                    ->where('code', 'transferencia')
                    ->update([
                        'name' => 'Transferencia Bancaria',
                        'description' => 'Transferencia bancaria, Nequi, Daviplata u otras plataformas',
                        'icon' => '🏦',
                        'config' => json_encode([
                            'require_reference' => true,
                            'platforms' => ['Bancolombia', 'Nequi', 'Daviplata', 'Banco de Bogotá', 'PSE']
                        ])
                    ]);

                if ($updatedTransfer > 0) {
                    echo "   ✅ Actualizado método 'Transferencia Bancaria'\n";
                }

                // 4. Verificar si existe "Crédito en Tienda"
                $creditiendaExists = DB::table('payment_methods')
                    ->where('code', 'credito_tienda')
                    ->exists();

                if (!$creditiendaExists) {
                    // Insertar "Crédito en Tienda"
                    DB::table('payment_methods')->insert([
                        'name' => 'Crédito en Tienda',
                        'code' => 'credito_tienda',
                        'description' => 'Sistema de crédito de la tienda para clientes autorizados',
                        'icon' => '🏪',
                        'active' => true,
                        'requires_reference' => false,
                        'sort_order' => 4,
                        'config' => json_encode([
                            'require_customer' => true,
                            'require_credit_approval' => true
                        ]),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    echo "   ✅ Agregado método 'Crédito en Tienda'\n";
                } else {
                    echo "   ℹ️  Método 'Crédito en Tienda' ya existe\n";
                }

                // 5. Reorganizar sort_order
                DB::statement("
                    UPDATE payment_methods
                    SET sort_order = CASE code
                        WHEN 'efectivo' THEN 1
                        WHEN 'tarjeta' THEN 2
                        WHEN 'transferencia' THEN 3
                        WHEN 'credito_tienda' THEN 4
                        ELSE sort_order
                    END
                ");

                echo "   ✅ Reorganizado orden de métodos de pago\n";

                // Contar métodos finales
                $finalCount = DB::table('payment_methods')->count();
                echo "   📊 Total de métodos de pago activos: {$finalCount}\n";

                DB::commit();
                echo "   ✅ Tenant procesado exitosamente\n\n";

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        });

        $successCount++;

    } catch (\Exception $e) {
        $errorCount++;
        echo "   ❌ Error procesando tenant {$tenant->id}: " . $e->getMessage() . "\n\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "🎯 RESUMEN DE LA OPERACIÓN\n";
echo str_repeat("=", 60) . "\n";
echo "✅ Tenants procesados exitosamente: {$successCount}\n";
echo "❌ Tenants con errores: {$errorCount}\n";
echo "📊 Total de tenants: {$totalTenants}\n";
echo str_repeat("=", 60) . "\n\n";

if ($successCount === $totalTenants) {
    echo "🎉 ¡Todos los tenants fueron actualizados correctamente!\n\n";
    echo "📋 Cambios aplicados:\n";
    echo "   • Eliminados: Nequi, Daviplata (redundantes)\n";
    echo "   • Eliminado: Crédito genérico\n";
    echo "   • Actualizado: Transferencia Bancaria (incluye todas las plataformas)\n";
    echo "   • Verificado/Agregado: Crédito en Tienda\n";
    echo "   • Reorganizado: Orden de visualización\n\n";
} else {
    echo "⚠️  Algunos tenants presentaron errores. Revise el log anterior.\n\n";
}

echo "✨ Proceso completado.\n\n";
