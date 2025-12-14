<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\AiUsageLog;
use App\Models\ConversationHistory;
use App\Models\Customer;
use App\Services\AiUsageService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AIController extends Controller
{
    /**
     * Handle the chat request.
     */
    public function chat(Request $request)
    {
        // Aumentar tiempo de ejecución para cadenas de pensamiento largas
        set_time_limit(120);

        $request->validate([
            'message' => 'required|string',
        ]);

        $userMessage = trim($request->input('message'));

        try {
            // 🔧 CÓDIGOS DE DESARROLLO (sin consumir tokens)
            $devCodes = [
                '123' => [
                    'reply' => '🔧 [DEV] Navegando a Gestión de Productos...',
                    'action' => [
                        'type' => 'navigate',
                        'payload' => [
                            'name' => 'POSModule',
                            'params' => ['module' => 'products']
                        ]
                    ]
                ],
                '456' => [
                    'reply' => '🔧 [DEV] Navegando a POS...',
                    'action' => [
                        'type' => 'navigate',
                        'payload' => [
                            'name' => 'POSModule',
                            'params' => ['module' => 'pos']
                        ]
                    ]
                ],
                '789' => [
                    'reply' => '🔧 [DEV] Navegando a Dashboard...',
                    'action' => [
                        'type' => 'navigate',
                        'payload' => [
                            'name' => 'POSModule',
                            'params' => ['module' => 'dashboard']
                        ]
                    ]
                ],
                '111' => [
                    'reply' => '🔧 [DEV] Navegando a Facturas...',
                    'action' => [
                        'type' => 'navigate',
                        'payload' => [
                            'name' => 'POSModule',
                            'params' => ['module' => 'invoices']
                        ]
                    ]
                ],
                '222' => [
                    'reply' => '🔧 [DEV] Navegando a Clientes...',
                    'action' => [
                        'type' => 'navigate',
                        'payload' => [
                            'name' => 'POSModule',
                            'params' => ['module' => 'customers']
                        ]
                    ]
                ],
                '333' => [
                    'reply' => '🔧 [DEV] Navegando a Reportes...',
                    'action' => [
                        'type' => 'navigate',
                        'payload' => [
                            'name' => 'POSModule',
                            'params' => ['module' => 'reports']
                        ]
                    ]
                ],
                'testuno' => [
                    'reply' => '🎯 [TEST] Creando descuento y enviando solo al 3134540533...',
                    'action' => null,
                    'execute_action' => [
                        'type' => 'create_campaign',
                        'params' => [
                            'discount' => [
                                'name' => 'Test Uno ' . date('His'),
                                'code' => 'UNO' . date('His'),
                                'type' => 'percentage',
                                'value' => 50,
                                'duration_days' => 2
                            ],
                            'whatsapp' => [
                                'message' => '🎁 Código exclusivo UNO' . date('His') . ' para 50% de descuento por 2 días',
                                'target' => 'specific',
                                'customer_ids' => [10]
                            ]
                        ]
                    ]
                ],
                'debug_wa' => [
                    'reply' => '🐛 [DEBUG] Enviando 1 mensaje WhatsApp a 3134540533 para depurar...',
                    'action' => null,
                    'execute_action' => [
                        'type' => 'send_whatsapp',
                        'params' => [
                            'message' => '🐛 Test de depuración WhatsApp',
                            'target' => 'specific',
                            'customer_ids' => [1]
                        ]
                    ]
                ],
                'test_whatsapp' => [
                    'reply' => '🔧 [DEV] Enviando WhatsApp a todos los clientes...\n\n⏳ Esto puede tomar unos segundos, por favor espera...',
                    'action' => null,
                    'execute_action' => [
                        'type' => 'send_whatsapp',
                        'params' => [
                            'message' => '¡Oferta especial! Usa el código KAEL891 para 100% de descuento solo hoy 🎁',
                            'target' => 'all'
                        ]
                    ]
                ],
                'test_discount' => [
                    'reply' => '🔧 [DEV] Creando descuento de prueba: TEST50 con 50% de descuento por 7 días...',
                    'action' => null,
                    'execute_action' => [
                        'type' => 'create_discount',
                        'params' => [
                            'name' => 'Descuento Prueba',
                            'code' => 'TEST50',
                            'type' => 'percentage',
                            'value' => 50,
                            'duration_days' => 7
                        ]
                    ]
                ],
                'test_campaign' => [
                    'reply' => '🔧 [DEV] Creando campaña completa: descuento + WhatsApp...',
                    'action' => null,
                    'execute_action' => [
                        'type' => 'create_campaign',
                        'params' => [
                            'discount' => [
                                'name' => 'Campaña Test ' . date('His'),
                                'code' => 'TEST' . date('His'),
                                'type' => 'percentage',
                                'value' => 30,
                                'duration_days' => 3
                            ],
                            'whatsapp' => [
                                'message' => '🎉 ¡Nueva promoción! Usa TEST' . date('His') . ' para 30% descuento por 3 días',
                                'target' => 'all'
                            ]
                        ]
                    ]
                ],
                '000' => [
                    'reply' => '🔧 [DEV] Códigos disponibles:\n\n📍 NAVEGACIÓN:\n• 123 = Productos\n• 456 = POS\n• 789 = Dashboard\n• 111 = Facturas\n• 222 = Clientes\n• 333 = Reportes\n\n🎯 ACCIONES:\n• testuno = Descuento + WhatsApp a 3134540533\n• debug_wa = WhatsApp test a 1 número\n• test_whatsapp = Enviar WhatsApp a todos\n• test_discount = Crear descuento TEST50\n• test_campaign = Campaña completa',
                    'action' => null
                ]
            ];

            // Verificar si es un código de desarrollo
            if (isset($devCodes[$userMessage])) {
                $userId = auth()->id() ?? $request->ip();
                $executionKey = "dev_code_{$userMessage}_" . md5($userId);

                $lastExecution = cache($executionKey);
                if ($lastExecution && (time() - $lastExecution) < 3) {
                    Log::warning("[DEV CODE] Ejecución duplicada bloqueada: {$userMessage}");
                    return response()->json([
                        'reply' => json_encode(['reply' => '⏳ Espera un momento antes de ejecutar de nuevo...']),
                        'status' => 'success'
                    ]);
                }

                cache([$executionKey => time()], 5);
                Log::info("[DEV CODE] Usuario usó código: {$userMessage}");

                $devResponse = $devCodes[$userMessage];

                if (isset($devResponse['execute_action'])) {
                    $actionResult = $this->executeAIAction($devResponse['execute_action']);
                    $devResponse['action_result'] = $actionResult;

                    if (isset($actionResult['message'])) {
                        $devResponse['reply'] .= "\n\n✅ " . $actionResult['message'];
                    }
                }

                return response()->json([
                    'reply' => json_encode($devResponse),
                    'status' => 'success'
                ]);
            }

            // 📝 GESTIÓN DE SESIÓN CONVERSACIONAL
            $sessionId = $request->input('session_id');
            if (!$sessionId) {
                $userId = auth()->id() ?? 'guest_' . $request->ip();
                $sessionId = 'sess_' . md5($userId . '_' . date('Ymd'));
            }

            // 🔒 PREVENIR DOBLES PETICIONES (Lock Mechanism)
            $lockKey = 'ai_chat_lock_' . $sessionId . '_' . md5($userMessage);
            if (Cache::has($lockKey)) {
                Log::warning("[AI Lock] Bloqueando petición duplicada: {$userMessage}");
                return response()->json([
                    'reply' => json_encode([
                        'reply' => '⏳ Ya estoy procesando este mensaje. Por favor espera un momento...',
                        'action' => null
                    ]),
                    'session_id' => $sessionId,
                    'status' => 'processing'
                ]);
            }
            Cache::put($lockKey, true, 60); // Bloqueo por 60 segundos

            // 1. Build lightweight system prompt (NO CONTEXT STUFFING!)
            $systemPrompt = $this->buildSystemPrompt();

            // 2. Recuperar historial de conversación (reducido para evitar confusión)
            $conversationHistory = ConversationHistory::getRecentMessages($sessionId, 3);

            // 3. Guardar mensaje del usuario
            ConversationHistory::create([
                'user_id' => auth()->id(),
                'session_id' => $sessionId,
                'role' => 'user',
                'content' => $userMessage,
            ]);

            // 4. Call AI with agent loop (handles tools automatically)
            $response = $this->callGroqAPI($systemPrompt, $userMessage, $conversationHistory);

            // 5. Guardar respuesta del asistente
            $aiResponse = json_decode($response, true);
            $assistantReply = $aiResponse['reply'] ?? 'Sin respuesta';
            ConversationHistory::create([
                'user_id' => auth()->id(),
                'session_id' => $sessionId,
                'role' => 'assistant',
                'content' => $assistantReply,
            ]);

            // Liberar bloqueo
            Cache::forget($lockKey);

            return response()->json([
                'reply' => $response,
                'session_id' => $sessionId,
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            Log::error('AI Chat Error: ' . $e->getMessage());
            if (isset($lockKey)) Cache::forget($lockKey);

            // 🔴 FIX: Guardar mensaje de error en historial para evitar "comandos fantasma"
            if (isset($sessionId)) {
                ConversationHistory::create([
                    'user_id' => auth()->id(),
                    'session_id' => $sessionId,
                    'role' => 'assistant',
                    'content' => 'Lo siento, tuve un problema técnico al procesar tu solicitud anterior.',
                ]);
            }

            return response()->json([
                'reply' => 'Lo siento, tuve un problema al procesar tu solicitud. Por favor intenta de nuevo.',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Build lightweight system prompt (NO CONTEXT STUFFING!)
     */
    private function buildSystemPrompt()
    {
        $currentDate = now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY');
        $currentTime = now()->format('H:i');

        return <<<EOT
Eres "105 IA", asistente virtual inteligente del sistema POS. Sé amigable, conversacional y muy útil.

🎯 TUS CAPACIDADES:
Tienes herramientas para consultar datos en tiempo real:
• search_products(query, filter, limit) - Buscar productos
• search_customers(query) - Buscar clientes
• search_categories(query) - Buscar categorías
• get_sales_report(period) - Estadísticas de ventas
• get_low_stock_products(limit) - Productos con stock bajo
• search_invoices(customer_name, date, invoice_id, invoice_number, limit) - Buscar facturas
• get_invoice_details(invoice_id) - Obtener información COMPLETA de una factura específica
• execute_action(action_type, params) - Ejecutar acciones (descuentos, WhatsApp, productos, etc)

🔧 REGLAS IMPORTANTES:

1. **NUNCA inventes datos** - Usa herramientas para obtener información real

2. **NAVEGACIÓN** (NO es herramienta - va en respuesta JSON):
   Frases: "llevame a X", "muestra X", "abre X", "ve a X", "ir a X", "quiero ver X"
   Módulos: products, pos, dashboard, invoices, customers, suppliers, categories, reports, settings

   Ejemplo:
   Usuario: "llevame a productos"
   Respuesta: {"reply": "¡Claro! Te llevo al módulo de productos 📦", "action": {"type": "navigate", "payload": {"name": "POSModule", "params": {"module": "products"}}}}

3. **CONSULTAS DE DATOS** (SÍ usa herramientas):
   Usuario: "muestra productos" → Llama search_products() → {"reply": "datos formateados", "action": null}
   Usuario: "ventas de hoy" → Llama get_sales_report('today') → {"reply": "estadísticas", "action": null}

   **FACTURAS - CONSULTAS INTELIGENTES**:
   • "¿cuántas facturas hay?" → search_invoices(date='all', limit=1000) → Contar resultados
   • "facturas de hoy" → search_invoices(date='today')
   • "facturas de ayer" → search_invoices(date='yesterday')
   • "facturas de la semana" → search_invoices(date='week')
   • "muestra la factura 5" o "detalles de la factura 5" → get_invoice_details(invoice_id=5)
   • "información de esa venta" (si ya mencionaste una factura) → get_invoice_details(invoice_id=ID_PREVIO)

   ⚠️ IMPORTANTE: Si el usuario pregunta por "información", "detalles" o "qué tiene" una factura, USA get_invoice_details(), NO search_invoices()

4. **CREAR PRODUCTOS** - SÉ PROACTIVO:
   Usuario: "crea producto X con categoría Y"

   SI FALTA INFO (especialmente CATEGORÍA) → Pídela específicamente:
   "Para crear el producto necesito: nombre, categoría, precio de venta, precio de costo, y stock inicial. ¿A qué categoría pertenece?"

   ⚠️ IMPORTANTE: SI NO DA CATEGORÍA EN EL MENSAJE ACTUAL: PREGUNTA.
   NO ASUMAS NINGUNA. NO uses el contexto de mensajes anteriores para adivinar la categoría.

   SI TIENE INFO COMPLETA:
   - Paso 1: Si usuario da nombre categoría → search_categories(query='nombre categoría') para obtener category_id
   - Paso 2: Si categoría no existe → Pregunta si quiere crearla o usar otra
   - Paso 3: execute_action('create_product', params con category_id numérico)
   - Paso 4: RESPONDE con éxito Y ACCIÓN DE NAVEGACIÓN para editar:
     {"reply": "¡Producto creado! Te abro el editor por si quieres ajustar algo.", "action": {"type": "navigate", "payload": {"name": "POSModule", "params": {"module": "products"}, "query": {"action": "edit", "id": ID_DEL_PRODUCTO}}}}

5. **ACCIONES MULTI-PASO**:
   Ejemplo: "crea descuento para Maria"
   - Paso 1: search_customers(query='Maria') → obtiene ID
   - Paso 2: execute_action('create_discount', params con customer_id)
   - Paso 3: RESPONDE con resultado (NO más herramientas)

6. **SALUDOS**: "hola", "hi", "buenos días" → Responde directamente sin herramientas

7. **DETENTE** cuando hayas obtenido lo que el usuario pidió

8. **ERRORES** - SÉ CLARO Y ÚTIL:
   Si algo falla → Explica QUÉ falló y QUÉ se necesita
   Ejemplo: "No puedo crear el producto porque la categoría 'Hogar' no existe en el sistema. ¿Quieres que la cree primero o usar otra categoría existente?"

9. **WhatsApp**: target debe ser "all", "active" o "specific" (con customer_ids)

10. **EDICIÓN DE PRODUCTOS**:
   - Si el usuario dice "abreme el editor", "editarlo", "modificarlo" justo después de crear o mencionar un producto:
     - Usa la acción `navigate` con `query: { "action": "edit", "id": [ID_DEL_PRODUCTO] }`.
     - El ID del producto recién creado estará en el historial de conversación o en tu memoria inmediata.
     - Ejemplo:
       Usuario: "abreme el editor"
       Respuesta: {
         "reply": "¡Claro! Abriendo el editor para 'Carne Molida' 📝",
         "action": {"type": "navigate", "payload": {"name": "POSModule", "params": {"module": "products"}, "query": {"action": "edit", "id": 39}}}
       }

11. **CREACIÓN PROACTIVA Y EDICIÓN OPCIONAL**:
   - Si el usuario pregunta "¿podemos crear X?" o "¿puedes crear X?", ASUME QUE ES UNA ORDEN.
   - NO preguntes "¿quieres que lo cree?". HAZLO.
   - Si faltan datos, créalo con valores por defecto (precio 0, stock 0) y avisa al usuario que puede editarlo después.
   - **IMPORTANTE**: Después de crear un producto, **NO** navegues automáticamente al editor.
   - En su lugar, usa `suggested_action` para mostrar un botón que diga "✏️ Editar Producto".
   - Así el usuario puede decidir si quiere editarlo o seguir conversando.
   - Ejemplo:
     Usuario: "podemos crear una categoria llamada empanadas"
     Respuesta: {
       "reply": "¡Listo! Categoría 'empanadas' creada ✅. ¿Quieres agregar productos a ella?",
       "execute_action": { "type": "create_category", "params": { "name": "empanadas" } }
     }

12. **FORMATO RESPUESTA**:
   SIEMPRE JSON: {"reply": "texto amigable y útil", "action": null o objeto_navegación, "suggested_action": { "type": "navigate", "label": "Texto del Botón", "payload": { ... } } o null}

13. **ERRORES PREVIOS**:
   - Si ves en el historial que tuviste un problema o error en el turno anterior, NO intentes reintentar la acción automáticamente a menos que el usuario lo pida de nuevo.
   - Prioriza SIEMPRE el último mensaje del usuario.
   - Si el usuario pregunta algo nuevo (ej: "¿cuántos productos hay?"), responde a ESO, no a la orden fallida anterior.

¡Sé inteligente, proactivo y ayuda al usuario a completar sus tareas!

Hoy: {$currentDate}, {$currentTime}
EOT;
    }

    /**
     * Get tools definition for Groq API
     */
    private function getToolsDefinition()
    {
        return [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'search_products',
                    'description' => 'Buscar productos en el inventario por nombre, ID o criterios de filtro. Usa esto cuando el usuario pregunte por productos, stock, o quiera ver el inventario.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'query' => [
                                'type' => 'string',
                                'description' => 'Término de búsqueda (nombre del producto o ID). Deja vacío para listar todos.'
                            ],
                            'filter' => [
                                'type' => 'string',
                                'enum' => ['all', 'low-stock', 'out-of-stock', 'active', 'inactive'],
                                'description' => 'Criterio de filtro'
                            ],
                            'limit' => [
                                'type' => 'integer',
                                'description' => 'Número máximo de resultados',
                                'default' => 10
                            ]
                        ],
                        'required' => []
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'search_customers',
                    'description' => 'Buscar clientes por nombre, teléfono, email o documento para obtener su ID.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'query' => [
                                'type' => 'string',
                                'description' => 'Término de búsqueda (nombre, teléfono, email o documento del cliente)'
                            ]
                        ],
                        'required' => ['query']
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_sales_report',
                    'description' => 'Obtener estadísticas de ventas para un período específico de tiempo',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'period' => [
                                'type' => 'string',
                                'enum' => ['today', 'yesterday', 'week', 'month', 'last_7_days'],
                                'description' => 'Período de tiempo para el reporte'
                            ]
                        ],
                        'required' => ['period']
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_low_stock_products',
                    'description' => 'Obtener lista de productos con stock en o por debajo del mínimo',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'limit' => [
                                'type' => 'integer',
                                'description' => 'Número máximo de resultados',
                                'default' => 20
                            ]
                        ],
                        'required' => []
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'search_categories',
                    'description' => 'Buscar categorías por nombre para obtener su ID.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'query' => [
                                'type' => 'string',
                                'description' => 'Término de búsqueda (nombre de la categoría). Deja vacío para listar todas.'
                            ]
                        ],
                        'required' => []
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'search_invoices',
                    'description' => 'Buscar facturas por cliente, fecha, número de factura o ID. Usa esto para listar facturas o encontrar una específica.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'customer_name' => [
                                'type' => 'string',
                                'description' => 'Nombre del cliente (búsqueda parcial)'
                            ],
                            'date' => [
                                'type' => 'string',
                                'enum' => ['today', 'yesterday', 'week', 'month', 'last_7_days', 'last_30_days', 'all'],
                                'description' => 'Filtrar por período de tiempo'
                            ],
                            'invoice_id' => [
                                'type' => 'integer',
                                'description' => 'ID específico de la factura'
                            ],
                            'invoice_number' => [
                                'type' => 'string',
                                'description' => 'Número de factura (ej: FACT-0001)'
                            ],
                            'limit' => [
                                'type' => 'integer',
                                'description' => 'Número máximo de resultados',
                                'default' => 20
                            ]
                        ],
                        'required' => []
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_invoice_details',
                    'description' => 'Obtener información COMPLETA de una factura específica incluyendo productos, cantidades, precios, cliente, fecha, etc. Usa esto cuando el usuario pida "información", "detalles", "qué tiene" o "mostrar" una factura.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'invoice_id' => [
                                'type' => 'integer',
                                'description' => 'ID de la factura a consultar'
                            ]
                        ],
                        'required' => ['invoice_id']
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'execute_action',
                    'description' => 'Ejecutar acciones de negocio. Si requiere customer_ids, busca primero al cliente. DESPUÉS DE EJECUTAR, responde al usuario.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'action_type' => [
                                'type' => 'string',
                                'enum' => ['create_discount', 'send_whatsapp', 'create_campaign', 'create_product', 'create_category'],
                                'description' => 'Tipo de acción a ejecutar'
                            ],
                            'params' => [
                                'type' => 'object',
                                'description' => 'Parámetros específicos. create_product: {name, category_id, sale_price, cost_price, current_stock}. create_discount: {name, code, type, value, duration_days}. send_whatsapp: {message, target, customer_ids}.'
                            ]
                        ],
                        'required' => ['action_type', 'params']
                    ]
                ]
            ]
        ];
    }

    /**
     * Call Groq API with agent loop (handles tools automatically)
     */
private function callGroqAPI($systemPrompt, $userMessage, $conversationHistory = [], $maxIterations = 15)
    {
        // Build initial messages array
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Add conversation history
        if (!empty($conversationHistory)) {
            $messages = array_merge($messages, $conversationHistory);
        }

        // Add current user message
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $iterations = 0;

        while ($iterations < $maxIterations) {
            $iterations++;

            Log::info("[Agent Loop] Iteration {$iterations}/{$maxIterations}");

            // Call Groq API with tools
            $response = $this->callGroqAPIWithTools($messages);

            if (!$response) {
                Log::error("[Agent Loop] API call failed");
                break;
            }

            $message = $response['choices'][0]['message'];
            $finishReason = $response['choices'][0]['finish_reason'];

            Log::info("[Agent Loop] Finish reason: {$finishReason}");

            // Add assistant message to conversation
            $messages[] = $message;

            // Check if AI wants to call a tool
            if ($finishReason === 'tool_calls' && isset($message['tool_calls'])) {
                Log::info("[Agent Loop] AI requested " . count($message['tool_calls']) . " tool call(s)");

                // Execute each tool call
                foreach ($message['tool_calls'] as $toolCall) {
                    $toolName = $toolCall['function']['name'];
                    $toolArgs = json_decode($toolCall['function']['arguments'], true);

                    Log::info("[Tool Call] {$toolName}", $toolArgs ?? []);

                    // Execute the tool
                    $toolResult = $this->executeToolCall($toolName, $toolArgs ?? []);

                    Log::info("[Tool Result] {$toolName}", ['result' => $toolResult]);

                    // Add tool result to messages
                    $messages[] = [
                        'role' => 'tool',
                        'tool_call_id' => $toolCall['id'],
                        'name' => $toolName,
                        'content' => json_encode($toolResult, JSON_UNESCAPED_UNICODE)
                    ];
                }

                // Continue loop to let AI process tool results
                continue;
            }

            // If finish_reason is 'stop', AI has final response
            if ($finishReason === 'stop') {
                $finalContent = $message['content'] ?? '';

                Log::info("[Agent Loop] Final response received");

                if (!$finalContent) {
                    return json_encode([
                        'reply' => 'No pude generar una respuesta. Por favor intenta de nuevo.',
                        'action' => null
                    ]);
                }

                // Verify it's valid JSON
                $decoded = json_decode($finalContent, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $finalContent;
                }

                // If not valid JSON, wrap it
                return json_encode([
                    'reply' => $finalContent,
                    'action' => null
                ], JSON_UNESCAPED_UNICODE);
            }

            // Handle other finish reasons
            Log::warning("[Agent Loop] Unexpected finish_reason: {$finishReason}");
            break;
        }

        // Max iterations reached or error occurred
        Log::warning("[Agent Loop] Ended after {$iterations} iterations");
        return json_encode([
            'reply' => 'Tuve un problema procesando tu solicitud compleja. ¿Podrías intentarlo de nuevo o simplificarla?',
            'action' => null
        ]);
    }

    /**
     * Call Groq API with tools definition
     */
    private function callGroqAPIWithTools($messages)
    {
        // Sistema de rotación de múltiples API Keys
        $apiKeys = array_filter([
            env('GROQ_API_KEY_1'),
            env('GROQ_API_KEY_2'),
            env('GROQ_API_KEY_3'),
            env('GROQ_API_KEY_4'),
            env('GROQ_API_KEY_5'),
            env('GROQ_API_KEY_6'),
            env('GROQ_API_KEY_7'),
            env('GROQ_API_KEY_8'),
            env('GROQ_API_KEY_9'),
            env('GROQ_API_KEY_10'),
        ]);

        if (empty($apiKeys)) {
            Log::error('[Groq API] No API keys configured');
            return null;
        }

        $startTime = microtime(true);
        $lastError = null;

        foreach ($apiKeys as $index => $apiKey) {
            $keyIndex = $index + 1;
            $keyLast4 = substr($apiKey, -4);

            Log::info("[Groq API] Trying API Key #{$keyIndex}");

            try {
                $response = Http::timeout(30)->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile',
                    'messages' => $messages,
                    'tools' => $this->getToolsDefinition(),
                    'tool_choice' => 'auto',
                    'temperature' => 0.3,
                    'max_tokens' => 1000,
                ]);

                $responseTime = (int)((microtime(true) - $startTime) * 1000);

                if ($response->successful()) {
                    Log::info("[Groq API] ✅ Success with API Key #{$keyIndex}");

                    $responseData = $response->json();

                    // Extract usage metrics
                    $usage = $responseData['usage'] ?? [];
                    $promptTokens = $usage['prompt_tokens'] ?? 0;
                    $completionTokens = $usage['completion_tokens'] ?? 0;
                    $totalTokens = $usage['total_tokens'] ?? 0;

                    Log::info("[Token Usage] Prompt: {$promptTokens}, Completion: {$completionTokens}, Total: {$totalTokens}");

                    // Log usage
                    try {
                        $lastUserMessage = '';
                        foreach (array_reverse($messages) as $msg) {
                            if (isset($msg['role']) && $msg['role'] === 'user') {
                                $lastUserMessage = $msg['content'] ?? '';
                                break;
                            }
                        }

                        // Registrar en tabla tenant (histórico local)
                        AiUsageLog::create([
                            'user_id' => auth()->id(),
                            'api_key_index' => $keyIndex,
                            'api_key_last_4' => $keyLast4,
                            'user_message' => substr($lastUserMessage, 0, 1000),
                            'prompt_tokens' => $promptTokens,
                            'completion_tokens' => $completionTokens,
                            'total_tokens' => $totalTokens,
                            'status' => 'success',
                            'response_time_ms' => $responseTime,
                            'model' => 'llama-3.3-70b-versatile',
                            'endpoint' => 'chat',
                            'ip_address' => request()->ip(),
                        ]);

                        // ✅ Registrar en tabla central para límites y facturación
                        $aiUsageService = app(AiUsageService::class);
                        $cost = ($totalTokens / 1000000) * 0.50; // Costo aproximado
                        $aiUsageService->logUsage(tenant('id'), $totalTokens, $cost);
                    } catch (\Exception $e) {
                        Log::error("[AI Usage Log] Error: " . $e->getMessage());
                    }

                    return $responseData;
                }

                // Handle errors
                $statusCode = $response->status();
                $errorBody = $response->body();

                if ($statusCode === 429) {
                    Log::warning("[Groq API] ⚠️ Rate limit on API Key #{$keyIndex}");

                    try {
                        AiUsageLog::create([
                            'user_id' => auth()->id(),
                            'api_key_index' => $keyIndex,
                            'api_key_last_4' => $keyLast4,
                            'user_message' => '',
                            'status' => 'rate_limited',
                            'error_message' => 'Rate limit exceeded',
                            'response_time_ms' => $responseTime,
                            'model' => 'llama-3.3-70b-versatile',
                            'endpoint' => 'chat',
                            'ip_address' => request()->ip(),
                        ]);
                    } catch (\Exception $e) {
                        Log::error("[AI Usage Log] Error: " . $e->getMessage());
                    }

                    $lastError = "Rate limit exceeded";
                    continue;
                }

                Log::error("[Groq API] ❌ Error {$statusCode} with API Key #{$keyIndex}: {$errorBody}");

                try {
                    AiUsageLog::create([
                        'user_id' => auth()->id(),
                        'api_key_index' => $keyIndex,
                        'api_key_last_4' => $keyLast4,
                        'user_message' => '',
                        'status' => 'error',
                        'error_message' => substr($errorBody, 0, 1000),
                        'response_time_ms' => $responseTime,
                        'model' => 'llama-3.3-70b-versatile',
                        'endpoint' => 'chat',
                        'ip_address' => request()->ip(),
                    ]);
                } catch (\Exception $e) {
                    Log::error("[AI Usage Log] Error: " . $e->getMessage());
                }

                $lastError = $errorBody;
                continue;

            } catch (\Exception $e) {
                Log::error("[Groq API] Exception with API Key #{$keyIndex}: " . $e->getMessage());
                $lastError = $e->getMessage();
                continue;
            }
        }

        // All API keys failed
        Log::error("[Groq API] ❌ All API keys exhausted. Last error: {$lastError}");
        return null;
    }

    /**
     * Execute a tool call requested by the AI
     */
    private function executeToolCall($toolName, $args)
    {
        try {
            switch ($toolName) {
                case 'search_products':
                    return $this->toolSearchProducts($args);

                case 'search_customers':
                return $this->toolSearchCustomers($args);

            case 'search_categories':
                return $this->toolSearchCategories($args);

                case 'get_sales_report':
                    return $this->toolGetSalesReport($args);

                case 'get_low_stock_products':
                    return $this->toolGetLowStockProducts($args);

                case 'search_invoices':
                    return $this->toolSearchInvoices($args);

                case 'get_invoice_details':
                    return $this->toolGetInvoiceDetails($args);

                case 'execute_action':
                    return $this->toolExecuteAction($args);

                default:
                    return ['error' => 'Unknown tool: ' . $toolName];
            }
        } catch (\Exception $e) {
            Log::error("[Tool Execution Error] {$toolName}: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Tool: Search products
     */
    private function toolSearchProducts($args)
    {
        $query = $args['query'] ?? '';
        $filter = $args['filter'] ?? 'all';
        $limit = $args['limit'] ?? 10;

        $productsQuery = Product::query();

        // Apply search
        if ($query) {
            $productsQuery->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('id', $query);
            });
        }

        // Apply filter
        switch ($filter) {
            case 'low-stock':
                $productsQuery->where('manage_stock', true)
                             ->whereRaw('current_stock <= min_stock');
                break;
            case 'out-of-stock':
                $productsQuery->where('current_stock', '<=', 0);
                break;
            case 'active':
                $productsQuery->where('active', true);
                break;
            case 'inactive':
                $productsQuery->where('active', false);
                break;
        }

        $products = $productsQuery->limit($limit)
            ->get(['id', 'name', 'sale_price', 'cost_price', 'current_stock', 'min_stock', 'active'])
            ->toArray();

        return [
            'products' => $products,
            'count' => count($products),
            'filter_applied' => $filter
        ];
    }

    /**
     * Tool: Search customers
     */
    private function toolSearchCustomers($args)
    {
        $query = $args['query'] ?? '';

        if (empty($query)) {
            return ['error' => 'Query parameter is required'];
        }

        $customers = Customer::where(function($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%")
              ->orWhere('email', 'LIKE', "%{$query}%")
              ->orWhere('phone', 'LIKE', "%{$query}%")
              ->orWhere('document_number', 'LIKE', "%{$query}%");
        })
        ->limit(10)
        ->get(['id', 'name', 'email', 'phone', 'document_number'])
        ->toArray();

        return [
            'customers' => $customers,
            'count' => count($customers)
        ];
    }

    /**
     * Tool: Search categories
     */
    private function toolSearchCategories($args)
    {
        try {
            $query = $args['query'] ?? '';

            $categories = \App\Models\Category::query()
                ->when($query, function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                })
                ->limit(20)
                ->get(['id', 'name', 'description']);

            return [
                'result' => [
                    'categories' => $categories->toArray(),
                    'count' => $categories->count()
                ]
            ];
        } catch (\Exception $e) {
            Log::error('[Tool] search_categories error: ' . $e->getMessage());
            return ['result' => ['categories' => [], 'count' => 0, 'error' => $e->getMessage()]];
        }
    }

    /**
     * Tool: Get sales report
     */
    private function toolGetSalesReport($args)
    {
        $period = $args['period'] ?? 'today';

        $dateQuery = match($period) {
            'today' => [['date', '=', Carbon::today()]],
            'yesterday' => [['date', '=', Carbon::yesterday()]],
            'week' => [['date', '>=', Carbon::now()->startOfWeek()]],
            'month' => [['date', '>=', Carbon::now()->startOfMonth()]],
            'last_7_days' => [['date', '>=', Carbon::now()->subDays(7)]],
            default => [['date', '=', Carbon::today()]]
        };

        $total = Invoice::where('type', 'invoice')
            ->where($dateQuery)
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        $count = Invoice::where('type', 'invoice')
            ->where($dateQuery)
            ->where('status', '!=', 'cancelled')
            ->count();

        // Get top selling products for this period
        $dateFilter = match($period) {
            'today' => Carbon::today(),
            'yesterday' => Carbon::yesterday(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'last_7_days' => Carbon::now()->subDays(7),
            default => Carbon::today()
        };

        $topProducts = InvoiceItem::whereHas('invoice', function($query) use ($dateFilter, $period) {
                if (in_array($period, ['today', 'yesterday'])) {
                    $query->where('type', 'invoice')
                          ->whereDate('date', $dateFilter)
                          ->where('status', '!=', 'cancelled');
                } else {
                    $query->where('type', 'invoice')
                          ->where('date', '>=', $dateFilter)
                          ->where('status', '!=', 'cancelled');
                }
            })
            ->selectRaw('product_name, sum(quantity) as total_qty, sum(unit_price * quantity) as total_revenue')
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->toArray();

        return [
            'period' => $period,
            'total_sales' => $total,
            'invoice_count' => $count,
            'average_ticket' => $count > 0 ? round($total / $count, 2) : 0,
            'top_products' => $topProducts
        ];
    }

    /**
     * Tool: Get low stock products
     */
    private function toolGetLowStockProducts($args)
    {
        $limit = $args['limit'] ?? 20;

        $products = Product::where('manage_stock', true)
            ->whereRaw('current_stock <= min_stock')
            ->limit($limit)
            ->get(['id', 'name', 'current_stock', 'min_stock', 'sale_price'])
            ->toArray();

        return [
            'products' => $products,
            'count' => count($products)
        ];
    }

    /**
     * Tool: Search invoices
     */
    private function toolSearchInvoices($args)
    {
        $customerName = $args['customer_name'] ?? null;
        $date = $args['date'] ?? null;
        $invoiceId = $args['invoice_id'] ?? null;
        $invoiceNumber = $args['invoice_number'] ?? null;
        $limit = $args['limit'] ?? 20;

        $query = Invoice::with('customer:id,name')
            ->where('type', 'invoice');

        // Filter by specific invoice ID
        if ($invoiceId) {
            $query->where('id', $invoiceId);
        }

        // Filter by invoice number
        if ($invoiceNumber) {
            $query->where('number', 'LIKE', "%{$invoiceNumber}%");
        }

        // Filter by date
        if ($date) {
            switch ($date) {
                case 'today':
                    $query->whereDate('date', Carbon::today());
                    break;
                case 'yesterday':
                    $query->whereDate('date', Carbon::yesterday());
                    break;
                case 'week':
                    $query->where('date', '>=', Carbon::now()->startOfWeek());
                    break;
                case 'month':
                    $query->where('date', '>=', Carbon::now()->startOfMonth());
                    break;
                case 'last_7_days':
                    $query->where('date', '>=', Carbon::now()->subDays(7));
                    break;
                case 'last_30_days':
                    $query->where('date', '>=', Carbon::now()->subDays(30));
                    break;
                case 'all':
                    // No filter
                    break;
            }
        }

        // Filter by customer name
        if ($customerName) {
            $query->whereHas('customer', function($q) use ($customerName) {
                $q->where('name', 'LIKE', "%{$customerName}%");
            });
        }

        $invoices = $query->limit($limit)
            ->orderBy('date', 'desc')
            ->get(['id', 'number', 'customer_id', 'date', 'total', 'status', 'payment_method'])
            ->map(function($invoice) {
                return [
                    'id' => $invoice->id,
                    'number' => $invoice->number,
                    'customer_name' => $invoice->customer?->name ?? 'Cliente Final',
                    'date' => $invoice->date,
                    'total' => $invoice->total,
                    'status' => $invoice->status,
                    'payment_method' => $invoice->payment_method
                ];
            })
            ->toArray();

        return [
            'invoices' => $invoices,
            'count' => count($invoices)
        ];
    }

    /**
     * Tool: Get invoice details
     */
    private function toolGetInvoiceDetails($args)
    {
        $invoiceId = $args['invoice_id'] ?? null;

        if (!$invoiceId) {
            return ['error' => 'invoice_id es requerido'];
        }

        $invoice = Invoice::with(['customer', 'items.product'])
            ->where('type', 'invoice')
            ->find($invoiceId);

        if (!$invoice) {
            return ['error' => "No se encontró la factura con ID {$invoiceId}"];
        }

        return [
            'invoice' => [
                'id' => $invoice->id,
                'number' => $invoice->number,
                'customer_name' => $invoice->customer?->name ?? 'Cliente Final',
                'customer_phone' => $invoice->customer?->phone,
                'customer_email' => $invoice->customer?->email,
                'date' => $invoice->date,
                'subtotal' => $invoice->subtotal,
                'tax' => $invoice->tax,
                'discount' => $invoice->discount,
                'total' => $invoice->total,
                'status' => $invoice->status,
                'payment_method' => $invoice->payment_method,
                'notes' => $invoice->notes,
                'items' => $invoice->items->map(function($item) {
                    return [
                        'product_name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'subtotal' => $item->quantity * $item->unit_price,
                        'tax' => $item->tax,
                        'discount' => $item->discount
                    ];
                })->toArray()
            ]
        ];
    }

    /**
     * Tool: Execute action
     */
    private function toolExecuteAction($args)
    {
        $actionType = $args['action_type'] ?? null;
        $params = $args['params'] ?? [];

        if (!$actionType) {
            return ['error' => 'action_type is required'];
        }

        // Reuse existing execution logic
        $result = $this->executeAIAction([
            'type' => $actionType,
            'params' => $params
        ]);

        return $result;
    }

    /**
     * Execute AI action (reused from old system)
     */
    private function executeAIAction($actionData)
    {
        try {
            $actionType = $actionData['type'] ?? null;

            switch ($actionType) {
                case 'create_discount':
                    return $this->createDiscountAction($actionData['params'] ?? []);

                case 'send_whatsapp':
                    return $this->sendWhatsAppAction($actionData['params'] ?? []);

                case 'create_campaign':
                    return $this->createCampaignAction($actionData['params'] ?? []);

                case 'create_product':
                    return $this->createProductAction($actionData['params'] ?? []);

                case 'create_category':
                    return $this->createCategoryAction($actionData['params'] ?? []);

                default:
                    return [
                        'success' => false,
                        'message' => 'Tipo de acción no reconocido'
                    ];
            }
        } catch (\Exception $e) {
            Log::error('Error ejecutando acción de IA:', [
                'action' => $actionData,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error ejecutando acción: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create discount action
     */
    private function createDiscountAction($params)
    {
        $controller = new \App\Http\Controllers\Api\AIActionsController();
        $request = new \Illuminate\Http\Request($params);
        $response = $controller->createDiscount($request);

        return $response->getData(true);
    }

    /**
     * Send WhatsApp action
     */
    private function sendWhatsAppAction($params)
    {
        $controller = new \App\Http\Controllers\Api\AIActionsController();
        $request = new \Illuminate\Http\Request($params);
        $response = $controller->sendBulkWhatsApp($request);

        return $response->getData(true);
    }

    /**
     * Create campaign action
     */
    private function createCampaignAction($params)
    {
        $controller = new \App\Http\Controllers\Api\AIActionsController();
        $request = new \Illuminate\Http\Request($params);
        $response = $controller->createCampaign($request);

        return $response->getData(true);
    }

    /**
     * Create product action
     */
    private function createProductAction($params)
    {
        $controller = new \App\Http\Controllers\Api\AIActionsController();
        $request = new \Illuminate\Http\Request($params);
        $response = $controller->createProduct($request);

        return $response->getData(true);
    }

    /**
     * Create category action
     */
    private function createCategoryAction($params)
    {
        $controller = new \App\Http\Controllers\Api\AIActionsController();
        $request = new \Illuminate\Http\Request($params);
        $response = $controller->createCategory($request);

        return $response->getData(true);
    }

    /**
     * Clear conversation history
     */
    public function clearHistory(Request $request)
    {
        try {
            $sessionId = $request->input('session_id');

            if (!$sessionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'session_id es requerido'
                ], 400);
            }

            $deleted = ConversationHistory::where('session_id', $sessionId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Historial limpiado correctamente',
                'deleted_messages' => $deleted
            ]);

        } catch (\Exception $e) {
            Log::error('Error limpiando historial: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar historial'
            ], 500);
        }
    }
}
