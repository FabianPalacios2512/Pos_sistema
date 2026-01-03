<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Verificar si el rol Administrador ya existe (evitar duplicados)
        $existingAdminRole = DB::table('roles')->where('name', 'Administrador')->first();

        if ($existingAdminRole) {
            // El rol ya existe, usar su ID
            $adminRoleId = $existingAdminRole->id;
            \Log::info('🔄 DatabaseSeeder: Rol Administrador ya existe, usando ID: ' . $adminRoleId);
        } else {
            // Crear rol Administrador con todos los permisos
            $adminRoleId = DB::table('roles')->insertGetId([
                'name' => 'Administrador',
                'description' => 'Acceso completo al sistema',
                'permissions' => json_encode([
                    "dashboard.view",
                    "pos.view", "pos.create_sale", "pos.apply_discount", "pos.cancel_sale",
                    "invoices.view", "invoices.create", "invoices.edit", "invoices.delete", "invoices.print",
                    "returns.view", "returns.create", "returns.approve",
                    "products.view", "products.create", "products.edit", "products.delete",
                    "categories.view", "categories.create", "categories.edit", "categories.delete",
                    "stock.view", "stock.adjust", "stock.transfer",
                    "intelligent_inventory.view", "intelligent_inventory.predictions",
                    "customers.view", "customers.create", "customers.edit", "customers.delete", "customers.view_history",
                    "suppliers.view", "suppliers.create", "suppliers.edit", "suppliers.delete",
                    "users.view", "users.create", "users.edit", "users.delete", "users.change_password", "users.manage_roles",
                    "cash_register.view", "cash_register.open", "cash_register.close", "cash_register.movements",
                    "reports.view", "reports.sales", "reports.inventory", "reports.financial", "reports.export",
                    "settings.view", "settings.edit", "settings.manage_business"
                ]),
                'active' => 1,
                'users_count' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            \Log::info('✅ DatabaseSeeder: Rol Administrador creado con ID: ' . $adminRoleId);
        }

        // ✅ Verificar si el usuario admin ya existe
        $existingAdmin = DB::table('users')->where('role_id', $adminRoleId)->first();

        if (!$existingAdmin) {
            // Crear usuario administrador
            DB::table('users')->insert([
                'name' => 'Administrador',
                'email' => 'admin@pos.com',
                'cc' => '1001504182',
                'password' => Hash::make('admin123'),
                'phone' => '3001234567',
                'active' => 1,
                'role_id' => $adminRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            \Log::info('✅ DatabaseSeeder: Usuario administrador creado');
        } else {
            \Log::info('🔄 DatabaseSeeder: Usuario administrador ya existe');
        }

        // ✅ Verificar si el rol Vendedor ya existe
        $existingVendedor = DB::table('roles')->where('name', 'Vendedor')->first();

        if (!$existingVendedor) {
            // Crear rol Vendedor
            DB::table('roles')->insert([
                'name' => 'Vendedor',
                'description' => 'Acceso a ventas y consultas básicas',
                'permissions' => json_encode([
                    "dashboard.view",
                    "products.view",
                    "inventory.view",
                    "sales.view", "sales.create",
                    "customers.view", "customers.create",
                    "reports.view", "reports.sales",
                    "pos.view", "pos.create_sale", "pos.apply_discount", "pos.cancel_sale"
                ]),
                'active' => 1,
                'users_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            \Log::info('✅ DatabaseSeeder: Rol Vendedor creado');
        }

        // ✅ Verificar si system_settings ya existe
        $existingSettings = DB::table('system_settings')->where('id', 1)->first();

        if (!$existingSettings) {
            // Configuración del sistema por defecto
            DB::table('system_settings')->insert([
            'company_name' => 'Mi Tienda POS',
            'company_document' => '900123456',
            'company_phone' => '3001234567',
            'company_email' => 'info@mitienda.com',
            'company_address' => 'Calle 123 #45-67',
            'iva_enabled' => 1,
            'iva_percentage' => 0.00,
            'iva_display_name' => 'IVA',
            'invoice_prefix' => 'FACT-',
            'invoice_number_start' => 1,
            'invoice_current_number' => 1,
            'require_customer' => 0,
            'require_customer_quotations' => 0,
            'discounts_enabled' => 1,
            'customer_discounts_enabled' => 1,
            'promo_codes_enabled' => 1,
            'auto_apply_discounts' => 1,
            'show_product_images' => 1,
            'products_per_page' => 12,
            'low_stock_alerts' => 1,
            'low_stock_threshold' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
            \Log::info('✅ DatabaseSeeder: System settings creados');
        }

        // ✅ Verificar si ya existen métodos de pago
        $existingPaymentMethods = DB::table('payment_methods')->count();

        if ($existingPaymentMethods == 0) {
            // Crear métodos de pago por defecto (solo 3 básicos)
            // Creditienda se agrega automáticamente cuando está activo
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
                'description' => 'Pago con tarjeta de crédito o débito',
                'icon' => '💳',
                'active' => true,
                'requires_reference' => true,
                'sort_order' => 2,
                'config' => json_encode(['require_authorization' => true, 'fee_percentage' => 0])
            ],
            [
                'name' => 'Transferencia Bancaria',
                'code' => 'transferencia',
                'description' => 'Transferencia bancaria, Nequi, Daviplata u otras plataformas',
                'icon' => '🏦',
                'active' => true,
                'requires_reference' => true,
                'sort_order' => 3,
                'config' => json_encode(['require_reference' => true, 'platforms' => ['Bancolombia', 'Nequi', 'Daviplata', 'Banco de Bogotá', 'PSE']])
            ]
        ];

        foreach ($paymentMethods as $method) {
            DB::table('payment_methods')->insert(array_merge($method, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
            \Log::info('✅ DatabaseSeeder: Métodos de pago creados');
        }

        // ✅ Verificar si ya existen categorías de gastos
        $existingExpenseCategories = DB::table('expense_categories')->count();

        if ($existingExpenseCategories == 0) {
            // Crear categorías de gastos por defecto
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
            \Log::info('✅ DatabaseSeeder: Categorías de gastos creadas');
        }

        // ✅ COMENTADO: Los echo contaminan la respuesta HTTP JSON durante registro de tenants
        // Estos mensajes son útiles solo cuando se ejecuta desde CLI con php artisan db:seed
        // echo "\n✅ Base de datos inicializada correctamente\n";
        // echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        // echo "📧 Email:    admin@pos.com\n";
        // echo "🔑 CC:       1001504182\n";
        // echo "🔐 Password: admin123\n";
        // echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
}
