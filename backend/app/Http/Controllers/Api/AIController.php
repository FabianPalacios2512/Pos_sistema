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

        // 🔍 LOG DE DEBUG: Ver qué llega
        Log::info('🤖 [AI Chat] Request recibido', [
            'message' => $request->input('message'),
            'provider' => $request->input('provider', 'groq'),
            'has_auth' => auth()->check(),
            'user_id' => auth()->id(),
            'ip' => $request->ip()
        ]);

        $request->validate([
            'message' => 'required|string',
        ]);

        $userMessage = trim($request->input('message'));

        // 🧠 SELECTOR DE CEREBROS (AI Provider)
        $provider = $request->input('provider', 'groq');

        Log::info('🔄 [AI Chat] Proveedor seleccionado', ['provider' => $provider]);

        if ($provider === 'gemini') {
            try {
                Log::info('🚀 [Gemini] Iniciando agente Gemini', ['message' => $userMessage]);
                $geminiService = new \App\Services\GeminiAgentService();
                $response = $geminiService->runAgent($userMessage);
                Log::info('✅ [Gemini] Respuesta generada', ['reply_length' => strlen(json_encode($response))]);
                return response()->json($response);
            } catch (\Exception $e) {
                Log::error("❌ [Gemini] Error: " . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Mensaje amigable sin exponer detalles técnicos
                $userFriendlyMessage = 'Tuve un problema técnico al procesar tu solicitud. Por favor intenta de nuevo en unos segundos.';
                
                // Si es error de conexión, mensaje específico
                if (str_contains($e->getMessage(), 'connection') || str_contains($e->getMessage(), 'timeout')) {
                    $userFriendlyMessage = 'Parece que hay problemas de conexión. Verifica tu internet e intenta de nuevo.';
                }
                
                return response()->json([
                    'reply' => $userFriendlyMessage,
                    'status' => 'error'
                ], 500);
            }
        }

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

            // 1. Limpiar historial viejo al iniciar (solo de este tenant)
            $this->cleanOldHistory($sessionId);

            // 2. Build lightweight system prompt (NO CONTEXT STUFFING!)
            $systemPrompt = $this->buildSystemPrompt();

            // 3. Recuperar historial de conversación (reducido para evitar confusión)
            $conversationHistory = ConversationHistory::getRecentMessages($sessionId, 3);

            // 4. Guardar mensaje del usuario
            ConversationHistory::create([
                'user_id' => auth()->id(),
                'session_id' => $sessionId,
                'role' => 'user',
                'content' => $userMessage,
            ]);

            // 5. Call AI with agent loop (handles tools automatically)
            $response = $this->callGroqAPI($systemPrompt, $userMessage, $conversationHistory);

            // 6. Guardar respuesta del asistente
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
            Log::error('Stack Trace: ' . $e->getTraceAsString());
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
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Handle chat with file upload (Excel or Image)
     */
    public function chatWithFile(Request $request)
    {
        Log::info("📂 [AI] Iniciando carga de archivo", [
            'has_file' => $request->hasFile('file'),
            'message' => $request->input('message')
        ]);

        set_time_limit(180); // Más tiempo para procesar archivos grandes

        $request->validate([
            'message' => 'nullable|string',
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $userMessage = trim($request->input('message', ''));
        $provider = $request->input('provider', 'groq');
        $file = $request->file('file');

        try {
            $extension = strtolower($file->getClientOriginalExtension());
            $mimeType = $file->getMimeType();

            // Determinar tipo de archivo
            if (in_array($extension, ['xlsx', 'xls', 'csv'])) {
                // Procesar Excel/CSV
                return $this->processExcelFile($file, $userMessage, $provider);
            } elseif (str_starts_with($mimeType, 'image/')) {
                // Procesar imagen
                return $this->processImageFile($file, $userMessage, $provider);
            } else {
                return response()->json([
                    'reply' => 'Tipo de archivo no soportado. Solo acepto archivos Excel (.xlsx, .xls, .csv) o imágenes.',
                    'status' => 'error'
                ], 400);
            }
        } catch (\Throwable $e) {
            Log::error('AI Chat With File Error: ' . $e->getMessage());
            return response()->json([
                'reply' => 'Tuve un problema al procesar el archivo. Verifica que sea un archivo válido e intenta de nuevo.',
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Process Excel file and create products in bulk
     */
    private function processExcelFile($file, $userMessage, $provider)
    {
        Log::info("📂 [processExcelFile] Iniciando procesamiento", [
            'provider' => $provider,
            'extension' => $file->getClientOriginalExtension(),
            'size' => $file->getSize(),
            'message' => $userMessage
        ]);

        // ✅ SI EL PROVIDER ES GEMINI, USAR GEMINI AGENT
        if ($provider === 'gemini') {
            Log::info("🤖 [processExcelFile] Usando Gemini Agent para procesar archivo");
            return $this->processExcelFileWithGemini($file, $userMessage);
        }

        // ❌ SI NO, USAR PROCESAMIENTO DIRECTO (ANTIGUO)
        Log::info("📊 [processExcelFile] Usando procesamiento directo (sin IA)");

        // Usar PhpSpreadsheet si está disponible, sino SimpleXLSX
        $extension = strtolower($file->getClientOriginalExtension());
        $products = [];

        try {
            if ($extension === 'csv') {
                // Procesar CSV
                $handle = fopen($file->getPathname(), 'r');
                $headers = fgetcsv($handle);

                if ($headers === false) {
                    fclose($handle);
                    return response()->json([
                        'reply' => 'El archivo CSV está vacío o no se pudo leer.',
                        'status' => 'error'
                    ]);
                }

                // Limpiar BOM si existe
                $bom = pack('H*','EFBBBF');
                $headers[0] = preg_replace("/^$bom/", '', $headers[0]);

                $headers = array_map('strtolower', array_map('trim', $headers));
                $headerCount = count($headers);

                while (($row = fgetcsv($handle)) !== false) {
                    // Saltar filas vacías
                    if (empty($row) || (count($row) === 1 && empty($row[0]))) {
                        continue;
                    }

                    $rowCount = count($row);

                    if ($rowCount > $headerCount) {
                        $row = array_slice($row, 0, $headerCount);
                    } elseif ($rowCount < $headerCount) {
                        $row = array_pad($row, $headerCount, '');
                    }

                    $combined = @array_combine($headers, $row);
                    if ($combined !== false) {
                        $products[] = $combined;
                    }
                }
                fclose($handle);
            } else {
                // Intentar usar PhpSpreadsheet
                if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                    return response()->json([
                        'reply' => "Para procesar archivos Excel (.xlsx/.xls), necesito la librería PhpSpreadsheet.\n\nPuedes:\n1. Exportar tu archivo como CSV\n2. O instalar: composer require phpoffice/phpspreadsheet",
                        'status' => 'error'
                    ]);
                }

                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();

                if (count($rows) < 2) {
                    return response()->json([
                        'reply' => 'El archivo Excel está vacío o solo tiene encabezados.',
                        'status' => 'error'
                    ]);
                }

                $headers = array_map('strtolower', array_map('trim', $rows[0]));
                for ($i = 1; $i < count($rows); $i++) {
                    if (count($rows[$i]) >= count($headers)) {
                        $products[] = array_combine($headers, $rows[$i]);
                    }
                }
            }

            if (empty($products)) {
                return response()->json([
                    'reply' => 'No encontré productos válidos en el archivo. Asegúrate de que tenga columnas como: nombre, precio, costo, stock, categoria.',
                    'status' => 'error'
                ]);
            }

            // Procesar y crear productos
            $created = 0;
            $errors = [];
            $createdProducts = [];

            foreach ($products as $index => $productData) {
                try {
                    // Mapear columnas comunes
                    $name = $productData['nombre'] ?? $productData['name'] ?? $productData['producto'] ?? null;
                    $price = $productData['precio'] ?? $productData['price'] ?? $productData['precio_venta'] ?? $productData['sale_price'] ?? 0;
                    $cost = $productData['costo'] ?? $productData['cost'] ?? $productData['precio_costo'] ?? $productData['cost_price'] ?? 0;
                    $stock = $productData['stock'] ?? $productData['cantidad'] ?? $productData['quantity'] ?? 0;
                    $sku = $productData['sku'] ?? $productData['codigo'] ?? $productData['code'] ?? null;
                    $categoryName = $productData['categoria'] ?? $productData['category'] ?? null;

                    if (empty($name)) {
                        $errors[] = "Fila " . ($index + 2) . ": Sin nombre de producto";
                        continue;
                    }

                    // Buscar o crear categoría
                    $categoryName = $categoryName ?: 'General';
                    $category = \App\Models\Category::where('name', 'LIKE', $categoryName)->first();

                    if (!$category) {
                        $category = \App\Models\Category::create([
                            'name' => $categoryName,
                            'active' => true
                        ]);
                    }
                    $categoryId = $category->id;

                    // Crear producto
                    $product = Product::create([
                        'name' => $name,
                        'sku' => $sku ?? strtoupper(substr(md5($name . time()), 0, 8)),
                        'sale_price' => floatval($price),
                        'cost_price' => floatval($cost),
                        'current_stock' => intval($stock),
                        'min_stock' => 5,
                        'category_id' => $categoryId,
                        'active' => true,
                        'manage_stock' => true,
                    ]);

                    $created++;
                    $createdProducts[] = $product->name;

                } catch (\Throwable $e) {
                    $errors[] = "Fila " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            // Eliminar archivo después de procesar
            @unlink($file->getPathname());

            // Preparar respuesta
            $reply = "✅ **Importación completada**\n\n";
            $reply .= "📦 Productos creados: **{$created}** de " . count($products) . "\n";

            if ($created > 0) {
                $reply .= "\n**Productos creados:**\n";
                foreach (array_slice($createdProducts, 0, 10) as $name) {
                    $reply .= "• {$name}\n";
                }
                if ($created > 10) {
                    $reply .= "• ... y " . ($created - 10) . " más\n";
                }
            }

            if (!empty($errors)) {
                $reply .= "\n⚠️ **Errores:**\n";
                foreach (array_slice($errors, 0, 5) as $error) {
                    $reply .= "• {$error}\n";
                }
                if (count($errors) > 5) {
                    $reply .= "• ... y " . (count($errors) - 5) . " errores más\n";
                }
            }

            return response()->json([
                'reply' => $reply,
                'status' => 'success',
                'data' => [
                    'created' => $created,
                    'total' => count($products),
                    'errors' => count($errors)
                ]
            ]);

        } catch (\Exception $e) {
            @unlink($file->getPathname());
            throw $e;
        }
    }

    /**
     * Process image file and create product with it
     */
    private function processImageFile($file, $userMessage, $provider)
    {
        try {
            // Guardar imagen en storage
            $fileName = 'product_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('products', $fileName, 'public');
            $imageUrl = asset('storage/' . $path);

            // Extraer nombre del mensaje o del archivo
            $productName = $this->extractProductNameFromMessage($userMessage);
            if (empty($productName)) {
                // Usar nombre del archivo sin extensión
                $productName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $productName = str_replace(['_', '-'], ' ', $productName);
                $productName = ucwords($productName);
            }

            // Crear producto con imagen
            $product = Product::create([
                'name' => $productName,
                'sku' => strtoupper(substr(md5($productName . time()), 0, 8)),
                'sale_price' => 0,
                'cost_price' => 0,
                'current_stock' => 0,
                'min_stock' => 5,
                'image_url' => $imageUrl,
                'active' => true,
                'manage_stock' => true,
            ]);

            $reply = "✅ **Producto creado con imagen**\n\n";
            $reply .= "📦 **{$product->name}**\n";
            $reply .= "🖼️ Imagen: Guardada correctamente\n";
            $reply .= "💰 Precio: $0 (pendiente)\n";
            $reply .= "📊 Stock: 0 unidades\n\n";
            $reply .= "⚠️ Recuerda configurar el precio y stock del producto.";

            return response()->json([
                'reply' => json_encode([
                    'reply' => $reply,
                    'action' => null,
                    'suggested_action' => [
                        'type' => 'navigate',
                        'label' => '✏️ Editar Producto',
                        'payload' => [
                            'name' => 'POSModule',
                            'params' => ['module' => 'products'],
                            'query' => ['action' => 'edit', 'id' => $product->id]
                        ]
                    ]
                ]),
                'status' => 'success',
                'data' => [
                    'product_id' => $product->id,
                    'image_url' => $imageUrl
                ]
            ]);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Extract product name from user message
     */
    private function extractProductNameFromMessage($message)
    {
        // Patrones comunes para extraer nombres de productos
        $patterns = [
            '/(?:crea(?:r)?|crear un|nuevo) producto (?:llamado |con nombre |de )?"?([^"]+)"?/i',
            '/producto[:\s]+([^\n,]+)/i',
            '/nombre[:\s]+([^\n,]+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                return trim($matches[1]);
            }
        }

        return null;
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

   **FACTURAS - REGLAS CRÍTICAS** ⚠️⚠️⚠️:

   **CUANDO EL USUARIO PIDE "INFORMACIÓN", "DETALLES", "VENDEDOR" O "PRODUCTOS" DE UNA FACTURA:**
   1. Si menciona un número como "FACT-000012":
      - Paso 1: search_invoices(invoice_number='FACT-000012') → Obtener ID
      - Paso 2: **OBLIGATORIO** get_invoice_details(invoice_id=ID) → Obtener TODO

   2. Si el usuario pregunta "qué productos" o "qué vendió" después de consultar una factura:
      - Usa get_invoice_details(invoice_id=ID_DEL_CONTEXTO)
      - NO digas "necesito consultar ventas de hoy"
      - La factura YA fue consultada, usa su ID del historial

   3. **NUNCA** respondas solo con search_invoices si el usuario quiere:
      - Vendedor
      - Productos
      - Detalles completos
      - "información de"

   **search_invoices** → Solo para LISTAR/CONTAR facturas
   **get_invoice_details** → Para VER DETALLES (vendedor, productos, precios)

   Ejemplos correctos:
   • "dame información de FACT-000012" → search_invoices + get_invoice_details
   • "qué productos tiene esa factura" → get_invoice_details(ID_del_historial)
   • "quién fue el vendedor" → get_invoice_details(ID_del_historial)
   • "cuántas facturas hay" → search_invoices solamente

4. **MEMORIA DE CONTEXTO** 🧠:
   Tienes acceso al historial de los últimos 3 mensajes. Úsalo inteligentemente:

   - Si en turno 1 consultaste la factura FACT-000012 (ID=12)
   - Y en turno 2 el usuario pregunta "qué productos tiene" o "quién fue el vendedor"
   - **USA EL ID DEL TURNO 1**: get_invoice_details(invoice_id=12)
   - NO digas "necesito consultar ventas" - YA TIENES EL CONTEXTO

   Si no encuentras el ID en el historial reciente:
   - Pregunta: "¿Te refieres a la factura FACT-000012 que consultamos antes?"
   - O pide el número de factura nuevamente

5. **CREAR PRODUCTOS** - SÉ PROACTIVO:
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

6. **ACCIONES MULTI-PASO**:
   Ejemplo: "crea descuento para Maria"
   - Paso 1: search_customers(query='Maria') → obtiene ID
   - Paso 2: execute_action('create_discount', params con customer_id)
   - Paso 3: RESPONDE con resultado (NO más herramientas)

7. **SALUDOS**: "hola", "hi", "buenos días" → Responde directamente sin herramientas

8. **DETENTE** cuando hayas obtenido lo que el usuario pidió

9. **ERRORES** - SÉ CLARO Y ÚTIL:
   Si algo falla → Explica QUÉ falló y QUÉ se necesita
   Ejemplo: "No puedo crear el producto porque la categoría 'Hogar' no existe en el sistema. ¿Quieres que la cree primero o usar otra categoría existente?"

10. **WhatsApp**: target debe ser "all", "active" o "specific" (con customer_ids)

11. **EDICIÓN DE PRODUCTOS**:
   - Si el usuario dice "abreme el editor", "editarlo", "modificarlo" justo después de crear o mencionar un producto:
     - Usa la acción `navigate` con `query: { "action": "edit", "id": [ID_DEL_PRODUCTO] }`.
     - El ID del producto recién creado estará en el historial de conversación o en tu memoria inmediata.

12. **CREACIÓN PROACTIVA Y EDICIÓN OPCIONAL**:
   - Si el usuario pregunta "¿podemos crear X?" o "¿puedes crear X?", ASUME QUE ES UNA ORDEN.
   - NO preguntes "¿quieres que lo cree?". HAZLO.
   - Si faltan datos, créalo con valores por defecto (precio 0, stock 0) y avisa al usuario que puede editarlo después.
   - **IMPORTANTE**: Después de crear un producto, **NO** navegues automáticamente al editor.
   - En su lugar, usa `suggested_action` para mostrar un botón que diga "✏️ Editar Producto".

13. **SEDES / BODEGAS (IMPORTANTE)**:
    - Si el usuario pide "crear sede", "nueva bodega", "sucursal":
      - Verifica el plan del usuario (si tienes acceso) o intenta ejecutar la acción `create_warehouse`.
      - Si la acción falla por límite de plan (Basic/Free), EXPLICA: "Tu plan actual no permite crear más sedes."
      - **LÍMITES DE PLAN**: Free/Basic: 1 sede, Premium: 3 sedes, Enterprise: Ilimitado

14. **FORMATO RESPUESTA**:
   SIEMPRE JSON: {"reply": "texto amigable y útil", "action": null o objeto_navegación, "suggested_action": { "type": "navigate", "label": "Texto del Botón", "payload": { ... } } o null}

15. **ERRORES PREVIOS**:
   - Si ves en el historial que tuviste un problema o error en el turno anterior, NO intentes reintentar la acción automáticamente a menos que el usuario lo pida de nuevo.
   - Prioriza SIEMPRE el último mensaje del usuario.

13. **ERRORES PREVIOS**:
   - Si ves en el historial que tuviste un problema o error en el turno anterior, NO intentes reintentar la acción automáticamente a menos que el usuario lo pida de nuevo.
   - Prioriza SIEMPRE el último mensaje del usuario.
   - Si el usuario pregunta algo nuevo (ej: "¿cuántos productos hay?"), responde a ESO, no a la orden fallida anterior.

14. **EJEMPLOS DE CONSULTAS DE FACTURAS**:
   Ejemplo 1:
   Usuario: "dame información de la factura FACT-000012"
   Paso 1: search_invoices(invoice_number='FACT-000012') → Obtiene [{ id: 12, number: 'FACT-000012', customer_name: 'MARIA JOSE', date: '2025-12-26', total: 1800, status: 'Pagada', payment_method: 'Efectivo' }]
   Paso 2: get_invoice_details(invoice_id=12) → Obtiene productos completos y vendedor
   Respuesta: "La factura **FACT-000012** del 26 de diciembre de 2025 a nombre de **MARIA JOSE** fue realizada por el vendedor **[Nombre del Vendedor]** y pagada con **efectivo** por un total de **\$1.800**. Productos vendidos: [lista productos con cantidades y precios]"

   Ejemplo 2:
   Usuario: "facturas de hoy"
   Paso 1: search_invoices(date='today') → Lista facturas
   Respuesta: "Hoy tienes X facturas por un total de \$XXX. Las principales son: [lista con números, clientes y montos]"

   Ejemplo 3:
   Usuario: "muestra los productos de esa factura" (después de haber mencionado FACT-000012)
   Paso 1: get_invoice_details(invoice_id=12) [usar el ID del contexto]
   Respuesta: "Productos de la factura FACT-000012:\n• [Producto 1]: X unidades x \$Y = \$Z\n• [Producto 2]: X unidades x \$Y = \$Z\n**Total: \$1.800**\n**Vendedor: [Nombre]**"

   ⚠️ IMPORTANTE: SIEMPRE incluye el vendedor (seller_name) en las respuestas sobre facturas cuando esté disponible.

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
                    'description' => 'Buscar facturas por cliente, fecha, número de factura o ID. IMPORTANTE: Si el usuario menciona un número de factura como "FACT-000012", usa el parámetro invoice_number. Esta herramienta retorna una lista con información básica (id, number, customer, date, total, status). Para obtener los DETALLES COMPLETOS (productos, cantidades, precios), usa después get_invoice_details con el ID encontrado.',
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
                                'description' => 'ID específico de la factura (ej: 12)'
                            ],
                            'invoice_number' => [
                                'type' => 'string',
                                'description' => 'Número de factura completo o parcial (ej: "FACT-000012" o "000012"). Usa esto cuando el usuario mencione el código de la factura.'
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
                    'description' => 'Obtener información COMPLETA de una factura específica incluyendo: productos con cantidades y precios, cliente, vendedor/cajero, fecha, método de pago, estado, etc. Usa esto cuando el usuario pida "información", "detalles", "qué tiene", "mostrar" una factura, o pregunte por el vendedor.',
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
                                'enum' => ['create_discount', 'send_whatsapp', 'create_campaign', 'create_product', 'create_category', 'create_warehouse'],
                                'description' => 'Tipo de acción a ejecutar'
                            ],
                            'params' => [
                                'type' => 'object',
                                'description' => 'Parámetros específicos. create_product: {name, category_id, sale_price, cost_price, current_stock}. create_warehouse: {name, address, phone}. create_discount: {name, code, type, value, duration_days}. send_whatsapp: {message, target, customer_ids}.'
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
        // Sistema de rotación de múltiples API Keys (usando config() para compatibilidad con cache)
        $apiKeys = array_filter([
            config('services.groq.api_key_1'),
            config('services.groq.api_key_2'),
            config('services.groq.api_key_3'),
            config('services.groq.api_key_4'),
            config('services.groq.api_key_5'),
            config('services.groq.api_key_6'),
            config('services.groq.api_key_7'),
            config('services.groq.api_key_8'),
            config('services.groq.api_key_9'),
            config('services.groq.api_key_10'),
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
            return ['error' => 'No se pudo ejecutar esta función. Por favor intenta de nuevo.'];
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
                'seller_name' => $invoice->seller_name ?? 'No especificado',
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

                case 'create_warehouse':
                    return $this->createWarehouseAction($actionData['params'] ?? []);

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
                'message' => 'No se pudo completar la acción. Por favor intenta de nuevo.'
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
     * Create warehouse action
     */
    private function createWarehouseAction($params)
    {
        // Validar límite de tiendas según plan
        $tenantPlan = tenant('plan') ?? 'free_trial';
        $warehouseCount = \App\Models\Warehouse::count();

        // Planes que NO pueden usar multi-tienda
        if (in_array($tenantPlan, ['free_trial', 'basic'])) {
            return [
                'success' => false,
                'message' => "Tu plan actual ({$tenantPlan}) no permite crear múltiples sedes. Necesitas el plan Premium o Enterprise."
            ];
        }

        // Premium: máximo 3 tiendas
        if ($tenantPlan === 'premium' && $warehouseCount >= 3) {
            return [
                'success' => false,
                'message' => "Has alcanzado el límite de 3 sedes para tu plan Premium. Actualiza a Enterprise para crear más."
            ];
        }

        try {
            $warehouse = \App\Models\Warehouse::create([
                'name' => $params['name'] ?? 'Nueva Sede',
                'address' => $params['address'] ?? null,
                'phone' => $params['phone'] ?? null,
                'active' => true,
                'is_default' => false
            ]);

            return [
                'success' => true,
                'message' => "Sede '{$warehouse->name}' creada exitosamente.",
                'data' => $warehouse
            ];
        } catch (\Exception $e) {
            Log::error('Error creando sede via IA: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => "No pude crear la sede. Verifica los datos e intenta de nuevo."
            ];
        }
    }

    /**
     * Clean old conversation history (only current tenant)
     * Deletes messages from the provided session
     */
    private function cleanOldHistory($sessionId)
    {
        try {
            // Eliminar TODO el historial de esta sesión (del tenant actual)
            $deleted = ConversationHistory::where('session_id', $sessionId)->delete();

            if ($deleted > 0) {
                Log::info("🧹 [AI] Historial limpiado: {$deleted} mensajes eliminados (session: {$sessionId})");
            }

        } catch (\Exception $e) {
            Log::error('❌ [AI] Error limpiando historial: ' . $e->getMessage());
        }
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

    /**
     * Get AI usage statistics for the current tenant
     * Returns usage limits, current usage, and warnings
     */
    public function getUsageStats()
    {
        try {
            $tenantId = tenant('id');

            if (!$tenantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant no identificado'
                ], 400);
            }

            $aiUsageService = new AiUsageService();
            $stats = $aiUsageService->getUsageStats($tenantId);

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas de uso IA: ' . $e->getMessage());

            // Return default stats if service fails
            return response()->json([
                'success' => true,
                'data' => [
                    'plan' => 'free_trial',
                    'limits' => [
                        'unlimited' => false,
                        'limits' => [
                            'requests_per_hour' => 10,
                            'requests_per_day' => 50,
                            'tokens_per_request' => 4000,
                            'tokens_per_day' => 100000
                        ]
                    ],
                    'usage' => [
                        'last_hour' => [
                            'requests' => 0,
                            'tokens' => 0,
                            'remaining_requests' => 10
                        ],
                        'today' => [
                            'requests' => 0,
                            'tokens' => 0,
                            'remaining_requests' => 50
                        ]
                    ],
                    'warnings' => []
                ]
            ]);
        }
    }

    /**
     * Get AI provider configuration based on tenant plan
     * Returns recommended provider and availability
     */
    public function getProviderConfig()
    {
        try {
            $tenantId = tenant('id');
            $tenant = \Stancl\Tenancy\Database\Models\Tenant::find($tenantId);
            $planName = $tenant->plan ?? 'free_trial';

            // Provider availability based on plan
            $providerConfig = [
                'free_trial' => [
                    'default' => 'groq',
                    'available' => ['groq'],
                    'gemini_enabled' => false,
                    'reason' => 'Plan gratuito - solo Groq disponible'
                ],
                'basic' => [
                    'default' => 'groq',
                    'available' => ['groq'],
                    'gemini_enabled' => false,
                    'reason' => 'Plan Básico - solo Groq disponible'
                ],
                'premium' => [
                    'default' => 'gemini',
                    'available' => ['groq', 'gemini'],
                    'gemini_enabled' => true,
                    'reason' => 'Plan Premium - Gemini recomendado'
                ],
                'enterprise' => [
                    'default' => 'gemini',
                    'available' => ['groq', 'gemini'],
                    'gemini_enabled' => true,
                    'reason' => 'Plan Enterprise - acceso completo'
                ]
            ];

            $config = $providerConfig[$planName] ?? $providerConfig['free_trial'];
            $config['current_plan'] = $planName;

            return response()->json([
                'success' => true,
                'data' => $config
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo config de proveedores IA: ' . $e->getMessage());

            return response()->json([
                'success' => true,
                'data' => [
                    'current_plan' => 'free_trial',
                    'default' => 'groq',
                    'available' => ['groq'],
                    'gemini_enabled' => false,
                    'reason' => 'Configuración por defecto'
                ]
            ]);
        }
    }

    /**
     * Process Excel file with Gemini AI
     */
    private function processExcelFileWithGemini($file, $userMessage)
    {
        Log::info("🤖 [Gemini Excel] Iniciando análisis con Gemini");

        $fullPath = null;
        try {
            // 1. Guardar archivo temporalmente
            $filePath = $file->store('temp_excel', 'local');
            $fullPath = storage_path('app/' . $filePath);

            Log::info("📁 [Gemini Excel] Archivo guardado en: {$fullPath}");

            // 2. Parsear el archivo CSV/Excel INMEDIATAMENTE
            $excelService = app(\App\Services\ExcelParserService::class);
            $parseResult = $excelService->parseFile($fullPath);

            $headers = $parseResult['headers'] ?? [];
            $rows = $parseResult['data'] ?? [];

            // ✅ ELIMINAR ARCHIVO INMEDIATAMENTE DESPUÉS DE PARSEAR
            // No esperar a que Gemini responda
            if (file_exists($fullPath)) {
                unlink($fullPath);
                Log::info("🗑️ [Gemini Excel] Archivo temporal eliminado inmediatamente");
            }

            if (empty($headers) || empty($rows)) {
                return response()->json([
                    'reply' => '❌ Error: El archivo está vacío o no tiene el formato correcto.',
                    'status' => 'error'
                ], 400);
            }

            Log::info("📊 [Gemini Excel] Archivo parseado", [
                'headers' => $headers,
                'total_rows' => count($rows)
            ]);

            // 3. Preparar datos para Gemini (SIN la ruta del archivo)
            $geminiService = app(\App\Services\GeminiAgentService::class);

            $prompt = $userMessage ?: "Importa estos productos a la base de datos.";
            $prompt .= "\n\n🚀 **INSTRUCCIÓN IMPORTANTE:** Usa la función `importarProductosMasivo` para crear todos estos productos en la base de datos de una sola vez.\n\n";
            $prompt .= "**Datos del archivo:**\n";
            $prompt .= "- Columnas: " . implode(', ', $headers) . "\n";
            $prompt .= "- Total de productos: " . count($rows) . "\n\n";
            $prompt .= "**Primeras 5 filas (vista previa):**\n```json\n";

            foreach (array_slice($rows, 0, 5) as $index => $row) {
                $prompt .= json_encode(array_combine($headers, $row), JSON_UNESCAPED_UNICODE) . "\n";
            }
            $prompt .= "```\n\n";
            $prompt .= "📋 **Datos completos listos para importar:**\n```json\n";
            $prompt .= json_encode(['headers' => $headers, 'rows' => $rows], JSON_UNESCAPED_UNICODE);
            $prompt .= "\n```\n\n";
            $prompt .= "✅ **Ahora llama a la función `importarProductosMasivo` con este JSON para importar los " . count($rows) . " productos.**";

            Log::info("📤 [Gemini Excel] Enviando a Gemini", [
                'total_products' => count($rows),
                'headers' => $headers
            ]);

            // 4. Ejecutar con Gemini Agent
            $response = $geminiService->runAgent($prompt);

            Log::info("✅ [Gemini Excel] Respuesta recibida de Gemini");

            // 6. Retornar respuesta de Gemini
            return response()->json([
                'reply' => $response,
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ [Gemini Excel] Error: ' . $e->getMessage());

            // Limpiar archivo si aún existe
            if ($fullPath && file_exists($fullPath)) {
                @unlink($fullPath);
                Log::info("🗑️ [Gemini Excel] Archivo temporal eliminado (error)");
            }

            return response()->json([
                'reply' => '❌ Error al procesar con Gemini: ' . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }
}

