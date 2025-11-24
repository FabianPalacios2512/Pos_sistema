<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
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

        // Configuración del sistema por defecto
        DB::table('system_settings')->insert([
            'company_name' => 'Mi Tienda POS',
            'company_document' => '900123456',
            'company_phone' => '3001234567',
            'company_email' => 'info@mitienda.com',
            'company_address' => 'Calle 123 #45-67',
            'iva_enabled' => 1,
            'iva_percentage' => 19.00,
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

        echo "\n✅ Base de datos inicializada correctamente\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📧 Email:    admin@pos.com\n";
        echo "🔑 CC:       1001504182\n";
        echo "🔐 Password: admin123\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
}
