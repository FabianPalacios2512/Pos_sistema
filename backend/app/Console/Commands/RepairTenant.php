<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RepairTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:repair {tenant_id : ID del tenant a reparar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Repara un tenant ejecutando migraciones y seeders faltantes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant_id');

        $this->info("🔧 Reparando tenant: {$tenantId}");

        // Buscar el tenant
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            $this->error("❌ Tenant '{$tenantId}' no encontrado");
            return 1;
        }

        $this->info("✅ Tenant encontrado: {$tenant->business_name}");

        try {
            // Ejecutar migraciones
            $this->info("📦 Ejecutando migraciones...");
            Artisan::call('tenants:migrate', [
                '--tenants' => [$tenantId],
                '--force' => true,
            ]);
            $this->info(Artisan::output());

            // Ejecutar seeders dentro del contexto del tenant
            $this->info("🌱 Ejecutando seeders...");
            $tenant->run(function () {
                // Métodos de pago por defecto
                $paymentMethodsCount = \DB::table('payment_methods')->count();

                if ($paymentMethodsCount === 0) {
                    $this->info("💳 Creando métodos de pago...");

                    $paymentMethods = [
                        [
                            'name' => 'Efectivo',
                            'code' => 'efectivo',
                            'description' => 'Pago en efectivo',
                            'icon' => '💵',
                            'active' => true,
                            'requires_reference' => false,
                            'sort_order' => 1,
                            'config' => json_encode(['allow_change' => true, 'currency' => 'COP'])
                        ],
                        [
                            'name' => 'Tarjeta de Crédito/Débito',
                            'code' => 'tarjeta',
                            'description' => 'Pago con tarjeta',
                            'icon' => '💳',
                            'active' => true,
                            'requires_reference' => true,
                            'sort_order' => 2,
                            'config' => json_encode(['require_authorization' => true, 'fee_percentage' => 0])
                        ],
                        [
                            'name' => 'Transferencia Bancaria',
                            'code' => 'transferencia',
                            'description' => 'Transferencia electrónica',
                            'icon' => '🏦',
                            'active' => true,
                            'requires_reference' => true,
                            'sort_order' => 3,
                            'config' => json_encode(['require_bank_account' => true])
                        ],
                        [
                            'name' => 'Nequi',
                            'code' => 'nequi',
                            'description' => 'Pago con Nequi',
                            'icon' => '📱',
                            'active' => true,
                            'requires_reference' => true,
                            'sort_order' => 4,
                            'config' => json_encode(['require_phone' => true])
                        ],
                        [
                            'name' => 'Daviplata',
                            'code' => 'daviplata',
                            'description' => 'Pago con Daviplata',
                            'icon' => '💰',
                            'active' => true,
                            'requires_reference' => true,
                            'sort_order' => 5,
                            'config' => json_encode(['require_phone' => true])
                        ],
                        [
                            'name' => 'Crédito',
                            'code' => 'credito',
                            'description' => 'Pago a crédito',
                            'icon' => '📋',
                            'active' => true,
                            'requires_reference' => false,
                            'sort_order' => 6,
                            'config' => json_encode(['allow_installments' => true])
                        ],
                    ];

                    foreach ($paymentMethods as $method) {
                        \DB::table('payment_methods')->insert(array_merge($method, [
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]));
                    }

                    $this->info("✅ {count($paymentMethods)} métodos de pago creados");
                } else {
                    $this->info("✅ Métodos de pago ya existentes: {$paymentMethodsCount}");
                }

                // Categorías de gastos por defecto
                $expenseCategoriesCount = \DB::table('expense_categories')->count();

                if ($expenseCategoriesCount === 0) {
                    $this->info("📊 Creando categorías de gastos...");

                    $expenseCategories = [
                        ['name' => 'Servicios Públicos', 'color' => '#3B82F6', 'description' => 'Agua, luz, internet, teléfono', 'is_active' => true],
                        ['name' => 'Nómina y Salarios', 'color' => '#10B981', 'description' => 'Salarios, prestaciones, pagos a empleados', 'is_active' => true],
                        ['name' => 'Mantenimiento', 'color' => '#F59E0B', 'description' => 'Reparaciones, mantenimiento de equipos e instalaciones', 'is_active' => true],
                        ['name' => 'Suministros y Materiales', 'color' => '#8B5CF6', 'description' => 'Papelería, productos de limpieza, insumos', 'is_active' => true],
                        ['name' => 'Arriendo', 'color' => '#EC4899', 'description' => 'Pago de arrendamiento de local o bodega', 'is_active' => true],
                        ['name' => 'Transporte', 'color' => '#14B8A6', 'description' => 'Gastos de transporte y logística', 'is_active' => true],
                        ['name' => 'Otros Gastos', 'color' => '#6B7280', 'description' => 'Gastos varios no clasificados', 'is_active' => true],
                    ];

                    foreach ($expenseCategories as $category) {
                        \DB::table('expense_categories')->insert(array_merge($category, [
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]));
                    }

                    $this->info("✅ " . count($expenseCategories) . " categorías de gastos creadas");
                } else {
                    $this->info("✅ Categorías de gastos ya existentes: {$expenseCategoriesCount}");
                }
            });

            $this->info("✅ Tenant reparado exitosamente!");
            $this->info("🎉 El tenant '{$tenantId}' está listo para usar");

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error al reparar tenant: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
