<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'company_document',
        'company_phone',
        'company_email',
        'company_address',
        'company_logo',
        'iva_enabled',
        'iva_percentage',
        'iva_display_name',
        'invoice_prefix',
        'invoice_number_start',
        'invoice_current_number',
        'invoice_footer_message',
        'invoice_template',
        'qr_style',
        'require_customer',
        'require_customer_quotations',
        'creditienda_enabled',
        'discounts_enabled',
        'customer_discounts_enabled',
        'promo_codes_enabled',
        'payment_methods',
        'auto_apply_discounts',
        'show_product_images',
        'products_per_page',
        'low_stock_alerts',
        'low_stock_threshold',
        'enable_credit_system',
        'credit_surcharge_percentage',
        'enable_loyalty_system',
        'loyalty_points_per_currency',
        'loyalty_point_value',
    ];

    protected $casts = [
        'iva_enabled' => 'boolean',
        'require_customer' => 'boolean',
        'require_customer_quotations' => 'boolean',
        'creditienda_enabled' => 'boolean',
        'discounts_enabled' => 'boolean',
        'customer_discounts_enabled' => 'boolean',
        'promo_codes_enabled' => 'boolean',
        'auto_apply_discounts' => 'boolean',
        'show_product_images' => 'boolean',
        'low_stock_alerts' => 'boolean',
        'payment_methods' => 'array',
        'iva_percentage' => 'decimal:2',
        'enable_credit_system' => 'boolean',
        'credit_surcharge_percentage' => 'decimal:2',
        'enable_loyalty_system' => 'boolean',
        'loyalty_points_per_currency' => 'decimal:6',
        'loyalty_point_value' => 'decimal:2',
    ];

    /**
     * Obtener la configuración del sistema (singleton)
     * 🔒 Con validación automática de funciones premium según plan
     */
    public static function getSettings()
    {
        $settings = static::first() ?? static::create([]);

        // 🔒 SEGURIDAD: Forzar desactivación de funciones premium según plan
        try {
            $tenantPlan = \DB::connection('mysql')
                ->table('tenants')
                ->where('id', tenant('id'))
                ->value('plan');

            // Creditienda: Solo premium y enterprise
            if (!in_array($tenantPlan ?? 'free_trial', ['premium', 'enterprise'])) {
                $settings->creditienda_enabled = false;
            }

            // Fidelización: Solo premium y enterprise
            if (!in_array($tenantPlan ?? 'free_trial', ['premium', 'enterprise'])) {
                $settings->enable_loyalty_system = false;
            }
        } catch (\Exception $e) {
            // Si hay error consultando el plan, asumir free_trial por seguridad
            $settings->creditienda_enabled = false;
            $settings->enable_loyalty_system = false;
        }

        return $settings;
    }

    /**
     * Obtener el siguiente número de factura
     */
    public function getNextInvoiceNumber()
    {
        $current = $this->invoice_current_number;
        $this->increment('invoice_current_number');
        return $this->invoice_prefix . str_pad($current, 4, '0', STR_PAD_LEFT);
    }
}
