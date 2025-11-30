<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingsController extends Controller
{
    /**
     * Obtener configuración del sistema
     */
    public function index()
    {
        try {
            $settings = SystemSetting::getSettings();

            return response()->json([
                'success' => true,
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener configuración: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar configuración del sistema
     */
    public function update(Request $request)
    {
        try {
            $settings = SystemSetting::getSettings();

            $validated = $request->validate([
                'company_name' => 'nullable|string|max:255',
                'company_document' => 'nullable|string|max:255',
                'company_phone' => 'nullable|string|max:255',
                'company_email' => 'nullable|email|max:255',
                'company_address' => 'nullable|string',
                'iva_enabled' => 'boolean',
                'iva_percentage' => 'numeric|min:0|max:100',
                'iva_display_name' => 'string|max:255',
                'invoice_prefix' => 'string|max:10',
                'invoice_footer_message' => 'nullable|string',
                'invoice_template' => 'nullable|in:classic,modern,minimal',
                'qr_style' => 'nullable|in:rounded,square,modern',
                'require_customer' => 'boolean',
                'require_customer_quotations' => 'boolean',
                'discounts_enabled' => 'boolean',
                'customer_discounts_enabled' => 'boolean',
                'promo_codes_enabled' => 'boolean',
                'auto_apply_discounts' => 'boolean',
                'show_product_images' => 'boolean',
                'products_per_page' => 'integer|min:1|max:50',
                'low_stock_alerts' => 'boolean',
                'low_stock_threshold' => 'integer|min:0',
                'enable_credit_system' => 'boolean',
                'creditienda_enabled' => 'boolean',
                'credit_surcharge_percentage' => 'numeric|min:0|max:100',
                'enable_loyalty_system' => 'boolean',
                'loyalty_points_per_currency' => 'numeric|min:0',
                'loyalty_point_value' => 'numeric|min:0',
            ]);

            $settings->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Configuración actualizada correctamente',
                'data' => $settings->fresh()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar configuración: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener siguiente número de factura
     */
    public function getNextInvoiceNumber()
    {
        try {
            $settings = SystemSetting::getSettings();
            $nextNumber = $settings->getNextInvoiceNumber();

            return response()->json([
                'success' => true,
                'data' => [
                    'next_number' => $nextNumber,
                    'current_counter' => $settings->invoice_current_number
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener número de factura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restablecer configuración a valores por defecto
     */
    public function reset()
    {
        try {
            $settings = SystemSetting::getSettings();

            // Mantener información de la empresa pero restablecer otras configuraciones
            $settings->update([
                'iva_enabled' => true,
                'iva_percentage' => 19.00,
                'iva_display_name' => 'IVA',
                'require_customer' => false,
                'discounts_enabled' => true,
                'customer_discounts_enabled' => true,
                'promo_codes_enabled' => true,
                'auto_apply_discounts' => true,
                'show_product_images' => true,
                'products_per_page' => 12,
                'low_stock_alerts' => true,
                'low_stock_threshold' => 5,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Configuración restablecida a valores por defecto',
                'data' => $settings->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restablecer configuración: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guardar configuración inicial del onboarding
     */
    public function saveOnboarding(Request $request)
    {
        try {
            $settings = SystemSetting::getSettings();

            $validated = $request->validate([
                'invoice_template' => 'required|in:classic,modern,minimal',
                'company_logo' => 'nullable|string',
                'company_name' => 'required|string|max:255',
                'company_email' => 'required|email|max:255',
                'company_phone' => 'required|string|max:255',
                'company_address' => 'required|string|max:500',
                'thank_you_message' => 'nullable|string|max:500',
                'qr_style' => 'required|in:rounded,square,circle',
                'whatsapp_number' => 'nullable|string|max:20',
                'onboarding_completed' => 'boolean'
            ]);

            // Actualizar configuración
            $settings->update([
                'company_name' => $validated['company_name'],
                'company_email' => $validated['company_email'],
                'company_phone' => $validated['company_phone'],
                'company_address' => $validated['company_address'],
                'company_logo' => $validated['company_logo'] ?? null,
                'invoice_template' => $validated['invoice_template'],
                'invoice_footer_message' => $validated['thank_you_message'] ?? '¡Gracias por su compra!',
                'qr_style' => $validated['qr_style'],
                'whatsapp_business_number' => $validated['whatsapp_number'] ?? null,
                'onboarding_completed' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Configuración inicial guardada exitosamente',
                'data' => $settings->fresh()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en onboarding:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar configuración inicial: ' . $e->getMessage()
            ], 500);
        }
    }
}
