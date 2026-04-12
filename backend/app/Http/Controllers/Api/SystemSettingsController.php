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

            // 🔒 Obtener datos completos del tenant desde la base de datos central
            $tenantData = \DB::connection('mysql')
                ->table('tenants')
                ->where('id', tenant('id'))
                ->select('id', 'business_name', 'plan', 'subscription_ends_at', 'created_at')
                ->first();

            // Obtener el plan de la BD
            $tenantPlanRaw = $tenantData->plan ?? 'free';

            // 🆕 Preparar datos del tenant para el frontend
            $tenant = null;
            if ($tenantData) {
                // Mapear nombres de planes para display
                $planNames = [
                    'free' => 'free_trial',
                    'basic' => 'basic',
                    'premium' => 'premium',
                    'enterprise' => 'enterprise'
                ];

                // Usar el nombre mapeado
                $tenantPlan = $planNames[$tenantPlanRaw] ?? 'free_trial';

                // 🔒 SEGURIDAD: Forzar desactivación de funciones premium según plan
                $allowedPlansForPremiumFeatures = ['premium', 'enterprise'];

                if (!in_array($tenantPlanRaw, $allowedPlansForPremiumFeatures)) {
                    $settings->creditienda_enabled = false;
                    $settings->enable_loyalty_system = false;
                }

                // Calcular max_users según el plan
                // Free/Trial: 1 usuario, Basic: 1 usuario, Premium: 3 usuarios, Enterprise: ilimitado
                $maxUsers = [
                    'free' => 1,
                    'basic' => 1,
                    'premium' => 3,
                    'enterprise' => null // null = ilimitado
                ];

                // Calcular max_warehouses según el plan
                $maxWarehouses = [
                    'free' => 1,
                    'basic' => 1,
                    'premium' => 3,
                    'enterprise' => null // null = ilimitado
                ];

                $tenant = [
                    'id' => $tenantData->id,
                    'business_name' => $tenantData->business_name,
                    'plan_type' => $tenantPlan, // Ya viene mapeado arriba
                    'subscription_status' => 'active', // Por ahora siempre activo
                    'subscription_start_date' => $tenantData->created_at,
                    'subscription_end_date' => $tenantData->subscription_ends_at,
                    'max_users' => $maxUsers[$tenantPlanRaw] ?? 1,
                    'max_warehouses' => $maxWarehouses[$tenantPlanRaw] ?? 1,
                    'max_products' => null, // ilimitado para todos los planes
                    'max_invoices' => null  // ilimitado para todos los planes
                ];
            } else {
                // Si no hay datos del tenant, usar valor por defecto
                $tenantPlan = 'free_trial';
            }

            return response()->json([
                'success' => true,
                'data' => $settings,
                'tenant_plan' => $tenantPlan,
                'tenant' => $tenant // 🆕 Agregar datos completos del tenant
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
                'store_type' => 'nullable|string|in:general,fashion,restaurant,food,electronics', // 🏪 Validación de tipo de tienda
                'company_document' => 'nullable|string|max:255',
                'company_phone' => 'nullable|string|max:255',
                'company_email' => 'nullable|email|max:255',
                'company_address' => 'nullable|string',
                'company_logo' => 'nullable|string', // ✅ Sin límite max: soporta base64 largo en LONGTEXT
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
                'whatsapp_business_number' => 'nullable|string|max:20',
                'onboarding_completed' => 'boolean', // ✅ Permitir actualizar onboarding
            ]);

            // 🔒 SEGURIDAD: Validar plan del tenant antes de permitir funciones premium
            $tenantPlan = \DB::connection('mysql')
                ->table('tenants')
                ->where('id', tenant('id'))
                ->value('plan');

            $allowedPlansForPremiumFeatures = ['premium', 'enterprise'];
            $isPremiumPlan = in_array($tenantPlan ?? 'free_trial', $allowedPlansForPremiumFeatures);

            // Bloquear activación de Creditienda si no es premium/enterprise
            if (!$isPremiumPlan && isset($validated['creditienda_enabled']) && $validated['creditienda_enabled']) {
                return response()->json([
                    'success' => false,
                    'message' => '🔒 Creditienda requiere plan Premium o Enterprise. Actualiza tu plan.',
                    'error_code' => 'PREMIUM_FEATURE_REQUIRED'
                ], 403);
            }

            // Bloquear activación de Fidelización si no es premium/enterprise
            if (!$isPremiumPlan && isset($validated['enable_loyalty_system']) && $validated['enable_loyalty_system']) {
                return response()->json([
                    'success' => false,
                    'message' => '🔒 Sistema de Fidelización requiere plan Premium o Enterprise. Actualiza tu plan.',
                    'error_code' => 'PREMIUM_FEATURE_REQUIRED'
                ], 403);
            }

            // Forzar desactivación si no es premium/enterprise (seguridad adicional)
            if (!$isPremiumPlan) {
                $validated['creditienda_enabled'] = false;
                $validated['enable_loyalty_system'] = false;
            }


            // ⚠️ DEBUG: Intentar UPDATE directo con DB
            if (isset($validated['store_type'])) {
                \DB::table('system_settings')
                    ->where('id', $settings->id)
                    ->update(['store_type' => $validated['store_type']]);

            }

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
                'iva_percentage' => 0.00,
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
    
    /**
     * Guardar tipo de layout del negocio (General, Moda, Restaurante)
     */
    public function saveBusinessLayout(Request $request)
    {
        try {
            $validated = $request->validate([
                'business_layout_type' => 'required|string|in:general,fashion,restaurant',
                'business_layout_selected' => 'boolean'
            ]);
            
            $settings = SystemSetting::first();
            
            if (!$settings) {
                $settings = SystemSetting::create([
                    'store_type' => $validated['business_layout_type'],
                    'business_layout_selected' => $validated['business_layout_selected'] ?? true
                ]);
            } else {
                // Actualizar store_type directamente en la BD
                $table = $settings->getTable();
                \DB::table($table)
                    ->where('id', $settings->id)
                    ->update([
                        'store_type' => $validated['business_layout_type'],
                        'updated_at' => now()
                    ]);
                    
                $settings = $settings->fresh();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Tipo de negocio actualizado exitosamente',
                'data' => [
                    'store_type' => $settings->store_type,
                    'layout_type' => $validated['business_layout_type']
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error al guardar layout del negocio:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar tipo de negocio: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener tipo de layout actual del negocio
     */
    public function getBusinessLayout()
    {
        try {
            $settings = SystemSetting::first();
            
            $layoutType = 'general'; // Default
            
            if ($settings && $settings->store_type) {
                $layoutType = $settings->store_type;
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'layout_type' => $layoutType,
                    'store_type' => $settings->store_type ?? 'general'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener tipo de negocio: ' . $e->getMessage()
            ], 500);
        }
    }
}