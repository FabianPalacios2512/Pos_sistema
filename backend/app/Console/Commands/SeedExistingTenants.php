<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class SeedExistingTenants extends Command
{
    protected $signature = 'tenants:seed-defaults';
    protected $description = 'Seed payment methods and expense categories for existing tenants';

    public function handle()
    {
        $tenants = Tenant::all();

        $this->info("🔄 Procesando {$tenants->count()} tenants...\n");

        foreach ($tenants as $tenant) {
            $this->info("📦 Tenant: {$tenant->id} ({$tenant->business_name})");

            $tenant->run(function () {
                $this->seedPaymentMethods();
                $this->seedExpenseCategories();
            });

            $this->info("   ✅ Completado\n");
        }

        $this->info("🎉 Todos los tenants han sido actualizados!");
    }

    private function seedPaymentMethods()
    {
        // Verificar si ya existen métodos de pago
        $count = DB::table('payment_methods')->count();
        if ($count > 0) {
            $this->warn("   ⚠️  Ya existen {$count} métodos de pago, omitiendo...");
            return;
        }

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
            DB::table('payment_methods')->insert(array_merge($method, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->info("   ✅ 6 métodos de pago creados");
    }

    private function seedExpenseCategories()
    {
        // Verificar si ya existen categorías
        $count = DB::table('expense_categories')->count();
        if ($count > 0) {
            $this->warn("   ⚠️  Ya existen {$count} categorías de gastos, omitiendo...");
            return;
        }

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
            DB::table('expense_categories')->insert(array_merge($category, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->info("   ✅ 7 categorías de gastos creadas");
    }
}
