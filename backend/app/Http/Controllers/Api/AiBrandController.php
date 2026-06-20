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
            $slot = (int) $request->input('slot', -1);

            // Get current template and store_type
            $currentConfig = DB::table('web_catalog_configs')
                ->where('tenant_id', $tenantId)
                ->first();
            $currentTemplate = $currentConfig->template ?? null;

            $service = new GroqBrandService();
            $result = $service->generateBrandIdentity($description, $storeName, null, 'general', $slot);

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
     * Apply AI-generated settings to the catalog config.
     * Accepts optional `brand_data` payload to save a specific design (used in the 5-design carousel).
     * If `brand_data` is not provided, reads from what is already stored in DB.
     */
    public function apply(Request $request)
    {
        $request->validate([
            'apply_colors'  => 'boolean',
            'apply_template'=> 'boolean',
            'brand_data'    => 'nullable|array',
        ]);

        try {
            $tenantId = tenant('id');

            // If frontend sent brand_data (chosen from carousel), save it first
            if ($request->has('brand_data') && is_array($request->input('brand_data'))) {
                $chosen = $request->input('brand_data');
                $service = new \App\Services\GroqBrandService();
                $service->saveBrandIdentity(
                    $tenantId,
                    $chosen,
                    $chosen['business_description'] ?? ''
                );
            }

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
                    'ecommerce_features' => json_decode($config->ai_ecommerce_features ?? 'null', true),
                    'fake_reviews' => json_decode($config->ai_fake_reviews ?? 'null', true),
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

    /**
     * Reset all AI-generated brand data to blank state
     */
    public function reset()
    {
        try {
            $tenantId = tenant('id');

            DB::table('web_catalog_configs')
                ->where('tenant_id', $tenantId)
                ->update([
                    'business_description'    => null,
                    'ai_color_palette'        => null,
                    'ai_fonts'                => null,
                    'ai_recommended_template' => null,
                    'ai_banner_texts'         => null,
                    'ai_about_us'             => null,
                    'ai_value_messages'       => null,
                    'ai_announcements'        => null,
                    'ai_cross_sell_messages'  => null,
                    'ai_layout_config'        => null,
                    'ai_generated_at'         => null,
                    'updated_at'              => now(),
                ]);

            Cache::forget("web_catalog_config_{$tenantId}");

            return response()->json([
                'success' => true,
                'message' => 'Identidad visual restablecida correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('[AiBrand] Reset error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al restablecer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore AI brand data from a backup JSON payload
     */
    public function restore(Request $request)
    {
        $request->validate([
            'version'              => 'required|string',
            'exported_at'          => 'required|string',
            'business_description' => 'nullable|string',
            'color_palette'        => 'nullable|array',
            'fonts'                => 'nullable|array',
            'recommended_template' => 'nullable|string|max:100',
            'banner_texts'         => 'nullable|array',
            'about_us'             => 'nullable|string',
            'value_messages'       => 'nullable|array',
            'announcements'        => 'nullable|array',
            'cross_sell_messages'  => 'nullable|array',
            'layout_config'        => 'nullable|array',
        ]);

        try {
            $tenantId = tenant('id');

            $updates = [
                'business_description'    => $request->input('business_description'),
                'ai_color_palette'        => $request->input('color_palette') !== null
                    ? json_encode($request->input('color_palette')) : null,
                'ai_fonts'                => $request->input('fonts') !== null
                    ? json_encode($request->input('fonts')) : null,
                'ai_recommended_template' => $request->input('recommended_template'),
                'ai_banner_texts'         => $request->input('banner_texts') !== null
                    ? json_encode($request->input('banner_texts')) : null,
                'ai_about_us'             => $request->input('about_us'),
                'ai_value_messages'       => $request->input('value_messages') !== null
                    ? json_encode($request->input('value_messages')) : null,
                'ai_announcements'        => $request->input('announcements') !== null
                    ? json_encode($request->input('announcements')) : null,
                'ai_cross_sell_messages'  => $request->input('cross_sell_messages') !== null
                    ? json_encode($request->input('cross_sell_messages')) : null,
                'ai_layout_config'        => $request->input('layout_config') !== null
                    ? json_encode($request->input('layout_config')) : null,
                'ai_generated_at'         => $request->input('exported_at')
                    ? \Carbon\Carbon::parse($request->input('exported_at'))->format('Y-m-d H:i:s')
                    : null,
                'updated_at'              => now(),
            ];

            // Apply primary color from restored palette
            $palette = $request->input('color_palette');
            if (is_array($palette) && isset($palette['primary'])) {
                $updates['primary_color'] = $palette['primary'];
            }

            // Apply restored template
            if ($request->input('recommended_template')) {
                $updates['template'] = $request->input('recommended_template');
            }

            $exists = DB::table('web_catalog_configs')->where('tenant_id', $tenantId)->exists();
            if ($exists) {
                DB::table('web_catalog_configs')->where('tenant_id', $tenantId)->update($updates);
            } else {
                DB::table('web_catalog_configs')->insert(array_merge($updates, [
                    'tenant_id'  => $tenantId,
                    'created_at' => now(),
                ]));
            }

            Cache::forget("web_catalog_config_{$tenantId}");

            return response()->json([
                'success' => true,
                'message' => 'Identidad visual restaurada correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('[AiBrand] Restore error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar: ' . $e->getMessage()
            ], 500);
        }
    }
}
