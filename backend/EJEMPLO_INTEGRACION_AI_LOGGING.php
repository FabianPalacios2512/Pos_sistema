<?php

/**
 * EJEMPLO DE INTEGRACIÓN DEL LOGGING DE IA
 *
 * Este archivo muestra cómo integrar el logging de consumo de IA
 * en el controlador AIController existente.
 *
 * NO COPIES TODO ESTE ARCHIVO, solo agrega las partes necesarias
 * a tu AIController.php actual.
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Central\CentralAiUsageLog; // ← NUEVO: Importar modelo central

class AIController extends Controller
{
    /**
     * EJEMPLO: Chat con IA y logging de tokens
     */
    public function chat(Request $request)
    {
        $userMessage = trim($request->input('message'));

        try {
            // ... tu lógica de IA existente ...

            // Llamada a OpenAI
            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => $messages,
                    // ... otros parámetros
                ]);

            $data = $response->json();

            // ✅ NUEVO: Registrar consumo de IA en BD central
            if (isset($data['usage'])) {
                CentralAiUsageLog::logUsage(
                    actionType: 'chat',
                    tokensUsed: $data['usage']['total_tokens'] ?? 0,
                    modelUsed: $data['model'] ?? 'gpt-4o-mini',
                    promptSummary: substr($userMessage, 0, 100),
                    metadata: [
                        'user_id' => auth()->id(),
                        'prompt_tokens' => $data['usage']['prompt_tokens'] ?? 0,
                        'completion_tokens' => $data['usage']['completion_tokens'] ?? 0,
                    ]
                );
            }

            // Continuar con tu lógica...
            return response()->json([
                'success' => true,
                'reply' => $data['choices'][0]['message']['content'],
            ]);

        } catch (\Exception $e) {
            Log::error('AI Chat Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Error al procesar tu mensaje'
            ], 500);
        }
    }

    /**
     * EJEMPLO: Análisis de datos con IA
     */
    public function analyzeSales(Request $request)
    {
        try {
            // Tu lógica de análisis...
            $analysisPrompt = "Analiza las siguientes ventas...";

            $response = Http::post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'Eres un analista de ventas experto'],
                    ['role' => 'user', 'content' => $analysisPrompt]
                ],
            ]);

            $data = $response->json();

            // ✅ Registrar consumo
            if (isset($data['usage'])) {
                CentralAiUsageLog::logUsage(
                    actionType: 'analysis',     // ← Tipo diferente
                    tokensUsed: $data['usage']['total_tokens'] ?? 0,
                    modelUsed: $data['model'] ?? 'gpt-4o-mini',
                    promptSummary: 'Análisis de ventas',
                    metadata: [
                        'analysis_type' => 'sales',
                        'period' => $request->input('period'),
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'analysis' => $data['choices'][0]['message']['content'],
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * EJEMPLO: Recomendaciones de inventario
     */
    public function generateRecommendations(Request $request)
    {
        try {
            // ... tu lógica ...

            $response = Http::post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o',  // ← Modelo más costoso
                // ... configuración
            ]);

            $data = $response->json();

            // ✅ Registrar con modelo diferente
            if (isset($data['usage'])) {
                CentralAiUsageLog::logUsage(
                    actionType: 'recommendation',
                    tokensUsed: $data['usage']['total_tokens'] ?? 0,
                    modelUsed: 'gpt-4o',  // ← El costo se calcula automáticamente
                    promptSummary: 'Recomendaciones de inventario',
                );
            }

            return response()->json([
                'success' => true,
                'recommendations' => $data['choices'][0]['message']['content'],
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

/**
 * ============================================
 * TIPOS DE ACCIÓN DISPONIBLES
 * ============================================
 *
 * Usa estos valores para el parámetro 'actionType':
 *
 * - 'chat'           → Conversación general
 * - 'analysis'       → Análisis de datos
 * - 'recommendation' → Recomendaciones automáticas
 * - 'report'         → Generación de reportes
 * - 'optimization'   → Optimización de inventario
 * - 'prediction'     → Predicciones de ventas
 * - 'automation'     → Tareas automáticas
 * - 'search'         → Búsquedas avanzadas
 *
 * Puedes crear tus propios tipos personalizados.
 */

/**
 * ============================================
 * MODELOS Y COSTOS
 * ============================================
 *
 * El sistema calcula automáticamente el costo según el modelo:
 *
 * Modelo           Costo/1K tokens    Uso Recomendado
 * ─────────────────────────────────────────────────────
 * gpt-3.5-turbo    $0.0005           Tareas simples
 * gpt-4o-mini      $0.00015          Balance costo/calidad ✅
 * gpt-4o           $0.005            Tareas complejas
 * gpt-4-turbo      $0.01             Muy complejas
 * gpt-4            $0.03             Legacy
 */
