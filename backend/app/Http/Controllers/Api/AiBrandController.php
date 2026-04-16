<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GroqBrandService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AiBrandController extends Controller
{
    /**
     * Generate brand identity using Groq AI
     */
    public function generate(Request $request)
    {
        $request->validate([
            'business_description' => 'required|string|min:10|max:2000',
        ]);

        try {
            $tenantId = tenant('id');
            $storeName = tenant('business_name') ?? tenant('id');
            $description = $request->input('business_description');

            // Get current template if exists
            $currentConfig = DB::table('web_catalog_configs')
                ->where('tenant_id', $tenantId)
                ->first();
            $currentTemplate = $currentConfig->template ?? null;

            $service = new GroqBrandService();
            $result = $service->generateBrandIdentity($description, $storeName, $currentTemplate);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 422);
            }

            // Save to database
            $saved = $service->saveBrandIdentity($tenantId, $result['data'], $description);

            if (!$saved) {
                return response()->json([
                    'success' => false,
                    'message' => 'Se generó la identidad pero hubo un error al guardarla'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Identidad de marca generada exitosamente',
                'data' => $result['data']
            ]);
        } catch (\Exception $e) {
            Log::error('[AiBrand] Generate error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al generar identidad de marca: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Apply AI-generated settings to the catalog config
     */
    public function apply(Request $request)
    {
        $request->validate([
            'apply_colors' => 'boolean',
            'apply_template' => 'boolean',
        ]);

        try {
            $tenantId = tenant('id');

            $config = DB::table('web_catalog_configs')
                ->where('tenant_id', $tenantId)
                ->first();

            if (!$config || !$config->ai_color_palette) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay configuración de IA generada. Genera una primero.'
                ], 404);
            }

            $updates = ['updated_at' => now()];

            // Apply primary color from AI palette
            if ($request->input('apply_colors', true)) {
                $palette = json_decode($config->ai_color_palette, true);
                if (isset($palette['primary'])) {
                    $updates['primary_color'] = $palette['primary'];
                }
            }

            // Apply recommended template
            if ($request->input('apply_template', false)) {
                if ($config->ai_recommended_template) {
                    $updates['template'] = $config->ai_recommended_template;
                }
            }

            DB::table('web_catalog_configs')
                ->where('tenant_id', $tenantId)
                ->update($updates);

            Cache::forget("web_catalog_config_{$tenantId}");

            return response()->json([
                'success' => true,
                'message' => 'Configuración de IA aplicada exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('[AiBrand] Apply error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al aplicar configuración: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current AI brand data
     */
    public function getAiBrandData()
    {
        try {
            $tenantId = tenant('id');

            $config = DB::table('web_catalog_configs')
                ->where('tenant_id', $tenantId)
                ->first();

            if (!$config) {
                return response()->json([
                    'success' => true,
                    'data' => null
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'business_description' => $config->business_description,
                    'color_palette' => json_decode($config->ai_color_palette, true),
                    'fonts' => json_decode($config->ai_fonts, true),
                    'recommended_template' => $config->ai_recommended_template,
                    'banner_texts' => json_decode($config->ai_banner_texts, true),
                    'about_us' => $config->ai_about_us,
                    'value_messages' => json_decode($config->ai_value_messages, true),
                    'announcements' => json_decode($config->ai_announcements, true),
                    'cross_sell_messages' => json_decode($config->ai_cross_sell_messages, true),
                    'layout_config' => json_decode($config->ai_layout_config ?? 'null', true),
                    'generated_at' => $config->ai_generated_at,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('[AiBrand] Get data error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos de IA: ' . $e->getMessage()
            ], 500);
        }
    }
}
