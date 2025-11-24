<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemSetting::create([
            'company_name' => 'POS Demo System',
            'company_document' => '123456789',
            'company_phone' => '+57 (1) 234-5678',
            'company_email' => 'contacto@posdemo.com',
            'company_address' => 'Calle 123 #45-67, Bogotá, Colombia',
            'iva_enabled' => true,
            'iva_percentage' => 19.00,
            'iva_display_name' => 'IVA',
            'invoice_prefix' => 'FACT-',
            'invoice_number_start' => 1,
            'invoice_current_number' => 1,
            'invoice_footer_message' => 'Gracias por su compra. ¡Vuelva pronto!',
            'require_customer' => false,
            'discounts_enabled' => true,
            'customer_discounts_enabled' => true,
            'promo_codes_enabled' => true,
            'payment_methods' => [
                'efectivo' => true,
                'tarjeta' => true,
                'transferencia' => true,
                'cheque' => false
            ],
            'auto_apply_discounts' => true,
            'show_product_images' => true,
            'products_per_page' => 12,
            'low_stock_alerts' => true,
            'low_stock_threshold' => 5,
        ]);
    }
}
