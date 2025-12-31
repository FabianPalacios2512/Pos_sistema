<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\ConversationHistory;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Supplier;
use Carbon\Carbon;

class GeminiAgentService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
    protected $sessionId;
    protected $maxHistoryMessages = 10; // Cuántos mensajes recordar

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    /**
     * Obtiene o crea el session_id para el usuario actual
     */
    private function getSessionId()
    {
        $userId = auth()->id() ?? 'guest_' . request()->ip();
        return 'gemini_sess_' . md5($userId . '_' . date('Ymd'));
    }

    /**
     * Construye el System Instruction para Gemini
     */
    private function buildSystemInstruction()
    {
        $currentDate = now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY');
        $currentTime = now()->format('H:i');

        // Obtener plan actual del tenant
        $tenantPlan = 'free_trial';
        try {
            if (function_exists('tenant')) {
                $tenant = tenant();
                $tenantPlan = $tenant ? ($tenant->plan ?? 'free_trial') : 'free_trial';
            }
        } catch (\Exception $e) {
            Log::warning('No se pudo obtener el plan del tenant: ' . $e->getMessage());
        }

        $planInfo = match($tenantPlan) {
            'free_trial' => 'TRIAL (Prueba Gratuita) - Funciones básicas limitadas.',
            'basic' => 'BÁSICO - Facturación e inventario simple. Sin multisede ni créditos.',
            'premium' => 'PREMIUM - Multisede, Creditienda (deudas), Reportes avanzados.',
            'enterprise' => 'ENTERPRISE - Todo ilimitado + API + Soporte prioritario.',
            default => 'BÁSICO'
        };

        return <<<EOT
Eres "105 IA", el asistente virtual inteligente del sistema POS 105. Eres amigable, profesional y muy útil.

🏢 CONTEXTO DEL NEGOCIO:
- Plan Actual: **{$tenantPlan}** ({$planInfo})
- Fecha actual: {$currentDate}
- Hora actual: {$currentTime}

🎯 TUS CAPACIDADES (Herramientas disponibles):
• crearProducto - Crear productos nuevos en el inventario
• consultarInventario - Buscar productos por nombre
• obtenerEstadisticas - Ver estadísticas generales del inventario
• actualizarProducto - Modificar precio o stock de productos
• eliminarProducto - Eliminar productos del inventario
• consultarVentas - Ver ventas por fecha o período
• consultarClientes - Buscar información de clientes
• consultarFacturas - Ver facturas y detalles de ventas
• consultarCategorias - Ver categorías de productos
• obtenerReporteVentas - Obtener resumen de ventas con métricas
• consultarDeudasClientes - (PREMIUM) Ver clientes con deudas/créditos activos
• consultarSedes - (PREMIUM) Ver listado de sedes/bodegas
• crearSede - (PREMIUM) Crear nuevas sedes/bodegas (según límites del plan)

🧠 MEMORIA CONVERSACIONAL:
- TIENES acceso al historial de la conversación actual
- Si el usuario dice "sí", "ese", "el último", "hazlo", entiende que se refiere a lo último que mencionaste
- Mantén el contexto y recuerda de qué estaban hablando

⚠️ FORMATO DE RESPUESTA:
- NUNCA devuelvas JSON crudo al usuario.
- Siempre responde en texto natural, amigable y profesional.
- Si una herramienta devuelve datos, interprétalos y explícalos.

📋 REGLAS DE NEGOCIO Y PLANES:
1. **ADAPTACIÓN AL PLAN**:
   - Si el usuario pide algo de **SEDES** (sucursales) o **CRÉDITOS** (deudas, fiado) y su plan es 'free_trial' o 'basic':
     - PUEDES responder la consulta si la herramienta lo permite, PERO SIEMPRE añade una nota de venta.
     - Ejemplo: "Veo que tienes 2 clientes con deuda. 💡 Para gestionar créditos y cobros automáticamente, te recomiendo actualizar al Plan Premium."
     - Si intentan crear una sede y el sistema lo rechaza por plan, explícales amablemente los beneficios del plan Premium.
     - Si preguntan CÓMO hacerlo manualmente: "Ve al menú lateral > Gestión de Sedes > Botón 'Nueva Sede'."

2. **VENTAS INTELIGENTES (UPSELLING)**:
   - Si detectas que el usuario pregunta mucho por inventario avanzado o múltiples cajas, sugiere sutilmente el plan Enterprise.
   - Si pregunta por "fiado" o "crédito", destaca el módulo Creditienda del plan Premium.

3. **RESPUESTAS REALES**:
   - Siempre usa las herramientas. No inventes datos.
   - Si preguntan "¿tenemos clientes con deudas?", USA `consultarDeudasClientes`.

4. **PREGUNTAS DE SEGUIMIENTO**:
   - Si preguntan "¿quiénes son?" después de un reporte, usa los datos que ya tienes o busca detalles específicos.

¡Ayuda al usuario a gestionar su negocio y crecer con POS 105!
EOT;
    }

    /**
     * Recupera el historial de conversación para Gemini
     */
    private function getConversationHistory()
    {
        $sessionId = $this->getSessionId();

        $messages = ConversationHistory::where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->limit($this->maxHistoryMessages)
            ->get()
            ->reverse();

        $geminiHistory = [];
        foreach ($messages as $msg) {
            $role = $msg->role === 'user' ? 'user' : 'model';
            $geminiHistory[] = [
                'role' => $role,
                'parts' => [['text' => $msg->content]]
            ];
        }

        return $geminiHistory;
    }

    /**
     * Limpia el historial viejo de la sesión actual
     */
    private function cleanOldHistory()
    {
        try {
            $sessionId = $this->getSessionId();

            // Eliminar TODO el historial de esta sesión (del tenant actual)
            $deleted = ConversationHistory::where('session_id', $sessionId)->delete();

            if ($deleted > 0) {
                Log::info("🧹 [Gemini] Historial limpiado: {$deleted} mensajes eliminados (session: {$sessionId})");
            }

        } catch (\Exception $e) {
            Log::error('❌ [Gemini] Error limpiando historial: ' . $e->getMessage());
        }
    }

    /**
     * Guarda un mensaje en el historial
     */
    private function saveMessage($role, $content)
    {
        ConversationHistory::create([
            'user_id' => auth()->id(),
            'session_id' => $this->getSessionId(),
            'role' => $role,
            'content' => $content
        ]);
    }

    /**
     * Ejecuta el agente Gemini con el prompt del usuario.
     */
    public function runAgent($prompt)
    {
        if (!$this->apiKey) {
            return [
                'reply' => 'Error: GEMINI_API_KEY no está configurada en el .env',
                'status' => 'error'
            ];
        }

        // Limpiar historial viejo al iniciar
        $this->cleanOldHistory();

        // Guardar mensaje del usuario en historial
        $this->saveMessage('user', $prompt);

        // 1. Definir herramientas (Tools) - AMPLIADAS
        $tools = $this->getToolsDefinition();

        // 2. Construir mensajes con historial
        $history = $this->getConversationHistory();

        // El historial ya incluye el mensaje actual, así que lo usamos directamente
        $messages = $history;

        // Si el historial está vacío (primera vez), agregamos el mensaje
        if (empty($messages)) {
            $messages = [
                [
                    'role' => 'user',
                    'parts' => [['text' => $prompt]]
                ]
            ];
        }

        $response = $this->callGemini($messages, $tools);

        // Validar estructura de respuesta
        if (!isset($response['candidates']) || !is_array($response['candidates']) || count($response['candidates']) === 0) {
            Log::warning('Gemini: No candidates returned', ['response' => $response]);
            return [
                'reply' => 'Lo siento, Gemini no devolvió ninguna respuesta válida.',
                'status' => 'error'
            ];
        }

        $candidate = $response['candidates'][0];
        $content = $candidate['content'] ?? null;
        $parts = $content['parts'] ?? [];

        if (empty($parts)) {
            Log::warning('Gemini: No content parts returned', ['candidate' => $candidate]);
            return [
                'reply' => 'Lo siento, la respuesta de Gemini estaba vacía.',
                'status' => 'error'
            ];
        }

        $firstPart = $parts[0];

        // 3. Verificar si Gemini quiere ejecutar una función (puede ser múltiples)
        if (isset($firstPart['functionCall'])) {
            return $this->handleFunctionCalls($parts, $messages, $tools);
        }

        // Si no hubo llamada a función, devolver la respuesta de texto normal
        $replyText = $firstPart['text'] ?? 'Lo siento, no entendí tu solicitud.';

        // Guardar respuesta en historial
        $this->saveMessage('assistant', $replyText);

        // Extraer información de tokens
        $tokensInfo = null;
        if (isset($response['usageMetadata'])) {
            $usage = $response['usageMetadata'];
            $tokensInfo = [
                'promptTokens' => $usage['promptTokenCount'] ?? 0,
                'candidatesTokens' => $usage['candidatesTokenCount'] ?? 0,
                'totalTokens' => $usage['totalTokenCount'] ?? 0,
            ];
        }

        return [
            'reply' => $replyText,
            'status' => 'success',
            'tokens' => $tokensInfo
        ];
    }

    /**
     * Maneja las llamadas a funciones de Gemini
     */
    private function handleFunctionCalls($parts, $messages, $tools)
    {
        $functionResults = [];
        $executedFunctions = [];

        foreach ($parts as $part) {
            if (!isset($part['functionCall'])) continue;

            $functionCall = $part['functionCall'];
            $functionName = $functionCall['name'];
            $args = $functionCall['args'] ?? [];

            // Convertir args vacío a objeto
            if (is_array($args) && empty($args)) {
                $args = new \stdClass();
            }

            Log::info("🤖 Gemini Agent: Ejecutando función {$functionName}", (array)$args);

            // Ejecutar la función
            $functionResult = $this->executeFunction($functionName, (array)$args);
            $functionResults[] = [
                'name' => $functionName,
                'result' => $functionResult
            ];
            $executedFunctions[] = $functionName;

            // Agregar respuesta de función al historial
            $messages[] = [
                'role' => 'model',
                'parts' => [['functionCall' => [
                    'name' => $functionName,
                    'args' => $args
                ]]]
            ];

            $messages[] = [
                'role' => 'function',
                'parts' => [[
                    'functionResponse' => [
                        'name' => $functionName,
                        'response' => ['content' => $functionResult]
                    ]
                ]]
            ];
        }

        // Segunda llamada para obtener respuesta final
        $finalResponse = $this->callGemini($messages, $tools);

        if (isset($finalResponse['candidates'][0]['content']['parts'][0]['text'])) {
            $replyText = $finalResponse['candidates'][0]['content']['parts'][0]['text'];
        } else {
            // Fallback inteligente
            $resultMessages = array_map(function($r) {
                return $r['result']['message'] ?? $r['result']['mensaje'] ?? json_encode($r['result']);
            }, $functionResults);
            $replyText = implode("\n", $resultMessages);
        }

        // Guardar respuesta en historial
        $this->saveMessage('assistant', $replyText);

        // Extraer información de tokens
        $tokensInfo = null;
        if (isset($finalResponse['usageMetadata'])) {
            $usage = $finalResponse['usageMetadata'];
            $tokensInfo = [
                'promptTokens' => $usage['promptTokenCount'] ?? 0,
                'candidatesTokens' => $usage['candidatesTokenCount'] ?? 0,
                'totalTokens' => $usage['totalTokenCount'] ?? 0,
            ];
        }

        return [
            'reply' => $replyText,
            'status' => 'success',
            'agent_action' => 'Ejecutado: ' . implode(', ', $executedFunctions),
            'tokens' => $tokensInfo
        ];
    }

    /**
     * Define todas las herramientas disponibles para Gemini
     */
    private function getToolsDefinition()
    {
        return [
            [
                'function_declarations' => [
                    // === PRODUCTOS ===
                    [
                        'name' => 'crearProducto',
                        'description' => 'Crea un nuevo producto en el inventario con nombre, precio y stock.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'nombre' => ['type' => 'STRING', 'description' => 'Nombre del producto'],
                                'precio' => ['type' => 'NUMBER', 'description' => 'Precio del producto'],
                                'stock' => ['type' => 'NUMBER', 'description' => 'Cantidad inicial en stock']
                            ],
                            'required' => ['nombre']
                        ]
                    ],
                    [
                        'name' => 'consultarInventario',
                        'description' => 'Busca productos en el inventario por nombre o término de búsqueda.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'query' => ['type' => 'STRING', 'description' => 'Término de búsqueda']
                            ],
                            'required' => ['query']
                        ]
                    ],
                    [
                        'name' => 'obtenerEstadisticas',
                        'description' => 'Obtiene el conteo total de productos, valor del inventario, productos con stock bajo y ventas de hoy.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => new \stdClass()
                        ]
                    ],
                    [
                        'name' => 'actualizarProducto',
                        'description' => 'Actualiza el precio o stock de un producto existente buscándolo por nombre.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'nombre' => ['type' => 'STRING', 'description' => 'Nombre exacto del producto a actualizar'],
                                'nuevo_precio' => ['type' => 'NUMBER', 'description' => 'Nuevo precio (opcional)'],
                                'nuevo_stock' => ['type' => 'NUMBER', 'description' => 'Nuevo stock (opcional)']
                            ],
                            'required' => ['nombre']
                        ]
                    ],
                    [
                        'name' => 'eliminarProducto',
                        'description' => 'Elimina un producto del inventario por su nombre.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'nombre' => ['type' => 'STRING', 'description' => 'Nombre exacto del producto a eliminar']
                            ],
                            'required' => ['nombre']
                        ]
                    ],

                    // === VENTAS ===
                    [
                        'name' => 'consultarVentas',
                        'description' => 'Consulta las ventas realizadas. Puede buscar por período (hoy, ayer, semana, mes) o por fecha específica (YYYY-MM-DD). Usa esto cuando pregunten si hubo ventas, cuántas ventas, total de ventas, etc.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'periodo' => [
                                    'type' => 'STRING',
                                    'description' => 'Período de tiempo: hoy, ayer, semana, mes, año, ultimos_7_dias, ultimos_30_dias',
                                    'enum' => ['hoy', 'ayer', 'semana', 'mes', 'año', 'ultimos_7_dias', 'ultimos_30_dias']
                                ],
                                'fecha' => [
                                    'type' => 'STRING',
                                    'description' => 'Fecha específica en formato YYYY-MM-DD (ej: 2025-12-27). Usa esto si el usuario pregunta por una fecha específica.'
                                ],
                                'limite' => [
                                    'type' => 'NUMBER',
                                    'description' => 'Número máximo de facturas a mostrar (por defecto 10)'
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'obtenerReporteVentas',
                        'description' => 'Obtiene un resumen completo de ventas con métricas: total vendido, cantidad de facturas, ticket promedio, productos más vendidos.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'periodo' => [
                                    'type' => 'STRING',
                                    'description' => 'Período: hoy, ayer, semana, mes',
                                    'enum' => ['hoy', 'ayer', 'semana', 'mes']
                                ]
                            ],
                            'required' => ['periodo']
                        ]
                    ],

                    // === CLIENTES ===
                    [
                        'name' => 'consultarClientes',
                        'description' => 'Busca clientes por nombre, teléfono, email o documento.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'query' => ['type' => 'STRING', 'description' => 'Término de búsqueda (nombre, teléfono, email)'],
                                'limite' => ['type' => 'NUMBER', 'description' => 'Máximo de resultados (default 10)']
                            ]
                        ]
                    ],

                    // === FACTURAS ===
                    [
                        'name' => 'consultarFacturas',
                        'description' => 'Busca facturas por cliente, número de factura o período de tiempo.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'cliente' => ['type' => 'STRING', 'description' => 'Nombre del cliente'],
                                'numero_factura' => ['type' => 'STRING', 'description' => 'Número de factura (ej: FACT-0001)'],
                                'periodo' => [
                                    'type' => 'STRING',
                                    'enum' => ['hoy', 'ayer', 'semana', 'mes']
                                ],
                                'limite' => ['type' => 'NUMBER', 'description' => 'Máximo de resultados']
                            ]
                        ]
                    ],
                    [
                        'name' => 'obtenerDetalleFactura',
                        'description' => 'Obtiene los detalles completos de una factura: productos, cantidades, precios, cliente, etc.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'factura_id' => ['type' => 'NUMBER', 'description' => 'ID de la factura']
                            ],
                            'required' => ['factura_id']
                        ]
                    ],

                    // === CATEGORÍAS ===
                    [
                        'name' => 'consultarCategorias',
                        'description' => 'Lista todas las categorías de productos o busca una específica.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'query' => ['type' => 'STRING', 'description' => 'Término de búsqueda (opcional)']
                            ]
                        ]
                    ],

                    // === PREMIUM: CRÉDITOS Y SEDES ===
                    [
                        'name' => 'consultarDeudasClientes',
                        'description' => 'Busca clientes que tienen deudas pendientes (créditos activos). Útil para saber quién debe dinero.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'minimo_deuda' => ['type' => 'NUMBER', 'description' => 'Monto mínimo de deuda a buscar (opcional, default 0)'],
                                'limite' => ['type' => 'NUMBER', 'description' => 'Máximo de resultados (default 10)']
                            ]
                        ]
                    ],
                    [
                        'name' => 'consultarSedes',
                        'description' => 'Lista las sedes (bodegas/sucursales) registradas en el sistema.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => new \stdClass()
                        ]
                    ],
                    [
                        'name' => 'crearSede',
                        'description' => 'Crea una nueva sede (bodega/sucursal). Verifica automáticamente si el plan del usuario permite crear más sedes (Premium: max 3, Enterprise: ilimitado).',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'nombre' => ['type' => 'STRING', 'description' => 'Nombre de la nueva sede'],
                                'direccion' => ['type' => 'STRING', 'description' => 'Dirección de la sede (opcional)'],
                                'telefono' => ['type' => 'STRING', 'description' => 'Teléfono de la sede (opcional)']
                            ],
                            'required' => ['nombre']
                        ]
                    ],

                    // === IMPORTACIÓN DE PRODUCTOS ===
                    [
                        'name' => 'analizarArchivoProductos',
                        'description' => 'Analiza un archivo Excel o CSV que contiene productos para importar. La IA detecta automáticamente las columnas (nombre, precio, stock, etc.) y genera un preview. IMPORTANTE: El usuario debe proporcionar la ruta del archivo en el servidor (ej: storage/tenantX/app/temp/imports/archivo.csv)',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'file_path' => [
                                    'type' => 'STRING',
                                    'description' => 'Ruta completa del archivo CSV/Excel en el servidor. Ejemplo: /backend/storage/tenantX/app/temp/imports/import_xxxxx.csv'
                                ]
                            ],
                            'required' => ['file_path']
                        ]
                    ],
                    [
                        'name' => 'importarProductosMasivo',
                        'description' => 'Importa múltiples productos a la base de datos de una sola vez. Usa esto cuando el usuario te proporcione datos de productos en formato JSON con headers y rows. Crea automáticamente las categorías que no existan.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'products_data' => [
                                    'type' => 'OBJECT',
                                    'description' => 'Objeto con headers (array de nombres de columnas) y rows (array de arrays con los datos de cada producto)',
                                    'properties' => [
                                        'headers' => [
                                            'type' => 'ARRAY',
                                            'description' => 'Array con los nombres de las columnas (ej: ["Nombre", "Precio Venta", "Stock", "Categoría"])',
                                            'items' => ['type' => 'STRING']
                                        ],
                                        'rows' => [
                                            'type' => 'ARRAY',
                                            'description' => 'Array de arrays, cada uno con los datos de un producto',
                                            'items' => [
                                                'type' => 'ARRAY',
                                                'items' => ['type' => 'STRING']
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'required' => ['products_data']
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Realiza la petición HTTP a la API de Gemini.
     */
    private function callGemini($contents, $tools = null)
    {
        $url = $this->baseUrl . '?key=' . $this->apiKey;

        $payload = [
            'contents' => $contents,
            'systemInstruction' => [
                'parts' => [['text' => $this->buildSystemInstruction()]]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topP' => 0.95,
                'maxOutputTokens' => 2048
            ]
        ];

        if ($tools) {
            $payload['tools'] = $tools;
            $payload['toolConfig'] = [
                'functionCallingConfig' => [
                    'mode' => 'AUTO'
                ]
            ];
        }

        Log::info('Gemini Request Payload', ['payload' => json_encode($payload)]);

        $response = Http::timeout(60)->withHeaders([
            'Content-Type' => 'application/json'
        ])->post($url, $payload);

        if ($response->failed()) {
            Log::error('Gemini API Error', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \Exception('Error al conectar con Gemini: ' . $response->body());
        }

        $jsonResponse = $response->json();
        Log::info('Gemini Response', ['response' => $jsonResponse]);

        // 📊 LOG DE TOKENS CONSUMIDOS
        if (isset($jsonResponse['usageMetadata'])) {
            $usage = $jsonResponse['usageMetadata'];
            Log::info('📊 [Gemini Tokens]', [
                'promptTokens' => $usage['promptTokenCount'] ?? 0,
                'candidatesTokens' => $usage['candidatesTokenCount'] ?? 0,
                'totalTokens' => $usage['totalTokenCount'] ?? 0,
            ]);
        }

        if (!$jsonResponse) {
            throw new \Exception('Respuesta vacía o inválida de Gemini API');
        }

        return $jsonResponse;
    }

    /**
     * Ejecuta la función local correspondiente.
     */
    private function executeFunction($name, $args)
    {
        switch ($name) {
            // Productos
            case 'crearProducto':
                return $this->crearProductoDB($args);
            case 'consultarInventario':
                return $this->consultarInventarioDB($args);
            case 'obtenerEstadisticas':
                return $this->obtenerEstadisticasDB();
            case 'actualizarProducto':
                return $this->actualizarProductoDB($args);
            case 'eliminarProducto':
                return $this->eliminarProductoDB($args);

            // Ventas
            case 'consultarVentas':
                return $this->consultarVentasDB($args);
            case 'obtenerReporteVentas':
                return $this->obtenerReporteVentasDB($args);

            // Clientes
            case 'consultarClientes':
                return $this->consultarClientesDB($args);

            // Facturas
            case 'consultarFacturas':
                return $this->consultarFacturasDB($args);
            case 'obtenerDetalleFactura':
                return $this->obtenerDetalleFacturaDB($args);

            // Categorías
            case 'consultarCategorias':
                return $this->consultarCategoriasDB($args);

            // Premium
            case 'consultarDeudasClientes':
                return $this->consultarDeudasClientesDB($args);
            case 'consultarSedes':
                return $this->consultarSedesDB();
            case 'crearSede':
                return $this->crearSedeDB($args);

            // Importación de productos
            case 'analizarArchivoProductos':
                return $this->analizarArchivoProductosDB($args);
            case 'importarProductosMasivo':
                return $this->importarProductosMasivoHandler($args);

            default:
                return ['error' => 'Función no encontrada: ' . $name];
        }
    }

    // ========================================
    // FUNCIONES DE BASE DE DATOS - PRODUCTOS
    // ========================================

    private function crearProductoDB($data)
    {
        try {
            // 1. Validar datos mínimos
            $nombre = $data['nombre'] ?? 'Producto sin nombre';
            $precio = $data['precio'] ?? 0;
            $stock = $data['stock'] ?? 0;

            // 2. Obtener Categoría por defecto (o crearla si no existe)
            $categoria = Category::first();
            if (!$categoria) {
                $categoria = Category::create([
                    'name' => 'General',
                    'active' => true
                ]);
            }

            // 3. Obtener Proveedor por defecto (o crear uno si no existe)
            $proveedor = Supplier::first();
            if (!$proveedor) {
                $proveedor = Supplier::create([
                    'name' => 'Proveedor General',
                    'document' => '000000000',
                    'active' => true,
                    'email' => 'general@example.com',
                    'phone' => '0000000000'
                ]);
            }

            // 4. Crear el producto en la base de datos del tenant actual
            $producto = Product::create([
                'name' => $nombre,
                'product_type' => 'simple',
                'sale_price' => $precio,
                'cost_price' => 0,
                'current_stock' => $stock,
                'manage_stock' => true,
                'active' => true,
                'category_id' => $categoria->id,
                'supplier_id' => $proveedor->id,
                'sku' => 'GEN-' . strtoupper(uniqid()),
                'barcode' => rand(10000000, 99999999),
            ]);

            Log::info("✅ Producto creado por IA: {$producto->name} (ID: {$producto->id})");

            return [
                'status' => 'ok',
                'message' => "Producto '{$producto->name}' creado exitosamente con ID {$producto->id}, precio \${$producto->sale_price} y stock {$producto->current_stock}.",
                'id' => $producto->id
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error al crear producto por IA: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'No se pudo crear el producto: ' . $e->getMessage()
            ];
        }
    }

    private function consultarInventarioDB($data)
    {
        try {
            $query = $data['query'] ?? '';

            $productos = Product::where('name', 'like', "%{$query}%")
                ->orWhere('sku', 'like', "%{$query}%")
                ->limit(10)
                ->get(['id', 'name', 'sale_price', 'current_stock']);

            if ($productos->isEmpty()) {
                return [
                    'results' => [],
                    'count' => 0,
                    'message' => "No se encontraron productos con '{$query}'."
                ];
            }

            return [
                'results' => $productos->toArray(),
                'count' => $productos->count(),
                'message' => "Se encontraron {$productos->count()} productos."
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error al consultar inventario: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Error al consultar inventario.'
            ];
        }
    }

    private function obtenerEstadisticasDB()
    {
        try {
            $totalProductos = Product::count();
            $valorInventario = Product::sum(DB::raw('sale_price * current_stock'));
            $bajoStock = Product::whereColumn('current_stock', '<=', 'min_stock')->count();

            // Ventas de hoy
            $ventasHoy = Invoice::where('type', 'invoice')
                ->where('status', '!=', 'cancelled')
                ->whereDate('date', Carbon::today())
                ->sum('total');

            return [
                'total_productos' => $totalProductos,
                'valor_total_inventario' => $valorInventario,
                'productos_bajo_stock' => $bajoStock,
                'ventas_hoy' => $ventasHoy,
                'mensaje' => "Tienes {$totalProductos} productos en inventario. Valor total: $" . number_format($valorInventario, 0, ',', '.') . ". Productos con stock bajo: {$bajoStock}. Ventas de hoy: $" . number_format($ventasHoy, 0, ',', '.')
            ];
        } catch (\Exception $e) {
            Log::error("❌ Error obtenerEstadisticas: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al obtener estadísticas.'];
        }
    }

    private function actualizarProductoDB($data)
    {
        try {
            $nombre = $data['nombre'];
            $producto = Product::where('name', 'LIKE', "%{$nombre}%")->first();

            if (!$producto) {
                return ['status' => 'error', 'message' => "No encontré ningún producto llamado '{$nombre}'."];
            }

            $cambios = [];
            if (isset($data['nuevo_precio'])) {
                $producto->sale_price = $data['nuevo_precio'];
                $cambios[] = "precio a \${$data['nuevo_precio']}";
            }
            if (isset($data['nuevo_stock'])) {
                $producto->current_stock = $data['nuevo_stock'];
                $cambios[] = "stock a {$data['nuevo_stock']}";
            }

            if (empty($cambios)) {
                return ['status' => 'warning', 'message' => "Encontré el producto '{$producto->name}' pero no me diste nuevos valores para actualizar."];
            }

            $producto->save();

            return [
                'status' => 'ok',
                'message' => "Producto '{$producto->name}' actualizado: " . implode(', ', $cambios) . "."
            ];
        } catch (\Exception $e) {
            Log::error("❌ Error actualizarProducto: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al actualizar producto.'];
        }
    }

    private function eliminarProductoDB($data)
    {
        try {
            $nombre = $data['nombre'];
            $producto = Product::where('name', $nombre)->first();

            if (!$producto) {
                return ['status' => 'error', 'message' => "No encontré ningún producto llamado '{$nombre}' para eliminar."];
            }

            $producto->delete();

            return [
                'status' => 'ok',
                'message' => "Producto '{$nombre}' eliminado correctamente del inventario."
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error al eliminar producto.'];
        }
    }

    // ========================================
    // FUNCIONES DE BASE DE DATOS - VENTAS
    // ========================================

    private function consultarVentasDB($args)
    {
        try {
            $periodo = $args['periodo'] ?? null;
            $fecha = $args['fecha'] ?? null;
            $limite = $args['limite'] ?? 10;

            $query = Invoice::with('customer:id,name')
                ->where('type', 'invoice')
                ->where('status', '!=', 'cancelled');

            // Filtrar por fecha específica
            if ($fecha) {
                $fechaCarbon = Carbon::parse($fecha);
                $query->whereDate('date', $fechaCarbon);
            }
            // Filtrar por período
            elseif ($periodo) {
                switch ($periodo) {
                    case 'hoy':
                        $query->whereDate('date', Carbon::today());
                        break;
                    case 'ayer':
                        $query->whereDate('date', Carbon::yesterday());
                        break;
                    case 'semana':
                        $query->where('date', '>=', Carbon::now()->startOfWeek());
                        break;
                    case 'mes':
                        $query->where('date', '>=', Carbon::now()->startOfMonth());
                        break;
                    case 'año':
                        $query->where('date', '>=', Carbon::now()->startOfYear());
                        break;
                    case 'ultimos_7_dias':
                        $query->where('date', '>=', Carbon::now()->subDays(7));
                        break;
                    case 'ultimos_30_dias':
                        $query->where('date', '>=', Carbon::now()->subDays(30));
                        break;
                }
            }

            $totalVentas = (clone $query)->sum('total');
            $cantidadFacturas = (clone $query)->count();

            $facturas = $query->orderBy('date', 'desc')
                ->limit($limite)
                ->get(['id', 'number', 'customer_id', 'date', 'total', 'status', 'payment_method'])
                ->map(function($f) {
                    return [
                        'id' => $f->id,
                        'numero' => $f->number,
                        'cliente' => $f->customer?->name ?? 'Cliente General',
                        'fecha' => $f->date,
                        'total' => $f->total,
                        'metodo_pago' => $f->payment_method
                    ];
                });

            $periodoTexto = $fecha ? "el {$fecha}" : ($periodo ?? 'todos los tiempos');

            return [
                'periodo' => $periodoTexto,
                'total_ventas' => $totalVentas,
                'cantidad_facturas' => $cantidadFacturas,
                'ticket_promedio' => $cantidadFacturas > 0 ? round($totalVentas / $cantidadFacturas, 2) : 0,
                'facturas' => $facturas->toArray(),
                'mensaje' => $cantidadFacturas > 0
                    ? "Se encontraron {$cantidadFacturas} ventas por un total de $" . number_format($totalVentas, 0, ',', '.') . " para {$periodoTexto}."
                    : "No se encontraron ventas para {$periodoTexto}."
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error consultarVentas: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al consultar ventas: ' . $e->getMessage()];
        }
    }

    private function obtenerReporteVentasDB($args)
    {
        try {
            $periodo = $args['periodo'] ?? 'hoy';

            $dateFilter = match($periodo) {
                'hoy' => Carbon::today(),
                'ayer' => Carbon::yesterday(),
                'semana' => Carbon::now()->startOfWeek(),
                'mes' => Carbon::now()->startOfMonth(),
                default => Carbon::today()
            };

            $isExactDate = in_array($periodo, ['hoy', 'ayer']);

            $queryBase = Invoice::where('type', 'invoice')
                ->where('status', '!=', 'cancelled');

            if ($isExactDate) {
                $queryBase->whereDate('date', $dateFilter);
            } else {
                $queryBase->where('date', '>=', $dateFilter);
            }

            $totalVentas = (clone $queryBase)->sum('total');
            $cantidadFacturas = (clone $queryBase)->count();
            $ticketPromedio = $cantidadFacturas > 0 ? round($totalVentas / $cantidadFacturas, 2) : 0;

            // Si hay pocas facturas (<= 10), incluirlas para dar contexto inmediato (quién compró, qué, etc.)
            $ultimasFacturas = [];
            if ($cantidadFacturas > 0 && $cantidadFacturas <= 10) {
                $ultimasFacturas = (clone $queryBase)
                    ->with('customer:id,name')
                    ->orderBy('date', 'desc')
                    ->get(['id', 'number', 'customer_id', 'date', 'total'])
                    ->map(function($f) {
                        return [
                            'id' => $f->id,
                            'numero' => $f->number,
                            'cliente' => $f->customer?->name ?? 'Cliente General',
                            'total' => $f->total,
                            'fecha' => $f->date->toDateTimeString()
                        ];
                    })
                    ->toArray();
            }

            // Productos más vendidos
            $topProductos = InvoiceItem::whereHas('invoice', function($q) use ($dateFilter, $isExactDate) {
                    $q->where('type', 'invoice')->where('status', '!=', 'cancelled');
                    if ($isExactDate) {
                        $q->whereDate('date', $dateFilter);
                    } else {
                        $q->where('date', '>=', $dateFilter);
                    }
                })
                ->selectRaw('product_name, SUM(quantity) as cantidad_vendida, SUM(unit_price * quantity) as total_vendido')
                ->groupBy('product_name')
                ->orderByDesc('cantidad_vendida')
                ->limit(5)
                ->get()
                ->toArray();

            return [
                'periodo' => $periodo,
                'total_ventas' => $totalVentas,
                'cantidad_facturas' => $cantidadFacturas,
                'ticket_promedio' => $ticketPromedio,
                'ultimas_facturas' => $ultimasFacturas, // Contexto extra para preguntas de seguimiento
                'productos_mas_vendidos' => $topProductos,
                'mensaje' => "Reporte de ventas para {$periodo}: {$cantidadFacturas} facturas, total $" . number_format($totalVentas, 0, ',', '.')
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error obtenerReporteVentas: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al generar reporte.'];
        }
    }

    // ========================================
    // FUNCIONES DE BASE DE DATOS - CLIENTES
    // ========================================

    private function consultarClientesDB($args)
    {
        try {
            $query = $args['query'] ?? '';
            $limite = $args['limite'] ?? 10;

            $clientesQuery = Customer::query();

            if ($query) {
                $clientesQuery->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%")
                      ->orWhere('phone', 'LIKE', "%{$query}%")
                      ->orWhere('document_number', 'LIKE', "%{$query}%");
                });
            }

            $clientes = $clientesQuery->limit($limite)
                ->get(['id', 'name', 'email', 'phone', 'document_number'])
                ->toArray();

            return [
                'clientes' => $clientes,
                'count' => count($clientes),
                'mensaje' => count($clientes) > 0
                    ? "Se encontraron " . count($clientes) . " clientes."
                    : "No se encontraron clientes con ese criterio."
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error consultarClientes: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al buscar clientes.'];
        }
    }

    // ========================================
    // FUNCIONES DE BASE DE DATOS - FACTURAS
    // ========================================

    private function consultarFacturasDB($args)
    {
        try {
            $cliente = $args['cliente'] ?? null;
            $numeroFactura = $args['numero_factura'] ?? null;
            $periodo = $args['periodo'] ?? null;
            $limite = $args['limite'] ?? 20;

            $query = Invoice::with('customer:id,name')
                ->where('type', 'invoice');

            if ($numeroFactura) {
                $query->where('number', 'LIKE', "%{$numeroFactura}%");
            }

            if ($cliente) {
                $query->whereHas('customer', function($q) use ($cliente) {
                    $q->where('name', 'LIKE', "%{$cliente}%");
                });
            }

            if ($periodo) {
                switch ($periodo) {
                    case 'hoy':
                        $query->whereDate('date', Carbon::today());
                        break;
                    case 'ayer':
                        $query->whereDate('date', Carbon::yesterday());
                        break;
                    case 'semana':
                        $query->where('date', '>=', Carbon::now()->startOfWeek());
                        break;
                    case 'mes':
                        $query->where('date', '>=', Carbon::now()->startOfMonth());
                        break;
                }
            }

            $facturas = $query->orderBy('date', 'desc')
                ->limit($limite)
                ->get(['id', 'number', 'customer_id', 'date', 'total', 'status', 'payment_method'])
                ->map(function($f) {
                    return [
                        'id' => $f->id,
                        'numero' => $f->number,
                        'cliente' => $f->customer?->name ?? 'Cliente General',
                        'fecha' => $f->date,
                        'total' => $f->total,
                        'estado' => $f->status,
                        'metodo_pago' => $f->payment_method
                    ];
                });

            return [
                'facturas' => $facturas->toArray(),
                'count' => $facturas->count(),
                'mensaje' => $facturas->count() > 0
                    ? "Se encontraron {$facturas->count()} facturas."
                    : "No se encontraron facturas con ese criterio."
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error consultarFacturas: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al buscar facturas.'];
        }
    }

    private function obtenerDetalleFacturaDB($args)
    {
        try {
            $facturaId = $args['factura_id'] ?? null;

            if (!$facturaId) {
                return ['status' => 'error', 'message' => 'Se requiere el ID de la factura.'];
            }

            $factura = Invoice::with(['customer', 'items'])
                ->where('type', 'invoice')
                ->find($facturaId);

            if (!$factura) {
                return ['status' => 'error', 'message' => "No se encontró la factura con ID {$facturaId}."];
            }

            $productos = $factura->items->map(function($item) {
                return [
                    'producto' => $item->product_name,
                    'cantidad' => $item->quantity,
                    'precio_unitario' => $item->unit_price,
                    'subtotal' => $item->quantity * $item->unit_price
                ];
            });

            return [
                'factura' => [
                    'id' => $factura->id,
                    'numero' => $factura->number,
                    'cliente' => $factura->customer?->name ?? 'Cliente General',
                    'fecha' => $factura->date,
                    'subtotal' => $factura->subtotal,
                    'impuesto' => $factura->tax,
                    'total' => $factura->total,
                    'estado' => $factura->status,
                    'metodo_pago' => $factura->payment_method
                ],
                'productos' => $productos->toArray(),
                'mensaje' => "Factura {$factura->number} del cliente " . ($factura->customer?->name ?? 'Cliente General') . " por $" . number_format($factura->total, 0, ',', '.')
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error obtenerDetalleFactura: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al obtener detalles de la factura.'];
        }
    }

    // ========================================
    // FUNCIONES DE BASE DE DATOS - CATEGORÍAS
    // ========================================

    private function consultarCategoriasDB($args)
    {
        try {
            $query = $args['query'] ?? '';

            $categoriasQuery = Category::query();

            if ($query) {
                $categoriasQuery->where('name', 'LIKE', "%{$query}%");
            }

            $categorias = $categoriasQuery->limit(20)
                ->get(['id', 'name', 'description'])
                ->toArray();

            return [
                'categorias' => $categorias,
                'count' => count($categorias),
                'mensaje' => count($categorias) > 0
                    ? "Se encontraron " . count($categorias) . " categorías."
                    : "No se encontraron categorías."
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error consultarCategorias: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al buscar categorías.'];
        }
    }

    // ========================================
    // FUNCIONES DE BASE DE DATOS - PREMIUM
    // ========================================

    private function consultarDeudasClientesDB($args)
    {
        try {
            $minimo = $args['minimo_deuda'] ?? 0;
            $limite = $args['limite'] ?? 10;

            $clientes = Customer::where('current_debt', '>', $minimo)
                ->orderByDesc('current_debt')
                ->limit($limite)
                ->get(['id', 'name', 'current_debt', 'phone', 'credit_limit'])
                ->map(function($c) {
                    return [
                        'nombre' => $c->name,
                        'deuda_actual' => $c->current_debt,
                        'limite_credito' => $c->credit_limit,
                        'telefono' => $c->phone
                    ];
                });

            $totalDeuda = Customer::sum('current_debt');
            $countDeudores = Customer::where('current_debt', '>', 0)->count();

            return [
                'clientes_deudores' => $clientes->toArray(),
                'total_deuda_global' => $totalDeuda,
                'cantidad_deudores' => $countDeudores,
                'mensaje' => $countDeudores > 0
                    ? "Hay {$countDeudores} clientes con deuda total de $" . number_format($totalDeuda, 0, ',', '.')
                    : "No hay clientes con deudas pendientes."
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error consultarDeudasClientes: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al consultar deudas.'];
        }
    }

    private function consultarSedesDB()
    {
        try {
            // Verificar si existe el modelo Warehouse
            if (!class_exists('\App\Models\Warehouse')) {
                return ['status' => 'error', 'message' => 'El módulo de Sedes no está instalado o activo.'];
            }

            $sedes = \App\Models\Warehouse::where('active', true)
                ->get(['id', 'name', 'address', 'phone', 'is_default'])
                ->toArray();

            return [
                'sedes' => $sedes,
                'count' => count($sedes),
                'mensaje' => count($sedes) > 0
                    ? "Se encontraron " . count($sedes) . " sedes activas."
                    : "No se encontraron sedes registradas."
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error consultarSedes: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al consultar sedes.'];
        }
    }

    private function crearSedeDB($args)
    {
        try {
            // Verificar si existe el modelo Warehouse
            if (!class_exists('\App\Models\Warehouse')) {
                return ['status' => 'error', 'message' => 'El módulo de Sedes no está instalado o activo.'];
            }

            $nombre = $args['nombre'] ?? null;
            $direccion = $args['direccion'] ?? null;
            $telefono = $args['telefono'] ?? null;

            if (!$nombre) {
                return ['status' => 'error', 'message' => 'El nombre de la sede es obligatorio.'];
            }

            // Validar límite de tiendas según plan
            $tenantPlan = tenant('plan') ?? 'free_trial';
            $warehouseCount = \App\Models\Warehouse::count();

            // Planes que NO pueden usar multi-tienda
            if (in_array($tenantPlan, ['free_trial', 'basic'])) {
                return [
                    'status' => 'error',
                    'message' => "Tu plan actual ({$tenantPlan}) no permite crear múltiples sedes. Necesitas el plan Premium o Enterprise."
                ];
            }

            // Premium: máximo 3 tiendas
            if ($tenantPlan === 'premium' && $warehouseCount >= 3) {
                return [
                    'status' => 'error',
                    'message' => "Has alcanzado el límite de 3 sedes para tu plan Premium. Actualiza a Enterprise para crear más."
                ];
            }

            // Crear la sede
            $sede = \App\Models\Warehouse::create([
                'name' => $nombre,
                'address' => $direccion,
                'phone' => $telefono,
                'active' => true,
                'is_default' => false // Las creadas por IA no son default por seguridad
            ]);

            return [
                'status' => 'success',
                'message' => "Sede '{$sede->name}' creada exitosamente con ID {$sede->id}.",
                'sede' => $sede
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error crearSede: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al crear la sede: ' . $e->getMessage()];
        }
    }

    // ========================================
    // FUNCIONES DE BASE DE DATOS - IMPORTACIÓN
    // ========================================

    private function analizarArchivoProductosDB($args)
    {
        try {
            $filePath = $args['file_path'] ?? null;

            if (!$filePath) {
                return [
                    'status' => 'error',
                    'message' => 'No se proporcionó la ruta del archivo.'
                ];
            }

            // Verificar si el archivo existe
            if (!file_exists($filePath)) {
                return [
                    'status' => 'error',
                    'message' => 'El archivo no existe en la ruta especificada: ' . $filePath
                ];
            }

            Log::info('[GeminiAgent] Analizando archivo de productos', ['file' => $filePath]);

            // Usar los servicios de análisis existentes
            $excelParser = new \App\Services\ExcelParserService();
            $columnMapper = new \App\Services\AIColumnMapperService();

            // Parsear el archivo
            $parseResult = $excelParser->parseFile($filePath);

            Log::info('[GeminiAgent] Archivo parseado', [
                'headers' => $parseResult['headers'],
                'total_rows' => $parseResult['total_rows']
            ]);

            // Analizar con IA
            $aiAnalysis = $columnMapper->analyzeColumnsWithAI(
                $parseResult['headers'],
                $parseResult['sample_data']
            );

            Log::info('[GeminiAgent] Análisis de IA completado', [
                'method' => $aiAnalysis['method'],
                'confidence' => $aiAnalysis['confidence']
            ]);

            // Preparar resumen para Gemini
            $mapping = $aiAnalysis['column_mapping'];
            $mappingText = [];
            foreach ($mapping as $excelCol => $systemField) {
                if ($systemField !== 'ignore') {
                    $mappingText[] = "• '$excelCol' → $systemField";
                }
            }

            return [
                'status' => 'success',
                'archivo' => basename($filePath),
                'total_filas' => $parseResult['total_rows'],
                'total_columnas' => count($parseResult['headers']),
                'columnas_detectadas' => $parseResult['headers'],
                'mapeo_inteligente' => $mapping,
                'confianza_ia' => $aiAnalysis['confidence'] . '%',
                'metodo_analisis' => $aiAnalysis['method'],
                'advertencias' => $aiAnalysis['warnings'] ?? [],
                'muestra_datos' => array_slice($parseResult['sample_data'], 0, 3),
                'mensaje' => "📊 Archivo analizado exitosamente:\n\n" .
                    "✅ {$parseResult['total_rows']} productos detectados\n" .
                    "✅ Confianza del análisis: {$aiAnalysis['confidence']}%\n\n" .
                    "🔄 Mapeo automático de columnas:\n" . implode("\n", $mappingText) .
                    "\n\n💡 El sistema está listo para importar estos productos a la base de datos."
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error analizarArchivoProductos: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'status' => 'error',
                'message' => 'Error al analizar el archivo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Importación masiva de productos desde JSON
     * Acepta headers y rows para crear productos en bulk
     */
    private function importarProductosMasivoHandler($args)
    {
        try {
            $productsData = $args['products_data'] ?? null;
            $warehouseId = $args['warehouse_id'] ?? null; // Bodega específica (opcional)

            if (!$productsData || !isset($productsData['headers']) || !isset($productsData['rows'])) {
                return [
                    'status' => 'error',
                    'message' => 'Datos incompletos. Se requiere products_data con headers y rows.'
                ];
            }

            $headers = $productsData['headers'];
            $rows = $productsData['rows'];

            // Obtener bodega de destino
            if ($warehouseId) {
                $targetWarehouse = \App\Models\Warehouse::find($warehouseId);
                if (!$targetWarehouse) {
                    return [
                        'status' => 'error',
                        'message' => "Bodega con ID {$warehouseId} no encontrada."
                    ];
                }
            } else {
                // Usar bodega por defecto
                $targetWarehouse = \App\Models\Warehouse::where('is_default', 1)->first();
                if (!$targetWarehouse) {
                    $targetWarehouse = \App\Models\Warehouse::first();
                }

                if (!$targetWarehouse) {
                    return [
                        'status' => 'error',
                        'message' => 'No hay bodegas configuradas en el sistema.'
                    ];
                }
            }

            Log::info('[GeminiAgent] Iniciando importación masiva', [
                'total_headers' => count($headers),
                'total_rows' => count($rows)
            ]);

            // Mapear headers a índices
            $headerMap = [];
            foreach ($headers as $index => $header) {
                $headerMap[strtolower(trim($header))] = $index;
            }

            Log::info('[GeminiAgent] Header map creado', ['map' => $headerMap]);

            $createdCount = 0;
            $errors = [];
            $categoriesCache = [];

            foreach ($rows as $rowIndex => $row) {
                try {
                    // Extraer datos de la fila
                    $nombre = $this->getValueFromRow($row, $headerMap, ['nombre', 'producto', 'name']);
                    $precioVenta = $this->getValueFromRow($row, $headerMap, ['precio venta', 'precio', 'price', 'venta']);
                    $precioCosto = $this->getValueFromRow($row, $headerMap, ['precio costo', 'costo', 'cost']);
                    $stock = $this->getValueFromRow($row, $headerMap, ['stock', 'cantidad', 'existencia']);
                    $sku = $this->getValueFromRow($row, $headerMap, ['sku', 'código', 'code']);
                    $barcode = $this->getValueFromRow($row, $headerMap, ['código barras', 'barcode', 'ean', 'upc']);
                    $categoriaNombre = $this->getValueFromRow($row, $headerMap, ['categoría', 'categoria', 'category']);

                    // Validar datos mínimos
                    if (empty($nombre)) {
                        $errors[] = "Fila " . ($rowIndex + 1) . ": nombre vacío, omitido.";
                        continue;
                    }

                    // Obtener o crear categoría
                    $categoryId = null;
                    if (!empty($categoriaNombre)) {
                        if (isset($categoriesCache[$categoriaNombre])) {
                            $categoryId = $categoriesCache[$categoriaNombre];
                        } else {
                            $category = Category::firstOrCreate(
                                ['name' => $categoriaNombre],
                                ['active' => true]
                            );
                            $categoryId = $category->id;
                            $categoriesCache[$categoriaNombre] = $categoryId;
                        }
                    } else {
                        // Categoría por defecto
                        $defaultCategory = Category::first();
                        if (!$defaultCategory) {
                            $defaultCategory = Category::create([
                                'name' => 'General',
                                'active' => true
                            ]);
                        }
                        $categoryId = $defaultCategory->id;
                    }

                    // Crear producto (sin stock en products, se maneja en product_warehouse)
                    $product = Product::create([
                        'name' => $nombre,
                        'product_type' => 'simple',
                        'sale_price' => floatval($precioVenta ?? 0),
                        'cost_price' => floatval($precioCosto ?? 0),
                        'current_stock' => intval($stock ?? 0), // Total del stock
                        'sku' => $sku ?? null,
                        'barcode' => $barcode ?? null,
                        'category_id' => $categoryId,
                        'active' => true,
                    ]);

                    // ✅ ASIGNAR STOCK A BODEGA (Multisede)
                    if ($targetWarehouse) {
                        $product->warehouses()->attach($targetWarehouse->id, [
                            'stock' => intval($stock ?? 0),
                            'product_variant_id' => null
                        ]);

                        Log::info('[GeminiAgent] Stock asignado a bodega', [
                            'product_id' => $product->id,
                            'warehouse_id' => $targetWarehouse->id,
                            'warehouse_name' => $targetWarehouse->name,
                            'stock' => intval($stock ?? 0)
                        ]);
                    }

                    $createdCount++;

                    Log::info('[GeminiAgent] Producto creado', [
                        'id' => $product->id,
                        'name' => $nombre,
                        'row' => $rowIndex + 1
                    ]);

                } catch (\Exception $e) {
                    $errors[] = "Fila " . ($rowIndex + 1) . ": " . $e->getMessage();
                    Log::error('[GeminiAgent] Error en fila ' . ($rowIndex + 1), [
                        'error' => $e->getMessage(),
                        'row' => $row
                    ]);
                }
            }

            $result = [
                'status' => 'success',
                'productos_creados' => $createdCount,
                'total_filas' => count($rows),
                'bodega' => $targetWarehouse->name,
                'errores_count' => count($errors),
                'errores' => $errors,
                'mensaje' => "✅ Importación completada:\n\n" .
                    "• {$createdCount} productos creados exitosamente\n" .
                    "• " . count($rows) . " filas procesadas\n" .
                    "• Stock asignado a: {$targetWarehouse->name}\n" .
                    (count($errors) > 0 ? "• " . count($errors) . " errores encontrados\n" : "") .
                    "\n📦 Los productos ya están disponibles en tu inventario."
            ];

            Log::info('[GeminiAgent] Importación masiva completada', $result);

            return $result;

        } catch (\Exception $e) {
            Log::error("❌ Error importarProductosMasivo: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'status' => 'error',
                'message' => 'Error al importar productos: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Helper para extraer valor de una fila usando múltiples nombres posibles
     */
    private function getValueFromRow($row, $headerMap, $possibleNames)
    {
        foreach ($possibleNames as $name) {
            $key = strtolower(trim($name));
            if (isset($headerMap[$key])) {
                $index = $headerMap[$key];
                if (isset($row[$index])) {
                    return trim($row[$index]);
                }
            }
        }
        return null;
    }
}
