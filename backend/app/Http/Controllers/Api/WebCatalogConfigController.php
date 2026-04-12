<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WebCatalogConfigController extends Controller
{
    /**
     * Obtener la configuración del catálogo web del tenant actual
     */
    public function getConfig(Request $request)
    {

        try {
            // Usar el helper tenant() para obtener el ID del tenant actual
            $tenantId = tenant('id');

            // Buscar configuración existente
            $config = DB::table('web_catalog_configs')
                ->where('tenant_id', $tenantId)
                ->first();

            if (!$config) {
                return response()->json([
                    'success' => false,
                    'message' => 'Configuración no encontrada',
                    'data' => null
                ]);
            }

            // Decodificar JSONs
            $config->visible_categories = json_decode($config->visible_categories);


            return response()->json([
                'success' => true,
                'data' => $config
            ]);
        } catch (\Exception $e) {
            Log::error('Error en getConfig: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar la configuración: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guardar o actualizar la configuración
     */
    public function saveConfig(Request $request)
    {

        try {
            $tenantId = tenant('id');

            $logoUrl = $request->input('brandIdentity.logo');
            $bannerUrl = $request->input('brandIdentity.banner');


            // BACKWARD COMPATIBILITY: Aceptar datos en formato NUEVO (products) o VIEJO (inventoryVisibility)
            $visibleCategories = $request->input('products.visibleCategories')
                ?? $request->input('inventoryVisibility.visibleCategories', []);

            $hideOutOfStock = $request->input('products.hideOutOfStock')
                ?? $request->input('inventoryVisibility.hideOutOfStock', false);

            $data = [
                'store_active' => $request->input('storeActive', true),
                'logo_url' => $logoUrl,
                'banner_url' => $bannerUrl,
                'primary_color' => $request->input('brandIdentity.primaryColor', '#10B981'),
                'template' => $request->input('brandIdentity.template', 'modern-grid'),

                'visible_categories' => json_encode($visibleCategories),
                'show_prices' => $request->input('products.showPrices', true),
                'hide_out_of_stock' => $hideOutOfStock,

                'allow_orders' => $request->input('orders.allowOrders', true),
                'whatsapp_number' => $request->input('orders.whatsappNumber', ''),
                'custom_message' => $request->input('orders.customMessage', ''),

                'delivery_cost' => $request->input('businessRules.deliveryCost', 0),
                'minimum_order' => $request->input('businessRules.minimumOrder', 0),
                'sync_with_cash_register' => $request->input('businessRules.syncWithCashRegister', false),

                'updated_at' => now()
            ];

            // Verificar si existe para decidir si crear o actualizar (para manejar created_at)
            $exists = DB::table('web_catalog_configs')->where('tenant_id', $tenantId)->exists();

            if (!$exists) {
                $data['tenant_id'] = $tenantId;
                $data['created_at'] = now();
                DB::table('web_catalog_configs')->insert($data);
            } else {
                DB::table('web_catalog_configs')
                    ->where('tenant_id', $tenantId)
                    ->update($data);
            }

            // Limpiar caché de configuración para que se recargue
            Cache::forget("web_catalog_config_{$tenantId}");


            return response()->json([
                'success' => true,
                'message' => 'Configuración guardada exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error en saveConfig', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la configuración: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener la configuración pública del catálogo (sin autenticación)
     */
    /**
     * Obtener la configuración pública del catálogo (sin autenticación)
     */
    public function getPublicConfig($tenantSubdomain = null)
    {
        try {
            // Como estamos en una ruta de tenant (tenant_api.php), el tenant ya está inicializado
            $tenantId = tenant('id');

            // Buscar configuración en la BD del tenant
            $config = DB::table('web_catalog_configs')
                ->where('tenant_id', $tenantId)
                ->first();

            // Si no existe configuración, retornar valores por defecto
            if (!$config) {
                $config = (object) [
                    'store_active' => true,
                    'logo_url' => null,
                    'banner_url' => null,
                    'primary_color' => '#10B981',
                    'template' => 'modern-grid',
                    'visible_categories' => '[]',
                    'hide_out_of_stock' => false,
                    'whatsapp_number' => '+57',
                    'custom_message' => 'Hola, quiero hacer el siguiente pedido:',
                    'delivery_cost' => 0,
                    'minimum_order' => 0,
                    'sync_with_cash_register' => false
                ];
            }

            // Obtener nombre de la tienda del objeto tenant
            // Nota: Depende de las columnas que tenga tu tabla tenants o la metadata
            $storeName = tenant('business_name') ?? tenant('id');

            // Parsear JSON y agregar información del tenant
            $publicConfig = [
                'store_name' => $storeName,
                'store_active' => (bool) $config->store_active,
                'logo_url' => $config->logo_url,
                'banner_url' => $config->banner_url,
                'primary_color' => $config->primary_color,
                'template' => $config->template,
                'visible_categories' => json_decode($config->visible_categories ?? '[]'),
                'hide_out_of_stock' => (bool) $config->hide_out_of_stock,
                'whatsapp_number' => $config->whatsapp_number,
                'custom_message' => $config->custom_message,
                'delivery_cost' => (float) $config->delivery_cost,
                'minimum_order' => (float) $config->minimum_order,
                'sync_with_cash_register' => (bool) $config->sync_with_cash_register
            ];

            return response()->json([
                'success' => true,
                'data' => $publicConfig
            ]);

        } catch (\Exception $e) {
            Log::error('Error en getPublicConfig: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar configuración pública: ' . $e->getMessage()
            ], 500);
        }
    }
}