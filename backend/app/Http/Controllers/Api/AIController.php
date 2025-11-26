<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\AiUsageLog;
use App\Models\ConversationHistory;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AIController extends Controller
{
    /**
     * Handle the chat request.
     */
    public function chat(Request $request)
    {
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
                '000' => [
                    'reply' => '🔧 [DEV] Códigos disponibles:\n• 123 = Productos\n• 456 = POS\n• 789 = Dashboard\n• 111 = Facturas\n• 222 = Clientes\n• 333 = Reportes',
                    'action' => null
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
                // 🎯 ACCIONES EJECUTABLES (sin gastar créditos)
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
                                'customer_ids' => [10] // Fabian Andres - 3134540533
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
                            'customer_ids' => [1] // ID del primer cliente
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
                                'code' => 'TEST' . date('His'), // Código único con hora/minuto/segundo
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
                // Obtener ID único del usuario (autenticado o IP)
                $userId = auth()->id() ?? $request->ip();
                $executionKey = "dev_code_{$userMessage}_" . md5($userId);

                // Prevenir ejecuciones múltiples (debounce de 3 segundos)
                $lastExecution = cache($executionKey);
                if ($lastExecution && (time() - $lastExecution) < 3) {
                    Log::warning("[DEV CODE] Ejecución duplicada bloqueada: {$userMessage}");
                    return response()->json([
                        'reply' => json_encode(['reply' => '⏳ Espera un momento antes de ejecutar de nuevo...']),
                        'status' => 'success'
                    ]);
                }

                cache([$executionKey => time()], 5); // Cache por 5 segundos

                Log::info("[DEV CODE] Usuario usó código: {$userMessage}");

                $devResponse = $devCodes[$userMessage];

                // Si tiene execute_action, ejecutarla
                if (isset($devResponse['execute_action'])) {
                    $actionResult = $this->executeAIAction($devResponse['execute_action']);
                    $devResponse['action_result'] = $actionResult;

                    // Agregar mensaje de resultado a la respuesta
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
            // Obtener o crear session_id
            $sessionId = $request->input('session_id');
            if (!$sessionId) {
                // Generar nuevo session_id único por usuario
                $userId = auth()->id() ?? 'guest_' . $request->ip();
                $sessionId = 'sess_' . md5($userId . '_' . date('Ymd'));
            }

            // 1. Build Context from Database
            $context = $this->buildContext();

            // 2. Prepare System Prompt
            $systemPrompt = $this->buildSystemPrompt($context);

            // 3. Recuperar historial de conversación (últimos 10 mensajes)
            $conversationHistory = ConversationHistory::getRecentMessages($sessionId, 10);

            // 4. Guardar mensaje del usuario en historial
            ConversationHistory::create([
                'user_id' => auth()->id(),
                'session_id' => $sessionId,
                'role' => 'user',
                'content' => $userMessage,
            ]);

            // 5. Call AI Provider (Groq) con historial
            $response = $this->callGroqAPI($systemPrompt, $userMessage, $conversationHistory);

            // 6. Detectar y ejecutar acciones si la IA las solicitó
            $aiResponse = json_decode($response, true);

            if ($aiResponse && isset($aiResponse['execute_action'])) {
                $actionResult = $this->executeAIAction($aiResponse['execute_action']);

                // Actualizar respuesta con resultado de la acción
                if (isset($actionResult['success']) && $actionResult['success']) {
                    $aiResponse['action_result'] = $actionResult;
                    $response = json_encode($aiResponse);
                }
            }

            // 7. Guardar respuesta del asistente en historial
            $assistantReply = $aiResponse['reply'] ?? 'Sin respuesta';
            ConversationHistory::create([
                'user_id' => auth()->id(),
                'session_id' => $sessionId,
                'role' => 'assistant',
                'content' => $assistantReply,
            ]);

            return response()->json([
                'reply' => $response,
                'session_id' => $sessionId, // Devolver session_id al frontend
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            Log::error('AI Chat Error: ' . $e->getMessage());
            return response()->json([
                'reply' => 'Lo siento, tuve un problema al procesar tu solicitud. Por favor intenta de nuevo.',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Gather relevant data from the database to feed the AI.
     */
    private function buildContext()
    {
        // --- Inventory Context COMPLETO ---
        $lowStockProducts = Product::where('manage_stock', true)
            ->whereRaw('current_stock <= min_stock')
            ->select('id', 'name', 'current_stock', 'min_stock', 'sale_price', 'cost_price')
            ->limit(10)
            ->get();

        $totalProducts = Product::count();
        $activeProducts = Product::where('active', true)->count();
        $outOfStock = Product::where('current_stock', '<=', 0)->count();

        // TOP 10 PRODUCTOS (los más caros/importantes) + resumen
        $allProductsList = Product::where('active', true)
            ->select('id', 'name', 'sale_price', 'current_stock')
            ->orderBy('sale_price', 'desc')
            ->limit(10)
            ->get();

        // Producto más caro
        $mostExpensiveProduct = Product::where('active', true)
            ->orderBy('sale_price', 'desc')
            ->first(['name', 'sale_price', 'current_stock']);

        // Producto más barato
        $cheapestProduct = Product::where('active', true)
            ->where('sale_price', '>', 0)
            ->orderBy('sale_price', 'asc')
            ->first(['name', 'sale_price', 'current_stock']);

        // Categorías con nombres
        $totalCategories = \App\Models\Category::count();
        $activeCategories = \App\Models\Category::where('active', true)->count();
        $categoriesList = \App\Models\Category::where('active', true)
            ->select('id', 'name')
            ->limit(10)
            ->get();

        // Clientes (CON LISTA COMPLETA para búsquedas)
        $totalCustomers = \App\Models\Customer::count();
        $customersList = \App\Models\Customer::select('id', 'name', 'email', 'phone', 'document_number')
            ->orderBy('created_at', 'desc')
            ->limit(50) // Aumentado para mejor búsqueda
            ->get();

        // --- Sales Context (Using Invoices) ---
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Helper for sales sum
        $getSalesSum = function($dateQuery) {
            return Invoice::where('type', 'invoice')
                ->where($dateQuery)
                ->where('status', '!=', 'cancelled')
                ->sum('total');
        };

        // Helper for sales count
        $getSalesCount = function($dateQuery) {
            return Invoice::where('type', 'invoice')
                ->where($dateQuery)
                ->where('status', '!=', 'cancelled')
                ->count();
        };

        // Today
        $salesToday = $getSalesSum([['date', '=', $today]]);
        $countToday = $getSalesCount([['date', '=', $today]]);

        // Yesterday
        $salesYesterday = $getSalesSum([['date', '=', $yesterday]]);
        $countYesterday = $getSalesCount([['date', '=', $yesterday]]);

        // This Month
        $salesMonth = $getSalesSum([['date', '>=', $startOfMonth]]);
        $countMonth = $getSalesCount([['date', '>=', $startOfMonth]]);

        // Top selling products today
        $topProducts = InvoiceItem::whereHas('invoice', function($query) use ($today) {
                $query->where('type', 'invoice')
                      ->whereDate('date', $today)
                      ->where('status', '!=', 'cancelled');
            })
            ->selectRaw('product_name, sum(quantity) as total_qty, sum(unit_price * quantity) as total_revenue')
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Top selling products AYER
        $topProductsYesterday = InvoiceItem::whereHas('invoice', function($query) use ($yesterday) {
                $query->where('type', 'invoice')
                      ->whereDate('date', $yesterday)
                      ->where('status', '!=', 'cancelled');
            })
            ->selectRaw('product_name, sum(quantity) as total_qty, sum(unit_price * quantity) as total_revenue')
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Top selling products DEL MES
        $topProductsMonth = InvoiceItem::whereHas('invoice', function($query) use ($startOfMonth) {
                $query->where('type', 'invoice')
                      ->where('date', '>=', $startOfMonth)
                      ->where('status', '!=', 'cancelled');
            })
            ->selectRaw('product_name, sum(quantity) as total_qty, sum(unit_price * quantity) as total_revenue')
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // Venta más alta del mes
        $highestSaleMonth = Invoice::where('type', 'invoice')
            ->where('date', '>=', $startOfMonth)
            ->where('status', '!=', 'cancelled')
            ->orderBy('total', 'desc')
            ->first(['id', 'number', 'total', 'date']);

        // Ventas de últimos 7 días para análisis de tendencias
        $last7DaysSales = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $sales = Invoice::where('type', 'invoice')
                ->whereDate('date', $date)
                ->where('status', '!=', 'cancelled')
                ->sum('total');
            $last7DaysSales[] = [
                'date' => $date,
                'total' => $sales,
                'day_name' => Carbon::parse($date)->locale('es')->dayName
            ];
        }

        // Proveedores
        $totalSuppliers = \App\Models\Supplier::count();
        $activeSuppliers = \App\Models\Supplier::where('active', true)->count();

        // Estado de cajas (cash sessions)
        $openCashSessions = \App\Models\CashSession::where('status', 'open')->count();
        $hasOpenSession = $openCashSessions > 0;

        // 📄 FACTURAS - Últimas 20 para búsqueda inteligente
        $recentInvoices = Invoice::with('customer:id,name,document_number')
            ->where('type', 'invoice')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get(['id', 'number', 'customer_id', 'date', 'total', 'status', 'payment_method', 'created_at'])
            ->map(function($invoice) {
                return [
                    'id' => $invoice->id,
                    'number' => $invoice->number,
                    'customer_name' => $invoice->customer?->name ?? 'Cliente General',
                    'customer_document' => $invoice->customer?->document_number ?? null,
                    'date' => $invoice->date,
                    'date_formatted' => Carbon::parse($invoice->date)->locale('es')->isoFormat('DD/MM/YYYY'),
                    'date_human' => Carbon::parse($invoice->date)->locale('es')->diffForHumans(),
                    'total' => $invoice->total,
                    'status' => $invoice->status,
                    'payment_method' => $invoice->payment_method,
                ];
            });

        // Facturas de AYER específicamente
        $invoicesYesterday = Invoice::with('customer:id,name')
            ->where('type', 'invoice')
            ->whereDate('date', $yesterday)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'number', 'customer_id', 'total', 'status'])
            ->map(function($invoice) use ($yesterday) {
                return [
                    'id' => $invoice->id,
                    'number' => $invoice->number,
                    'customer_name' => $invoice->customer?->name ?? 'Cliente General',
                    'total' => $invoice->total,
                    'status' => $invoice->status,
                    'date' => $yesterday->format('Y-m-d'),
                ];
            });

        // Facturas de HOY
        $invoicesToday = Invoice::with('customer:id,name')
            ->where('type', 'invoice')
            ->whereDate('date', $today)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'number', 'customer_id', 'total', 'status'])
            ->map(function($invoice) use ($today) {
                return [
                    'id' => $invoice->id,
                    'number' => $invoice->number,
                    'customer_name' => $invoice->customer?->name ?? 'Cliente General',
                    'total' => $invoice->total,
                    'status' => $invoice->status,
                    'date' => $today->format('Y-m-d'),
                ];
            });

        return [
            'inventory' => [
                'total_products' => $totalProducts,
                'active_products' => $activeProducts,
                'out_of_stock' => $outOfStock,
                'low_stock_count' => $lowStockProducts->count(),
                'low_stock_samples' => $lowStockProducts->toArray(),
                'all_products_list' => $allProductsList->toArray(), // LISTA COMPLETA
                'most_expensive' => $mostExpensiveProduct ? $mostExpensiveProduct->toArray() : null,
                'cheapest' => $cheapestProduct ? $cheapestProduct->toArray() : null,
            ],
            'categories' => [
                'total' => $totalCategories,
                'active' => $activeCategories,
                'list' => $categoriesList->toArray(),
            ],
            'customers' => [
                'total' => $totalCustomers,
                'recent_list' => $customersList->toArray(),
            ],
            'suppliers' => [
                'total' => $totalSuppliers,
                'active' => $activeSuppliers,
            ],
            'invoices' => [
                'recent' => $recentInvoices->toArray(),
                'today' => $invoicesToday->toArray(),
                'yesterday' => $invoicesYesterday->toArray(),
                'total_count' => Invoice::where('type', 'invoice')->count(),
            ],
            'sales' => [
                'today' => ['total' => $salesToday, 'count' => $countToday],
                'yesterday' => ['total' => $salesYesterday, 'count' => $countYesterday],
                'this_week' => ['total' => $getSalesSum([['date', '>=', $startOfWeek]]), 'count' => $getSalesCount([['date', '>=', $startOfWeek]])],
                'this_month' => ['total' => $salesMonth, 'count' => $countMonth],
                'last_7_days' => $last7DaysSales,
                'top_products_today' => $topProducts->toArray(),
                'top_products_yesterday' => $topProductsYesterday->toArray(), // NUEVO
                'top_products_month' => $topProductsMonth->toArray(), // NUEVO
                'highest_sale_month' => $highestSaleMonth ? $highestSaleMonth->toArray() : null, // NUEVO
            ],
            'system' => [
                'open_cash_sessions' => $openCashSessions,
                'has_open_session' => $hasOpenSession,
                'current_date' => now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY'),
                'current_time' => now()->format('H:i'),
                'today' => $today->format('Y-m-d'),
                'yesterday' => $yesterday->format('Y-m-d'),
                'yesterday_human' => 'ayer',
                'today_human' => 'hoy',
            ],
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    /**
     * Construct the system prompt with the gathered context.
     */
    private function buildSystemPrompt($context)
    {
        $contextJson = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<EOT
Eres "105 IA", asistente virtual del sistema POS. Sé amigable, conversacional y útil.

📊 DATOS:
{$contextJson}

🎯 REGLAS CRÍTICAS:

1. **USA SOLO DATOS REALES DEL CONTEXTO**:
   - NUNCA inventes IDs, números de factura, totales o fechas
   - Si buscas una factura de "ayer" de un cliente, usa SOLO los datos de "invoices.yesterday"
   - Verifica que el nombre del cliente coincida EXACTAMENTE
   - Si no encuentras algo en el contexto, di "No encontré [X]" en lugar de inventar datos

2. **BÚSQUEDAS DE CLIENTES**:
   - Para encontrar un cliente por nombre, busca en "customers.recent_list"
   - Nombres pueden ser parciales: "maria jose" coincide con "Maria Jose gonzalez"
   - Anota el ID del cliente para usarlo en acciones de WhatsApp

3. **RESPUESTAS CON PERSONALIDAD**:
   - Usa emojis 📦🛒💰📊
   - Sé conversacional (no robótico)
   - Explica QUÉ va a pasar cuando navegues
   - Anticipa lo que el usuario necesitará

4. **NAVEGACIÓN INTELIGENTE**:
   ❌ MAL: "¡Listo!" (muy seco)
   ✅ BIEN: "¡Perfecto! Te llevo al módulo de productos donde podrás ver todo tu inventario 📦"

   ❌ MAL: "¡Vamos!"
   ✅ BIEN: "¡Claro! Abriendo el POS para que puedas registrar ventas 🛒"

5. **MANEJO DE CONTEXTO CONVERSACIONAL** 🔥:
   - **RECUERDA la conversación anterior**: Si hay historial de mensajes, léelo para entender el contexto
   - Si acabas de sugerir ver algo y el usuario dice "sí", "claro", "ok", "si", "dale": NAVEGA inmediatamente a lo que sugeriste
   - Si sugeriste ver productos inactivos y el usuario dice "sí", navega a products con filter=inactive
   - Si preguntaste "¿quieres ir a X?" y responden afirmativamente, NO preguntes otra vez, ve directo
   - **Mantén coherencia**: Si el usuario dijo "quiero crear un producto" y luego responde con detalles (nombre, precio), ENTIENDE que está continuando la conversación

   📝 **Ejemplo de conversación con memoria**:
   Usuario: "me puedes enseñar a crear un producto?"
   TÚ: "¡Claro! Para crear un producto, necesito saber algunos detalles. ¿Cuál es el nombre del producto que deseas crear? 📦"

   Usuario: "papas fritas"
   TÚ (CON MEMORIA): "Perfecto! Vamos a crear 'papas fritas'. ¿Cuánto costará? (precio de venta)"
   TÚ (❌ SIN MEMORIA): "No encontré 'papas fritas' en el inventario. ¿Quieres agregar un nuevo producto o buscar algo más?" ← ESTO ESTÁ MAL

   Usuario: "crearlo"
   TÚ (CON MEMORIA): "¡Entendido! Creando 'papas fritas'. ¿Cuál será el precio de venta?"
   TÚ (❌ SIN MEMORIA): "¡Claro! Para crear algo, necesito saber qué es..." ← ESTO ESTÁ MAL

   **REGLA DE ORO**: Si en mensajes anteriores el usuario pidió crear/modificar/buscar algo, CONTINÚA esa conversación, NO empieces desde cero.

6. **CUANDO MUESTRES DATOS**:
   - Formatea bonito (usa saltos de línea)
   - Menciona cuántos hay
   - Destaca lo importante
   - Ofrece acciones relacionadas

7. **NAVEGACIÓN CON FILTROS**:
   - Cuando el usuario pide ver "productos inactivos", navega con query.filter = "inactive"
   - "productos activos" → filter = "active"
   - "productos con bajo stock" → filter = "low-stock"
   - "productos de [categoría]" → filter = "category:[nombre]"

   SIEMPRE USA ACTION para navegar, NO solo describir

6. **EJEMPLOS DE RESPUESTAS CORRECTAS**:

Usuario: "muestra los productos"
{
  "reply": "Tienes 10 productos activos en inventario:\n\n📦 Productos:\n1. Combo gamer - $15,000,000\n2. Sala Pocket - $50,000\n3. Sistema POS - $30,000\n...\n\n¿Quieres ir al módulo de productos para ver más detalles?",
  "action": null
}

Usuario: "llévame a productos"
{
  "reply": "¡Perfecto! Te llevo al módulo de productos donde podrás:\n✓ Ver inventario completo\n✓ Crear nuevos productos\n✓ Editar precios y stock 📦",
  "action": {"type": "navigate", "payload": {"name": "POSModule", "params": {"module": "products"}}}
}

Usuario: "cómo hago una venta?"
{
  "reply": "Para hacer una venta:\n1. Ve al módulo POS 🛒\n2. Selecciona productos\n3. Elige cliente\n4. Procesa el pago\n\n¿Te llevo al POS ahora?",
  "action": null
}

Usuario: "sí" (después de que TÚ preguntaste algo)
{
  "reply": "¡Listo! Abriendo lo que te sugerí 🚀",
  "action": {"type": "navigate", "payload": {"name": "POSModule", "params": {"module": "[LO QUE SUGERISTE]"}, "query": {"filter": "[SI MENCIONASTE ALGÚN FILTRO]"}}}
}

Usuario: "producto más caro"
{
  "reply": "Tu producto más caro es:\n💎 {$context['inventory']['most_expensive']['name']}: $[precio]\n\nTiene [stock] unidades disponibles.",
  "action": null
}

Usuario: "muéstrame los productos inactivos"
{
  "reply": "¡Claro! Te llevo al módulo de productos con el filtro de inactivos activado 📦. Podrás ver, editar o reactivar productos.",
  "action": {"type": "navigate", "payload": {"name": "POSModule", "params": {"module": "products"}, "query": {"filter": "inactive"}}}
}

Usuario: "llévame a facturas"
{
  "reply": "¡Perfecto! Te llevo al módulo de facturas donde podrás ver todas las facturas generadas, crear nuevas y gestionar cobros 📄💰",
  "action": {"type": "navigate", "payload": {"name": "POSModule", "params": {"module": "invoices"}}}
}

Usuario: "quiero ver los reportes"
{
  "reply": "¡Claro! Te llevo a los reportes donde verás:\n📊 Ventas del período\n📈 Gráficos de tendencias\n🏆 Productos más vendidos\n💰 Análisis de ingresos",
  "action": {"type": "navigate", "payload": {"name": "POSModule", "params": {"module": "reports"}}}
}

Usuario: "llévame a configuraciones"
{
  "reply": "¡Perfecto! Abriendo configuraciones donde podrás ajustar los parámetros del sistema ⚙️",
  "action": {"type": "navigate", "payload": {"name": "POSModule", "params": {"module": "settings"}}}
}

Usuario: "ver clientes"
{
  "reply": "¡Claro! Te llevo al módulo de clientes donde podrás ver, crear y editar información de tus clientes 👥",
  "action": {"type": "navigate", "payload": {"name": "POSModule", "params": {"module": "customers"}}}
}

Usuario: "mostrar categorías"
{
  "reply": "¡Perfecto! Te llevo al módulo de categorías donde podrás organizar tus productos 🏷️",
  "action": {"type": "navigate", "payload": {"name": "POSModule", "params": {"module": "categories"}}}
}

Usuario: "productos con bajo stock"
{
  "reply": "Te llevo al módulo de productos con el filtro de bajo stock activado ⚠️. Podrás ver qué productos necesitan reposición.",
  "action": {"type": "navigate", "payload": {"name": "POSModule", "params": {"module": "products"}, "query": {"filter": "low-stock"}}}
}

7. **BÚSQUEDA INTELIGENTE DE FACTURAS**:
   - Entiendes fechas naturales: "ayer" → busca en invoices.yesterday, "hoy" → busca en invoices.today
   - Fechas del sistema: HOY = {$context['system']['today']}, AYER = {$context['system']['yesterday']}
   - Buscas por nombre de cliente (coincidencia parcial en customer_name)
   - SIEMPRE navegas a facturas con query params pre-llenados
   - Cuenta cuántas facturas encontraste y muestra resumen

   Ejemplos:

Usuario: "busca facturas de ayer"
{
  "reply": "Encontré [CUENTA invoices.yesterday] facturas de ayer:\n\n📄 Facturas de ayer:\n[LISTA las facturas de invoices.yesterday mostrando: número, cliente, total]\n\n¿Quieres verlas todas en el módulo de facturas?",
  "action": {"type": "navigate", "payload": {"name": "POSModule", "params": {"module": "invoices"}, "query": {"date": "yesterday"}}}
}

Usuario: "facturas del cliente Fabian"
{
  "reply": "Buscando facturas del cliente Fabian... 🔍\n\n[BUSCA en invoices.recent donde customer_name contenga 'Fabian']\n\nEncontré [X] facturas:\n📄 [Muestra las encontradas]\n\n¿Te llevo al módulo de facturas con esta búsqueda?",
  "action": {"type": "navigate", "payload": {"name": "POSModule", "params": {"module": "invoices"}, "query": {"search": "Fabian"}}}
}

Usuario: "factura de ayer de Fabian"
{
  "reply": "Buscando facturas de AYER del cliente Fabian... 🔍\n\n[FILTRA invoices.yesterday por customer_name que contenga 'Fabian']\n\nResultado:\n📄 [Si encuentras: muestra detalles | Si no: 'No encontré facturas']\n\n¿Quieres abrirla en el módulo de facturas?",
  "action": {"type": "navigate", "payload": {"name": "POSModule", "params": {"module": "invoices"}, "query": {"date": "yesterday", "search": "Fabian"}}}
}

Usuario: "muéstrame las facturas de hoy"
{
  "reply": "Facturas de hoy:\n\n📊 [CUENTA invoices.today] facturas\n💰 Monto total: $[SUMA invoices.today.total]\n\n¿Quieres verlas en detalle?",
  "action": {"type": "navigate", "payload": {"name": "POSModule", "params": {"module": "invoices"}, "query": {"date": "today"}}}
}

Usuario: "entra a configuraciones y mira si hay promociones"
{
  "reply": "¡Claro! Entrando a configuraciones para revisar promociones activas ⚙️\n\n[Nota: Esta funcionalidad requiere que el módulo de promociones esté implementado. Por ahora te llevo a configuraciones]",
  "action": {"type": "navigate", "payload": {"name": "POSModule", "params": {"module": "settings"}}}
}

8. **INSTRUCCIONES DE FECHAS**:
   - "ayer" → busca en invoices.yesterday (fecha exacta disponible en system.yesterday)
   - "hoy" → busca en invoices.today (fecha exacta disponible en system.today)
   - Filtra por customer_name si mencionan un nombre
   - SIEMPRE incluye action para navegar con filtros

9. **BÚSQUEDA DE CLIENTES EN FACTURAS**:
   - Busca coincidencia parcial (case insensitive)
   - Ejemplos: "Fabian", "fabian", "FABIAN" → busca customer_name que contenga "fabian"
   - Usa invoices.recent para búsqueda general
   - Combina fecha + cliente si mencionan ambos

10. **🚀 ACCIONES EJECUTABLES - ¡PUEDES HACER COSAS REALES!**:

   **IMPORTANTE:** Puedes ejecutar acciones reales en el sistema agregando "execute_action" a tu respuesta.

   A) **CREAR DESCUENTOS/CUPONES:**

   Usuario: "crea un descuento del 100% con código caña22 por 1 día"
   {
     "reply": "¡Perfecto! Acabo de crear el descuento:\n🎁 Código: CAÑA22\n💰 Descuento: 100%\n📆 Válido: Solo hoy\n\nTus clientes pueden usar este código en el POS. ¿Quieres enviarlo por WhatsApp a todos los clientes?",
     "action": null,
     "execute_action": {
       "type": "create_discount",
       "params": {
         "name": "Descuento Caña",
         "code": "caña22",
         "type": "percentage",
         "value": 100,
         "duration_days": 1
       }
     }
   }

   Usuario: "crea descuento del 50% código BF50 por 3 días"
   {
     "reply": "¡Descuento Black Friday creado!\n🎁 Código: BF50\n💰 50% de descuento\n📆 Válido por 3 días",
     "execute_action": {
       "type": "create_discount",
       "params": {
         "name": "Black Friday 50%",
         "code": "BF50",
         "type": "percentage",
         "value": 50,
         "duration_days": 3
       }
     }
   }

   Usuario: "crea código X Y envíaselo a [cliente/clientes]"
   {
     "reply": "✅ Descuento creado y enviado por WhatsApp!",
     "execute_action": {
       "type": "create_campaign",
       "params": {
         "discount": {
           "name": "Nombre del descuento",
           "code": "CODIGO",
           "type": "percentage",
           "value": 100,
           "duration_days": 1
         },
         "whatsapp": {
           "message": "🎁 ¡Usa el código CODIGO para [X]% de descuento!",
           "target": "specific",
           "customer_ids": [10]
         }
       }
     }
   }

   B) **ENVIAR WHATSAPP MASIVO:**

   Usuario: "envía por WhatsApp el código caña22 a todos los clientes"
   {
     "reply": "¡Enviando mensaje por WhatsApp a todos los clientes! 📱\n\nMensaje:\n'¡Oferta especial! Usa el código CAÑA22 para 100% de descuento solo hoy 🎁'\n\nEsto puede tomar unos minutos...",
     "execute_action": {
       "type": "send_whatsapp",
       "params": {
         "message": "¡Oferta especial! Usa el código CAÑA22 para 100% de descuento solo hoy 🎁",
         "target": "all"
       }
     }
   }

   Usuario: "envía el descuento solo a clientes activos"
   {
     "reply": "Enviando a clientes activos por WhatsApp...",
     "execute_action": {
       "type": "send_whatsapp",
       "params": {
         "message": "[TU MENSAJE AQUÍ]",
         "target": "active"
       }
     }
   }

   C) **CAMPAÑA COMPLETA (Descuento + WhatsApp):**

   Usuario: "crea una campaña del 30% por 2 días y envíala a todos"
   {
     "reply": "¡Campaña creada y enviándose! 🚀\n\n✅ Descuento 30% creado\n📱 Enviando WhatsApp a clientes...",
     "execute_action": {
       "type": "create_campaign",
       "params": {
         "discount": {
           "name": "Campaña 30%",
           "code": "SAVE30",
           "type": "percentage",
           "value": 30,
           "duration_days": 2
         },
         "whatsapp": {
           "message": "🎉 ¡30% de descuento! Usa el código SAVE30 por 2 días",
           "target": "all"
         }
       }
     }
   }

   **REGLAS PARA ACCIONES:**
   - **IMPORTANTE:** Si el usuario dice "crea X Y envíaselo/mándalo a [cliente]" SIEMPRE usa "create_campaign" (NO solo create_discount)
   - Para buscar cliente por nombre, consulta la base de datos primero
   - Solo USA execute_action cuando el usuario PIDA explícitamente crear/enviar
   - GENERA códigos automáticamente si no mencionan uno
   - duration_days por defecto = 1 día
   - type puede ser "percentage" o "fixed_amount"
   - target puede ser "all", "active", o "specific"
   - Si target="specific" DEBES incluir customer_ids con los IDs correctos del cliente

8. **CREAR PRODUCTOS Y CATEGORÍAS** 📦:

   **FLUJO CONVERSACIONAL PARA CREAR PRODUCTOS:**
   1. Usuario pide crear un producto → Pregunta el NOMBRE
   2. Usuario da nombre → Pregunta el PRECIO DE VENTA
   3. Usuario da precio → Pregunta la CATEGORÍA (muestra categorías disponibles)
   4. Usuario da categoría:
      - Si existe en categories.active_list → Úsala
      - Si NO existe → Ofrece crearla primero
   5. Usuario confirma → Pregunta CANTIDAD EN STOCK (opcional, default 0)
   6. Tienes todos los datos → EJECUTA create_product

   **CATEGORÍAS DISPONIBLES:**
   - Consulta categories.active_list para mostrar categorías existentes
   - Total de categorías: {$context['categories']['total']}
   - Activas: {$context['categories']['active']}

   **CREAR CATEGORÍA PRIMERO (si no existe):**
   Si el usuario menciona una categoría que NO está en categories.active_list:

   {
     "reply": "La categoría '[NOMBRE]' no existe. ¿Quieres que la cree primero? 🏷️",
     "execute_action": {
       "type": "create_category",
       "params": {
         "name": "[NOMBRE CATEGORÍA]",
         "description": "[Descripción breve]"
       }
     }
   }

   **LUEGO CREAR PRODUCTO:**
   Una vez que tengas: nombre, precio, category_id (del contexto o recién creada), y opcionalmente stock:

   {
     "reply": "¡Perfecto! Creando el producto '[NOMBRE]' con precio $[PRECIO] en la categoría '[CATEGORÍA]' con [STOCK] unidades en stock 📦",
     "execute_action": {
       "type": "create_product",
       "params": {
         "name": "[NOMBRE DEL PRODUCTO]",
         "sale_price": [PRECIO NUMÉRICO],
         "category_id": [ID DE LA CATEGORÍA],
         "current_stock": [CANTIDAD O 0],
         "description": "[Descripción opcional]"
       }
     },
     "action": {
       "type": "navigate",
       "payload": {
         "name": "POSModule",
         "params": {"module": "products"}
       }
     }
   }

   **EJEMPLO COMPLETO:**
   Usuario: "me puedes ayudar a crear un producto?"
   TÚ: "¡Claro! Para crear un producto, necesito saber algunos detalles. ¿Cuál es el nombre del producto que deseas crear? 📦"

   Usuario: "Jabón en polvo"
   TÚ: "Perfecto! Vamos a crear 'Jabón en polvo'. ¿Cuál será el precio de venta?"

   Usuario: "10000"
   TÚ: "Genial! Ahora dime en qué categoría va. Las categorías disponibles son:\n[LISTA categories.active_list]\n\n¿Cuál prefieres?"

   Usuario: "Aseo Personal"
   TÚ (si existe): "Perfecto! ¿Cuántas unidades tendrás en stock inicialmente? (puedes decir 0 si no tienes aún)"
   TÚ (si NO existe): "La categoría 'Aseo Personal' no existe. ¿Quieres que la cree primero? 🏷️"

   Usuario: "100" o "si, créala"
   TÚ: {crea categoría si hace falta, luego crea producto con todos los datos}

🎯 MÓDULOS DISPONIBLES (úsalos en params.module):
- products (productos) 📦
- pos (punto de venta) 🛒
- dashboard (panel principal) 📊
- invoices (facturas) 📄
- customers (clientes) 👥
- suppliers (proveedores) 🚚
- categories (categorías) 🏷️
- reports (reportes) 📈
- settings (configuraciones) ⚙️

🔍 FILTROS DISPONIBLES (úsalos en query.filter para productos):
- inactive (inactivos)
- low-stock (bajo stock)
- active (activos)
- category:[nombre] (por categoría)

⚡ RECUERDA: Si el usuario responde "sí" o afirmativamente a una sugerencia tuya, NAVEGA al módulo que sugeriste.

🔧 FORMATO DE RESPUESTA (JSON):
SIEMPRE responde en formato JSON con esta estructura:
{
  "reply": "texto amigable",
  "action": objeto navegación o null,
  "execute_action": objeto acción ejecutable o OMITIR
}

Fecha actual: {$context['system']['current_date']}

Pregunta del usuario:
EOT;
    }

    /**
     * Call the Groq API with conversation history.
     */
    private function callGroqAPI($systemPrompt, $userMessage, $conversationHistory = [])
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
            return json_encode([
                'reply' => 'Error: No se han configurado API Keys de Groq. Por favor contacta al administrador.',
                'action' => null
            ]);
        }

        // Intentar con cada API key hasta encontrar una que funcione
        $lastError = null;
        $startTime = microtime(true);

        foreach ($apiKeys as $index => $apiKey) {
            $keyIndex = $index + 1;
            $keyLast4 = substr($apiKey, -4);
            Log::info("[Groq API] Intentando con API Key #{$keyIndex}");

            // Construir array de mensajes con historial
            $messages = [
                ['role' => 'system', 'content' => $systemPrompt]
            ];

            // Añadir historial de conversación (si existe)
            if (!empty($conversationHistory)) {
                $messages = array_merge($messages, $conversationHistory);
            }

            // Añadir mensaje actual del usuario
            $messages[] = ['role' => 'user', 'content' => $userMessage];

            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => 'llama-3.3-70b-versatile',
            'messages' => $messages,
            'temperature' => 0.3, // Más consistente y precisa
            'max_tokens' => 500, // Respuestas concisas para ahorrar tokens
            'top_p' => 0.9,
            'response_format' => ['type' => 'json_object'], // Forzar JSON
        ]);

            $responseTime = (int)((microtime(true) - $startTime) * 1000); // ms

            if ($response->successful()) {
                Log::info("[Groq API] ✅ Respuesta exitosa con API Key #{$keyIndex}");

                $responseData = $response->json();
                $content = $responseData['choices'][0]['message']['content'] ?? null;

                // Extraer métricas de uso de tokens
                $usage = $responseData['usage'] ?? [];
                $promptTokens = $usage['prompt_tokens'] ?? 0;
                $completionTokens = $usage['completion_tokens'] ?? 0;
                $totalTokens = $usage['total_tokens'] ?? 0;

                // Registrar uso exitoso
                try {
                    AiUsageLog::create([
                        'user_id' => auth()->id(),
                        'api_key_index' => $keyIndex,
                        'api_key_last_4' => $keyLast4,
                        'user_message' => substr($userMessage, 0, 1000), // Limitar longitud
                        'prompt_tokens' => $promptTokens,
                        'completion_tokens' => $completionTokens,
                        'total_tokens' => $totalTokens,
                        'status' => 'success',
                        'response_time_ms' => $responseTime,
                        'model' => 'llama-3.3-70b-versatile',
                        'endpoint' => 'chat',
                        'ip_address' => request()->ip(),
                    ]);
                } catch (\Exception $e) {
                    Log::error("[AI Usage Log] Error guardando log: " . $e->getMessage());
                }

                if (!$content) {
                    return json_encode([
                        'reply' => 'No pude generar una respuesta. Por favor intenta de nuevo.',
                        'action' => null
                    ]);
                }

                // Verificar que sea JSON válido
                $decoded = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $content; // Ya es JSON válido
                }

                // Si no es JSON válido, envolver en estructura correcta
                return json_encode([
                    'reply' => $content,
                    'action' => null
                ]);
            }

            // Si falló, verificar si es rate limit
            $statusCode = $response->status();
            $errorBody = $response->body();

            if ($statusCode === 429) {
                Log::warning("[Groq API] ⚠️ Rate limit alcanzado en API Key #{$keyIndex}, probando siguiente...");

                // Registrar rate limit
                try {
                    AiUsageLog::create([
                        'user_id' => auth()->id(),
                        'api_key_index' => $keyIndex,
                        'api_key_last_4' => $keyLast4,
                        'user_message' => substr($userMessage, 0, 1000),
                        'status' => 'rate_limited',
                        'error_message' => 'Rate limit exceeded',
                        'response_time_ms' => $responseTime,
                        'model' => 'llama-3.3-70b-versatile',
                        'endpoint' => 'chat',
                        'ip_address' => request()->ip(),
                    ]);
                } catch (\Exception $e) {
                    Log::error("[AI Usage Log] Error guardando rate limit log: " . $e->getMessage());
                }

                $lastError = "Rate limit excedido";
                continue; // Probar con siguiente API key
            }

            // Si es otro error, también intentar con siguiente
            Log::error("[Groq API] ❌ Error {$statusCode} con API Key #{$keyIndex}: {$errorBody}");

            // Registrar error
            try {
                AiUsageLog::create([
                    'user_id' => auth()->id(),
                    'api_key_index' => $keyIndex,
                    'api_key_last_4' => $keyLast4,
                    'user_message' => substr($userMessage, 0, 1000),
                    'status' => 'error',
                    'error_message' => substr($errorBody, 0, 1000),
                    'response_time_ms' => $responseTime,
                    'model' => 'llama-3.3-70b-versatile',
                    'endpoint' => 'chat',
                    'ip_address' => request()->ip(),
                ]);
            } catch (\Exception $e) {
                Log::error("[AI Usage Log] Error guardando error log: " . $e->getMessage());
            }

            $lastError = $errorBody;
            continue;
        }

        // Si todas las API keys fallaron
        Log::error("[Groq API] ❌ Todas las API Keys agotadas. Último error: {$lastError}");
        return json_encode([
            'reply' => 'Lo siento, el servicio de IA está temporalmente saturado. Por favor intenta de nuevo en unos momentos. 🔄',
            'action' => null
        ]);
    }

    /**
     * Ejecutar acción solicitada por la IA
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
     * Crear descuento desde IA
     */
    private function createDiscountAction($params)
    {
        $controller = new \App\Http\Controllers\Api\AIActionsController();
        $request = new \Illuminate\Http\Request($params);
        $response = $controller->createDiscount($request);

        return $response->getData(true);
    }

    /**
     * Enviar WhatsApp masivo desde IA
     */
    private function sendWhatsAppAction($params)
    {
        $controller = new \App\Http\Controllers\Api\AIActionsController();
        $request = new \Illuminate\Http\Request($params);
        $response = $controller->sendBulkWhatsApp($request);

        return $response->getData(true);
    }

    /**
     * Crear campaña completa desde IA
     */
    private function createCampaignAction($params)
    {
        $controller = new \App\Http\Controllers\Api\AIActionsController();
        $request = new \Illuminate\Http\Request($params);
        $response = $controller->createCampaign($request);

        return $response->getData(true);
    }

    /**
     * Crear producto desde IA
     */
    private function createProductAction($params)
    {
        $controller = new \App\Http\Controllers\Api\AIActionsController();
        $request = new \Illuminate\Http\Request($params);
        $response = $controller->createProduct($request);

        return $response->getData(true);
    }

    /**
     * Crear categoría desde IA
     */
    private function createCategoryAction($params)
    {
        $controller = new \App\Http\Controllers\Api\AIActionsController();
        $request = new \Illuminate\Http\Request($params);
        $response = $controller->createCategory($request);

        return $response->getData(true);
    }

    /**
     * Limpiar historial de conversación (nueva conversación)
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

            // Eliminar historial de esta sesión
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
