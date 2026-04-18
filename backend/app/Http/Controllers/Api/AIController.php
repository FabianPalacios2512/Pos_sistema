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

        $request->validate([
            'message' => 'required|string',
        ]);

        $userMessage = trim($request->input('message'));

        // 🧠 SELECTOR DE CEREBROS (AI Provider)
        $provider = $request->input('provider', 'groq');


        if ($provider === 'gemini') {
            try {
                // 🧠 Obtener contexto de pantalla del frontend
                $screenContext = $request->input('screen_context', null);
                
                $geminiService = new \App\Services\GeminiAgentService();
                $response = $geminiService->runAgent($userMessage, $screenContext);
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
            // 🧠 Agregar contexto de pantalla si está disponible
            $screenContext = $request->input('screen_context', null);
            $systemPrompt = $this->buildSystemPrompt($screenContext);

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

        // ✅ SI EL PROVIDER ES GEMINI, USAR GEMINI AGENT
        if ($provider === 'gemini') {
            return $this->processExcelFileWithGemini($file, $userMessage);
        }

        // ❌ SI NO, USAR PROCESAMIENTO DIRECTO (ANTIGUO)

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
     * @param string|null $screenContext - Contexto de pantalla del frontend (opcional)
     */
    private function buildSystemPrompt($screenContext = null)
    {
        $currentDate = now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY');
        $currentTime = now()->format('H:i');
        
        // 🧠 Agregar contexto de pantalla si está disponible
        $screenContextBlock = '';
        if (!empty($screenContext)) {
            $screenContextBlock = "\n\n" . $screenContext . "\n";
        }

        // 🔒 Construir bloque de permisos según rol del usuario
        $roleBlock = '';
        $user = auth()->user();
        if ($user) {
            $user->loadMissing('role');
        }
        if ($user && $user->role) {
            $roleName = $user->role->name ?? '';
            $isVendedor = $user->isVendedor();
            $isAdminPos = $user->isAdminPos();
            
            if ($isVendedor) {
                $moduleNameMap = [
                    'dashboard' => 'Panel Principal', 'pos' => 'Punto de Venta', 'invoices' => 'Facturas',
                    'returns' => 'Devoluciones', 'products' => 'Productos', 'categories' => 'Categorías',
                    'stock' => 'Gestión de Stock', 'inventory' => 'Inventario', 'customers' => 'Clientes',
                    'sales' => 'Ventas', 'accounts-receivable' => 'CrediTienda'
                ];
                $modulosPermitidos = [];
                foreach ($permissions as $perm) {
                    $mod = explode('.', $perm)[0];
                    if (isset($moduleNameMap[$mod]) && !in_array($moduleNameMap[$mod], $modulosPermitidos)) {
                        $modulosPermitidos[] = $moduleNameMap[$mod];
                    }
                }
                $listaModulos = implode(', ', $modulosPermitidos);
                $roleBlock = <<<ROLE

🔒 ROL DEL USUARIO: {$roleName} (VENDEDOR/CAJERO - ACCESO LIMITADO)
Módulos permitidos: {$listaModulos}
⚠️ NO tiene acceso a: Reportes, Gastos, Gestión de Usuarios, Configuración, Proveedores.
- No le sugieras navegar a módulos restringidos ni le ofrezcas acciones administrativas.
- No le muestres datos financieros globales (ganancias netas, márgenes, gastos del negocio).
- Si pregunta por algo restringido, dile amablemente que eso lo maneja el administrador.
- Sus ventas/facturas son solo las suyas, no las del negocio completo.
ROLE;
            } elseif ($isAdminPos) {
                $sedeName = $user->warehouse?->name ?? 'su sede asignada';
                $roleBlock = <<<ROLE

👤 ROL DEL USUARIO: {$roleName} (ADMINISTRADOR DE SEDE - {$sedeName})
- Tiene acceso completo pero SOLO a datos de su sede "{$sedeName}".
- Puede gestionar productos, inventario, ventas, usuarios de su sede.
- NO puede ver datos de otras sedes ni configuraciones globales del sistema.
ROLE;
            } else {
                $roleBlock = "\n👤 ROL DEL USUARIO: {$roleName} (ADMINISTRADOR - ACCESO COMPLETO)\n";
            }
        }

        return <<<EOT
Eres "105 IA", asistente virtual inteligente del sistema POS. Sé amigable, conversacional y muy útil.
{$roleBlock}{$screenContextBlock}
🎯 TUS CAPACIDADES:

📊 **DATOS DEL NEGOCIO EN CONTEXTO**:
El contexto de arriba contiene datos de HOY y ESTE MES solamente.
USA el contexto SOLO para:
- "¿cuánto vendí hoy?" → Usa ventas del contexto
- "¿cuánto llevo este mes?" → Usa ventas del contexto
- "¿cuántos productos hay?" → Usa inventario del contexto
- "¿la caja está abierta?" → Usa estado de caja del contexto

⚠️ **OBLIGATORIO USAR HERRAMIENTAS PARA FECHAS ESPECÍFICAS**:
Si el usuario menciona UNA FECHA ESPECÍFICA (ayer, el 27, la semana pasada, martes, etc.), DEBES usar herramientas.
El contexto NO tiene datos históricos, solo datos de HOY.

🔧 **HERRAMIENTAS** (OBLIGATORIAS para consultas históricas o específicas):

**📈 get_sales_report** - ⭐ PARA ESTADÍSTICAS DE VENTAS:
  - period='today' → ventas de HOY (pero mejor usa el contexto)
  - period='yesterday' → ventas de AYER
  - period='week' → ventas de esta semana
  - period='month' → ventas de este mes
  - period='specific_date', specific_date='YYYY-MM-DD' → UNA FECHA ESPECÍFICA

  🚨 EJEMPLOS OBLIGATORIOS:
  • "ventas del 27 de enero" → get_sales_report(period='specific_date', specific_date='2026-01-27')
  • "¿cómo estuvieron las ventas ayer?" → get_sales_report(period='yesterday')
  • "ventas del martes" → Calcula la fecha y usa get_sales_report(period='specific_date', specific_date='YYYY-MM-DD')
  • "ventas de la semana pasada" → get_sales_report(period='week')

**🔍 Otras herramientas**:
• search_products(query, filter, limit) - Buscar productos específicos
• search_customers(query) - Buscar clientes
• search_categories(query) - Buscar categorías
• get_low_stock_products(limit) - Productos con stock bajo
• search_invoices(customer_name, date, invoice_id, invoice_number, limit) - Buscar/listar facturas individuales
• get_invoice_details(invoice_id) - Obtener información COMPLETA de una factura específica
• execute_action(action_type, params) - Ejecutar acciones (descuentos, WhatsApp, productos, etc)

⚠️ **VENTAS vs FACTURAS - DIFERENCIA**:
- "¿Cómo estuvieron las ventas del 27?" → get_sales_report (da TOTALES: cantidad de facturas, total vendido)
- "Muéstrame la factura #5" → search_invoices + get_invoice_details (da detalles de UNA factura)
- NUNCA respondas sobre ventas de fechas pasadas sin usar get_sales_report

🔧 REGLAS IMPORTANTES:
   Frases: "llevame a X", "muestra X", "abre X", "ve a X", "ir a X", "quiero ver X"
   Módulos: products, pos, dashboard, invoices, customers, suppliers, categories, reports, settings, returns-management

   Ejemplo:
   Usuario: "llevame a productos"
   Respuesta: {"reply": "¡Claro! Te llevo al módulo de productos 📦", "action": {"type": "navigate", "payload": {"name": "POSModule", "params": {"module": "products"}}}}

2. **CONSULTAS DE DATOS** (SÍ usa herramientas):
   Usuario: "muestra productos" → Llama search_products()
   Usuario: "ventas del 27" → Llama get_sales_report(period='specific_date', specific_date='2026-01-27')

   **FACTURAS - REGLAS**:
   - Para ESTADÍSTICAS (total vendido, cuántas facturas): get_sales_report
   - Para detalles de UNA factura específica: search_invoices + get_invoice_details

3. **MEMORIA DE CONTEXTO** 🧠:
   Tienes acceso al historial de los últimos mensajes.
   Si ya consultaste una factura, puedes usar su ID para consultas de seguimiento.

4. **CREAR PRODUCTOS**:
   Si falta info (categoría), pregunta. Usa search_categories para obtener el ID.

5. **ACCIONES MULTI-PASO**:
   Ejemplo: "crea descuento para Maria" → search_customers → execute_action

6. **SALUDOS**: Responde directamente sin herramientas

7. **WhatsApp**: target debe ser "all", "active" o "specific"

8. **FORMATO RESPUESTA**:
   JSON: {"reply": "texto amigable", "action": null o navegación}

⏰ **FECHA Y HORA ACTUAL (COLOMBIA)**: {$currentDate} a las {$currentTime}
⚠️ Para calcular fechas: Hoy es {$currentDate}. Si dicen "el martes" o "hace 2 días", calcula la fecha YYYY-MM-DD correspondiente.
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
                    'description' => 'Obtener estadísticas de ventas para un período específico de tiempo. Para fechas específicas como "27 de enero", usa specific_date.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'period' => [
                                'type' => 'string',
                                'enum' => ['today', 'yesterday', 'week', 'month', 'last_7_days', 'specific_date'],
                                'description' => 'Período de tiempo para el reporte. Usa "specific_date" si el usuario menciona una fecha concreta.'
                            ],
                            'specific_date' => [
                                'type' => 'string',
                                'description' => 'Fecha específica en formato YYYY-MM-DD. Solo usar cuando period="specific_date". Ejemplo: 2026-01-27 para el 27 de enero de 2026.'
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


            // Call Groq API with tools
            $response = $this->callGroqAPIWithTools($messages);

            if (!$response) {
                Log::error("[Agent Loop] API call failed");
                break;
            }

            $message = $response['choices'][0]['message'];
            $finishReason = $response['choices'][0]['finish_reason'];


            // Add assistant message to conversation
            $messages[] = $message;

            // Check if AI wants to call a tool
            if ($finishReason === 'tool_calls' && isset($message['tool_calls'])) {

                // Execute each tool call
                foreach ($message['tool_calls'] as $toolCall) {
                    $toolName = $toolCall['function']['name'];
                    $toolArgs = json_decode($toolCall['function']['arguments'], true);


                    // Execute the tool
                    $toolResult = $this->executeToolCall($toolName, $toolArgs ?? []);


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

                    $responseData = $response->json();

                    // Extract usage metrics
                    $usage = $responseData['usage'] ?? [];
                    $promptTokens = $usage['prompt_tokens'] ?? 0;
                    $completionTokens = $usage['completion_tokens'] ?? 0;
                    $totalTokens = $usage['total_tokens'] ?? 0;


                    // Log usage
                    try {
                        $lastUserMessage = '';
                        foreach (array_reverse($messages) as $msg) {
                            if (isset($msg['role']) && $msg['role'] === 'user') {
                                $lastUserMessage = $msg['content'] ?? '';
                                break;
                            }
                        }

                        // Calcular costo
                        $costUsd = AiUsageLog::calculateTextCost($promptTokens, $completionTokens, 'groq');

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
                            'provider' => 'groq',
                            'endpoint' => 'chat',
                            'request_type' => 'chat',
                            'cost_usd' => $costUsd,
                            'ip_address' => request()->ip(),
                        ]);

                        // ✅ Registrar en tabla central para límites y facturación
                        $aiUsageService = app(AiUsageService::class);
                        $aiUsageService->logUsage(tenant('id'), $totalTokens, $costUsd);
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
        
        // Usar zona horaria de Colombia explícitamente
        $now = Carbon::now('America/Bogota');
        $today = Carbon::today('America/Bogota');
        $yesterday = Carbon::yesterday('America/Bogota');

        // Manejar fecha específica
        $specificDate = $args['specific_date'] ?? null;
        $isSpecificDate = $period === 'specific_date' && $specificDate;

        // Calcular fecha de inicio según el período
        $dateFilter = match($period) {
            'today' => $today->format('Y-m-d'),
            'yesterday' => $yesterday->format('Y-m-d'),
            'week' => $now->copy()->startOfWeek()->format('Y-m-d'),
            'month' => $now->copy()->startOfMonth()->format('Y-m-d'),
            'last_7_days' => $now->copy()->subDays(7)->format('Y-m-d'),
            'specific_date' => $specificDate ?? $today->format('Y-m-d'),
            default => $today->format('Y-m-d')
        };
        
        $todayStr = $today->format('Y-m-d');

        // Query para facturas PAGADAS (igual que la IA de voz)
        $query = Invoice::where('type', 'invoice')
            ->where('status', 'paid'); // Solo pagadas, como hace la voz
            
        if (in_array($period, ['today', 'yesterday']) || $isSpecificDate) {
            $query->whereDate('date', $dateFilter);
        } else {
            $query->whereDate('date', '>=', $dateFilter)
                  ->whereDate('date', '<=', $todayStr);
        }

        $invoices = $query->get();
        $total = $invoices->sum('total');
        $count = $invoices->count();

        // Obtener últimas facturas para contexto
        $lastInvoices = $invoices->sortByDesc('created_at')->take(5)->map(function($inv) {
            return [
                'id' => $inv->id,
                'number' => $inv->invoice_number ?? "FV-{$inv->id}",
                'customer' => $inv->customer_name ?? 'Cliente general',
                'total' => $inv->total,
                'date' => $inv->date,
            ];
        })->values()->toArray();

        // Get top selling products for this period
        $topProducts = InvoiceItem::whereHas('invoice', function($q) use ($dateFilter, $period, $todayStr, $isSpecificDate) {
                $q->where('type', 'invoice')
                  ->where('status', 'paid');
                  
                if (in_array($period, ['today', 'yesterday']) || $isSpecificDate) {
                    $q->whereDate('date', $dateFilter);
                } else {
                    $q->whereDate('date', '>=', $dateFilter)
                      ->whereDate('date', '<=', $todayStr);
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
            'current_date' => $now->format('Y-m-d'),
            'current_time' => $now->format('H:i'),
            'query_date' => $dateFilter,
            'timezone' => 'America/Bogota',
            'total_sales' => $total,
            'invoice_count' => $count,
            'average_ticket' => $count > 0 ? round($total / $count, 2) : 0,
            'last_invoices' => $lastInvoices,
            'top_products' => $topProducts,
            'message' => $count > 0 
                ? "El {$dateFilter} hubo {$count} facturas pagadas por un total de $" . number_format($total, 0, ',', '.') 
                : "No hay facturas pagadas para el período consultado ({$dateFilter})"
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

        $fullPath = null;
        try {
            // 1. Guardar archivo temporalmente
            $filePath = $file->store('temp_excel', 'local');
            $fullPath = storage_path('app/' . $filePath);


            // 2. Parsear el archivo CSV/Excel INMEDIATAMENTE
            $excelService = app(\App\Services\ExcelParserService::class);
            $parseResult = $excelService->parseFile($fullPath);

            $headers = $parseResult['headers'] ?? [];
            $rows = $parseResult['data'] ?? [];

            // ✅ ELIMINAR ARCHIVO INMEDIATAMENTE DESPUÉS DE PARSEAR
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            if (empty($headers) || empty($rows)) {
                return response()->json([
                    'reply' => '❌ Error: El archivo está vacío o no tiene el formato correcto.',
                    'status' => 'error'
                ], 400);
            }


            // 🚀 NUEVO ENFOQUE: Importar directamente sin pasar por Gemini Agent
            // Esto evita el error MALFORMED_FUNCTION_CALL con archivos grandes
            $geminiService = app(\App\Services\GeminiAgentService::class);
            
            // Llamar directamente al handler de importación
            $importResult = $geminiService->executeImportFromController([
                'products_data' => [
                    'headers' => $headers,
                    'rows' => $rows
                ]
            ]);


            // Retornar resultado de la importación (usar 'mensaje' que es el campo correcto)
            $replyText = $importResult['mensaje'] ?? $importResult['message'] ?? 'Importación procesada';
            
            return response()->json([
                'reply' => $replyText,
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ [Gemini Excel] Error: ' . $e->getMessage());

            // Limpiar archivo si aún existe
            if ($fullPath && file_exists($fullPath)) {
                @unlink($fullPath);
            }

            return response()->json([
                'reply' => '❌ Error al procesar con Gemini: ' . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * 🔊 Text-to-Speech usando Gemini 2.5 Flash TTS
     * Genera audio de alta calidad a partir de texto
     */
    public function textToSpeech(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:5000',
            'voice' => 'string|in:Kore,Charon,Fenrir,Aoede,Puck,Leda,Orus,Achird',
            'model' => 'string'
        ]);

        $text = trim($request->input('text'));
        $voice = $request->input('voice', 'Kore'); // Voz natural en español
        $model = $request->input('model', 'gemini-2.5-flash-preview-tts');


        try {
            $apiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
            
            if (!$apiKey) {
                throw new \Exception('API key de Gemini no configurada');
            }

            // Llamar a Gemini TTS API
            $response = Http::timeout(30)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $text]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'responseModalities' => ['AUDIO'],
                    'speechConfig' => [
                        'voiceConfig' => [
                            'prebuiltVoiceConfig' => [
                                'voiceName' => $voice
                            ]
                        ]
                    ]
                ]
            ]);

            if (!$response->successful()) {
                Log::error('❌ [TTS] Error de API Gemini', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Error en API de Gemini TTS: ' . $response->status());
            }

            $data = $response->json();

            // Extraer audio del response
            $audioData = $data['candidates'][0]['content']['parts'][0]['inlineData']['data'] ?? null;
            $mimeType = $data['candidates'][0]['content']['parts'][0]['inlineData']['mimeType'] ?? 'audio/mp3';

            if (!$audioData) {
                throw new \Exception('No se recibió audio en la respuesta');
            }

            // Decodificar base64 a binario
            $audioBlob = base64_decode($audioData);


            // Retornar audio como stream
            return response($audioBlob, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Length', strlen($audioBlob))
                ->header('Cache-Control', 'no-cache');

        } catch (\Exception $e) {
            Log::error('❌ [TTS] Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error generando audio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🎵 TTS Preview para selector de voces
     * Usa las voces reales de Gemini para que el usuario escuche exactamente
     * cómo sonará su asistente antes de elegir
     */
    public function ttsPreview(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:500',
            'voiceName' => 'required|string|in:Kore,Charon,Fenrir,Aoede,Puck,Leda,Orus,Achird'
        ]);

        $text = trim($request->input('text'));
        $voiceName = $request->input('voiceName');

        try {
            $apiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
            
            if (!$apiKey) {
                throw new \Exception('API key de Gemini no configurada');
            }

            // Usar modelo TTS de Gemini
            $response = Http::timeout(15)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-tts:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $text]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'responseModalities' => ['AUDIO'],
                    'speechConfig' => [
                        'voiceConfig' => [
                            'prebuiltVoiceConfig' => [
                                'voiceName' => $voiceName
                            ]
                        ]
                    ]
                ]
            ]);

            if (!$response->successful()) {
                Log::error('❌ [TTS Preview] Error de API Gemini', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Error en API de Gemini TTS');
            }

            $data = $response->json();

            // Extraer audio del response
            $audioData = $data['candidates'][0]['content']['parts'][0]['inlineData']['data'] ?? null;
            $mimeType = $data['candidates'][0]['content']['parts'][0]['inlineData']['mimeType'] ?? 'audio/pcm';

            if (!$audioData) {
                throw new \Exception('No se recibió audio en la respuesta');
            }

            // Decodificar base64 a binario (PCM raw)
            $pcmData = base64_decode($audioData);
            
            // Convertir PCM a WAV para que el navegador pueda reproducirlo
            // Gemini TTS devuelve PCM 24kHz, 16-bit, mono
            $wavData = $this->pcmToWav($pcmData, 24000, 16, 1);

            // Retornar audio como WAV
            return response($wavData, 200)
                ->header('Content-Type', 'audio/wav')
                ->header('Content-Length', strlen($wavData))
                ->header('Cache-Control', 'no-cache');

        } catch (\Exception $e) {
            Log::error('❌ [TTS Preview] Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error generando preview de audio'
            ], 500);
        }
    }

    /**
     * 📞 Genera un token efímero para la Gemini Live API
     * 
     * Los tokens efímeros permiten que el cliente se conecte directamente
     * al WebSocket de Gemini sin exponer la API key.
     * 
     * @see https://ai.google.dev/gemini-api/docs/ephemeral-tokens
     */
    public function getLiveToken(Request $request)
    {
        $request->validate([
            'model' => 'string'
        ]);

        $model = $request->input('model', 'gemini-2.5-flash-native-audio-dialog');


        try {
            $apiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
            
            if (!$apiKey) {
                throw new \Exception('API key de Gemini no configurada');
            }

            // Generar token efímero usando la API de Google
            // El token efímero es válido por ~2 minutos
            $response = Http::timeout(10)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateEphemeralToken?key={$apiKey}", [
                'config' => [
                    'ttl' => '120s' // 2 minutos de validez
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                

                return response()->json([
                    'success' => true,
                    'token' => $data['ephemeralToken'] ?? $data['token'] ?? null,
                    'expires_in' => 120,
                    'model' => $model
                ]);
            }

            // Si la API de tokens efímeros no está disponible,
            // devolvemos la API key directamente (menos seguro, solo para desarrollo)
            Log::warning('⚠️ [Live] API de tokens efímeros no disponible, usando API key directa', [
                'status' => $response->status(),
                'body' => substr($response->body(), 0, 200)
            ]);

            return response()->json([
                'success' => true,
                'token' => $apiKey,
                'expires_in' => 0, // Sin expiración (es la API key real)
                'model' => $model,
                'warning' => 'Usando API key directa (modo desarrollo)'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ [Live] Error obteniendo token: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error obteniendo token de sesión: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * 🔧 Convierte audio PCM raw a formato WAV
     * 
     * @param string $pcmData - Datos PCM raw en binario
     * @param int $sampleRate - Frecuencia de muestreo (ej: 24000)
     * @param int $bitsPerSample - Bits por muestra (ej: 16)
     * @param int $channels - Número de canales (1 = mono, 2 = stereo)
     * @return string - Datos WAV completos con header
     */
    private function pcmToWav(string $pcmData, int $sampleRate = 24000, int $bitsPerSample = 16, int $channels = 1): string
    {
        $dataSize = strlen($pcmData);
        $byteRate = $sampleRate * $channels * ($bitsPerSample / 8);
        $blockAlign = $channels * ($bitsPerSample / 8);
        
        // Construir header WAV (44 bytes)
        $header = '';
        
        // RIFF chunk descriptor
        $header .= 'RIFF';                                    // ChunkID
        $header .= pack('V', 36 + $dataSize);                 // ChunkSize (tamaño total - 8)
        $header .= 'WAVE';                                    // Format
        
        // fmt sub-chunk
        $header .= 'fmt ';                                    // Subchunk1ID
        $header .= pack('V', 16);                             // Subchunk1Size (16 para PCM)
        $header .= pack('v', 1);                              // AudioFormat (1 = PCM)
        $header .= pack('v', $channels);                      // NumChannels
        $header .= pack('V', $sampleRate);                    // SampleRate
        $header .= pack('V', $byteRate);                      // ByteRate
        $header .= pack('v', $blockAlign);                    // BlockAlign
        $header .= pack('v', $bitsPerSample);                 // BitsPerSample
        
        // data sub-chunk
        $header .= 'data';                                    // Subchunk2ID
        $header .= pack('V', $dataSize);                      // Subchunk2Size
        
        return $header . $pcmData;
    }

    /**
     * 📊 Registrar uso de llamada de voz en vivo
     * Llamado desde el frontend cuando termina una llamada Live
     */
    public function logVoiceUsage(Request $request)
    {
        $request->validate([
            'duration_seconds' => 'required|integer|min:1',
            'status' => 'nullable|string|in:success,cancelled,error,timeout',
        ]);

        $durationSeconds = $request->input('duration_seconds');
        $status = $request->input('status', 'success');

        try {
            // Calcular costo estimado
            $costUsd = AiUsageLog::calculateVoiceCost($durationSeconds);

            // Registrar en la base de datos
            $log = AiUsageLog::create([
                'user_id' => auth()->id(),
                'api_key_index' => 0,
                'api_key_last_4' => 'LIVE',
                'user_message' => 'Llamada de voz en vivo',
                'prompt_tokens' => 0,
                'completion_tokens' => 0,
                'total_tokens' => 0,
                'status' => $status,
                'response_time_ms' => $durationSeconds * 1000,
                'model' => 'gemini-2.5-flash-preview-native-audio',
                'provider' => 'gemini',
                'endpoint' => 'live',
                'request_type' => 'voice',
                'voice_duration_seconds' => $durationSeconds,
                'cost_usd' => $costUsd,
                'ip_address' => $request->ip(),
            ]);

            // También registrar en tabla central si existe
            try {
                $aiUsageService = app(\App\Services\AiUsageService::class);
                $aiUsageService->logUsage(tenant('id'), 0, $costUsd);
            } catch (\Exception $e) {
                \Log::warning("No se pudo registrar en central: " . $e->getMessage());
            }


            return response()->json([
                'success' => true,
                'logged' => [
                    'duration_seconds' => $durationSeconds,
                    'cost_usd' => number_format($costUsd, 6),
                    'cost_cop' => number_format($costUsd * 4200, 2),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error("[AI Voice Log] Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'No se pudo registrar el uso de voz',
            ], 500);
        }
    }
}