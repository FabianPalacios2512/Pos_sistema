<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Controlador de acciones ejecutables por la IA
 *
 * Este controlador permite que la IA ejecute acciones reales en el sistema:
 * - Crear descuentos/cupones
 * - Enviar mensajes WhatsApp masivos
 * - Automatizar campañas de marketing
 */
class AIActionsController extends Controller
{
    /**
     * Crear un descuento/cupón desde la IA
     *
     * POST /api/ai/actions/create-discount
     *
     * Body:
     * {
     *   "name": "Descuento Caña",
     *   "code": "caña22",
     *   "type": "percentage",
     *   "value": 100,
     *   "duration_days": 1,
     *   "description": "Descuento especial del 100%"
     * }
     */
    public function createDiscount(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:discounts,code',
                'type' => 'required|in:percentage,fixed_amount',
                'value' => 'required|numeric|min:0|max:100',
                'duration_days' => 'nullable|integer|min:1',
                'description' => 'nullable|string',
                'minimum_amount' => 'nullable|numeric|min:0',
                'usage_limit' => 'nullable|integer|min:1',
            ]);

            // Validar porcentaje
            if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
                return response()->json([
                    'success' => false,
                    'message' => 'El porcentaje no puede ser mayor a 100%'
                ], 422);
            }

            // Configurar fechas
            $starts_at = Carbon::now();
            $expires_at = isset($validated['duration_days'])
                ? Carbon::now()->addDays($validated['duration_days'])
                : null;

            // Crear descuento
            $discount = Discount::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? "Descuento creado por IA: {$validated['name']}",
                'code' => strtoupper($validated['code']),
                'type' => $validated['type'],
                'value' => $validated['value'],
                'applies_to' => 'all_products',
                'minimum_amount' => $validated['minimum_amount'] ?? 0,
                'maximum_discount' => null,
                'usage_limit' => $validated['usage_limit'] ?? null,
                'used_count' => 0,
                'starts_at' => $starts_at,
                'expires_at' => $expires_at,
                'active' => true,
                'stackable' => false,
                'allow_multiple_uses_per_user' => false,
            ]);

            Log::info('🎁 IA creó descuento:', [
                'id' => $discount->id,
                'code' => $discount->code,
                'value' => $discount->value,
                'type' => $discount->type,
                'expires_at' => $discount->expires_at?->format('Y-m-d H:i:s'),
            ]);

            return response()->json([
                'success' => true,
                'message' => '¡Descuento creado exitosamente!',
                'discount' => [
                    'id' => $discount->id,
                    'name' => $discount->name,
                    'code' => $discount->code,
                    'type' => $discount->type,
                    'value' => $discount->value,
                    'starts_at' => $discount->starts_at->format('d/m/Y H:i'),
                    'expires_at' => $discount->expires_at?->format('d/m/Y H:i') ?? 'Sin expiración',
                    'usage_limit' => $discount->usage_limit ?? 'Ilimitado',
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('❌ Error creando descuento desde IA:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear descuento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar mensaje WhatsApp masivo a clientes
     *
     * POST /api/ai/actions/send-bulk-whatsapp
     *
     * Body:
     * {
     *   "message": "¡Usa el código CAÑA22 para 100% descuento hoy!",
     *   "target": "all|active|specific",
     *   "customer_ids": [1,2,3] (opcional, solo si target=specific)
     * }
     */
    public function sendBulkWhatsApp(Request $request)
    {
        try {
            Log::info('📨 [sendBulkWhatsApp] Request recibido:', [
                'all_data' => $request->all(),
                'target' => $request->input('target'),
                'customer_ids' => $request->input('customer_ids'),
            ]);

            $validated = $request->validate([
                'message' => 'required|string|max:1000',
                'target' => 'required|in:all,active,specific',
                'customer_ids' => 'nullable|array',
                'customer_ids.*' => 'integer|exists:customers,id',
            ]);

            Log::info('✅ [sendBulkWhatsApp] Datos validados:', [
                'validated' => $validated,
                'customer_ids_count' => count($validated['customer_ids'] ?? []),
            ]);

            // Obtener clientes según target
            $customers = $this->getTargetCustomers($validated['target'], $validated['customer_ids'] ?? []);

            if ($customers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron clientes para enviar el mensaje'
                ], 404);
            }

            // ✅ NORMALIZAR teléfonos ANTES de filtrar duplicados
            $customers = $customers->map(function($customer) {
                $normalized = preg_replace('/[^0-9]/', '', $customer->phone); // Eliminar +, -, espacios
                $customer->normalized_phone = $normalized;

                Log::info("🔧 [NORMALIZACIÓN]", [
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'phone_original' => $customer->phone,
                    'phone_normalized' => $normalized
                ]);

                return $customer;
            });

            // ✅ Filtrar duplicados por teléfono NORMALIZADO usando groupBy + first
            $seenPhones = [];
            $uniqueCustomers = collect();

            foreach ($customers as $customer) {
                $normalizedPhone = $customer->normalized_phone;

                if (!isset($seenPhones[$normalizedPhone])) {
                    $seenPhones[$normalizedPhone] = true;
                    $uniqueCustomers->push($customer);
                }
            }

            $duplicatesRemoved = $customers->count() - $uniqueCustomers->count();

            Log::info("📊 [FILTRADO DUPLICADOS]", [
                'total_customers' => $customers->count(),
                'unique_customers' => $uniqueCustomers->count(),
                'duplicates_removed' => $duplicatesRemoved,
                'unique_ids' => $uniqueCustomers->pluck('id')->toArray(),
                'unique_phones' => $uniqueCustomers->pluck('normalized_phone')->toArray()
            ]);

            if ($duplicatesRemoved > 0) {
                Log::info("📱 Se removieron {$duplicatesRemoved} números duplicados", [
                    'total' => $customers->count(),
                    'unique' => $uniqueCustomers->count(),
                    'removed' => $duplicatesRemoved
                ]);
            }

            // Verificar que WhatsApp esté conectado
            $whatsappStatus = $this->checkWhatsAppStatus();
            if (!$whatsappStatus['connected']) {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp no está conectado. Por favor, conecta WhatsApp primero.'
                ], 503);
            }

            // Enviar mensajes (solo a números únicos y válidos)
            $sent = 0;
            $failed = 0;
            $errors = [];
            $skipped = 0;

            foreach ($uniqueCustomers as $customer) {
                // ✅ Validar que el teléfono no esté vacío
                if (empty($customer->phone)) {
                    $skipped++;
                    $errors[] = "❌ Cliente '{$customer->name}' (ID: {$customer->id}) no tiene teléfono";
                    continue;
                }

                // ✅ Validar que el número sea válido ANTES de enviar
                if (!$this->isValidPhoneNumber($customer->phone)) {
                    $skipped++;
                    $errors[] = "❌ Cliente '{$customer->name}' (ID: {$customer->id}) tiene número inválido: {$customer->phone}";
                    Log::warning("⚠️ Número inválido omitido", [
                        'customer_id' => $customer->id,
                        'customer_name' => $customer->name,
                        'phone' => $customer->phone
                    ]);
                    continue;
                }

                $result = $this->sendWhatsAppMessage($customer->phone, $validated['message'], $customer->name);

                if ($result['success']) {
                    $sent++;
                } else {
                    $failed++;
                    $errors[] = "Error enviando a {$customer->name}: {$result['error']}";
                }

                // Delay de 500ms entre mensajes (más seguro para evitar ban)
                usleep(500000); // 500ms
            }

            Log::info('📱 IA envió mensajes WhatsApp masivos:', [
                'total_clientes' => $customers->count(),
                'numeros_unicos' => $uniqueCustomers->count(),
                'duplicados_removidos' => $duplicatesRemoved,
                'enviados' => $sent,
                'fallidos' => $failed,
                'omitidos' => $skipped,
            ]);

            return response()->json([
                'success' => true,
                'message' => "✅ Mensajes: {$sent} enviados, {$failed} fallidos, {$skipped} omitidos" .
                            ($duplicatesRemoved > 0 ? " ({$duplicatesRemoved} duplicados)" : ""),
                'stats' => [
                    'total' => $customers->count(),
                    'unique' => $uniqueCustomers->count(),
                    'duplicates_removed' => $duplicatesRemoved,
                    'sent' => $sent,
                    'failed' => $failed,
                    'skipped' => $skipped,
                    'errors' => $errors,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error enviando WhatsApp masivo desde IA:', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar mensajes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear campaña automática: crear descuento + enviar WhatsApp
     *
     * POST /api/ai/actions/create-campaign
     *
     * Body:
     * {
     *   "discount": {
     *     "name": "Black Friday",
     *     "code": "BF2025",
     *     "value": 50,
     *     "type": "percentage",
     *     "duration_days": 3
     *   },
     *   "whatsapp": {
     *     "message": "¡Black Friday! 50% descuento con código BF2025",
     *     "target": "active"
     *   }
     * }
     */
    public function createCampaign(Request $request)
    {
        try {
            $validated = $request->validate([
                'discount' => 'required|array',
                'discount.name' => 'required|string',
                'discount.code' => 'required|string|unique:discounts,code',
                'discount.value' => 'required|numeric|min:0|max:100',
                'discount.type' => 'required|in:percentage,fixed_amount',
                'discount.duration_days' => 'nullable|integer|min:1',
                'whatsapp' => 'nullable|array',
                'whatsapp.message' => 'nullable|string',
                'whatsapp.target' => 'nullable|in:all,active,specific',
                'whatsapp.customer_ids' => 'nullable|array', // ✅ AGREGADO
                'whatsapp.customer_ids.*' => 'nullable|integer|exists:customers,id', // ✅ AGREGADO
            ]);

            // 1. Crear descuento
            $discountRequest = new Request($validated['discount']);
            $discountResponse = $this->createDiscount($discountRequest);
            $discountData = $discountResponse->getData(true);

            if (!$discountData['success']) {
                return response()->json($discountData, 422);
            }

            $discount = $discountData['discount'];

            // 2. Enviar WhatsApp si está configurado
            $whatsappResult = null;
            if (isset($validated['whatsapp']) && isset($validated['whatsapp']['message'])) {
                $whatsappRequest = new Request($validated['whatsapp']);
                $whatsappResponse = $this->sendBulkWhatsApp($whatsappRequest);
                $whatsappResult = $whatsappResponse->getData(true);
            }

            Log::info('🚀 IA creó campaña completa:', [
                'discount_code' => $discount['code'],
                'whatsapp_sent' => $whatsappResult['stats']['sent'] ?? 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => '¡Campaña creada y enviada exitosamente!',
                'discount' => $discount,
                'whatsapp' => $whatsappResult,
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error creando campaña desde IA:', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear campaña: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== MÉTODOS AUXILIARES ====================

    /**
     * Obtener clientes según el target
     */
    private function getTargetCustomers(string $target, array $customerIds = [])
    {
        $query = Customer::query();

        switch ($target) {
            case 'all':
                return $query->get();

            case 'active':
                return $query->where('active', true)->get();

            case 'specific':
                return $query->whereIn('id', $customerIds)->get();

            default:
                return collect([]);
        }
    }

    /**
     * Verificar estado de WhatsApp
     */
    private function checkWhatsAppStatus()
    {
        try {
            $response = Http::timeout(5)->get('http://localhost:3002/status');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'connected' => $data['connected'] ?? false,
                    'status' => $data['connected'] ? 'connected' : 'disconnected',
                ];
            }
        } catch (\Exception $e) {
            Log::warning('WhatsApp status check failed:', ['error' => $e->getMessage()]);
        }

        return ['connected' => false, 'status' => 'disconnected'];
    }

    /**
     * Enviar mensaje de WhatsApp individual
     */
    private function sendWhatsAppMessage(string $phone, string $message, string $customerName = '')
    {
        try {
            // ✅ ID único para rastrear cada envío
            $messageId = uniqid('msg_', true);

            // ✅ VALIDAR número antes de formatear
            if (!$this->isValidPhoneNumber($phone)) {
                Log::warning("⚠️ Número inválido rechazado", [
                    'message_id' => $messageId,
                    'phone' => $phone,
                    'customer' => $customerName
                ]);

                return [
                    'success' => false,
                    'error' => "Número inválido: {$phone}",
                    'phone' => $phone,
                ];
            }

            // Formatear número (agregar código de país si no existe)
            $formattedPhone = $this->formatPhoneNumber($phone);

            Log::info("📤 [INICIO ENVÍO] ID: {$messageId}", [
                'message_id' => $messageId,
                'phone_original' => $phone,
                'phone_formatted' => $formattedPhone,
                'customer' => $customerName,
                'timestamp' => now()->format('Y-m-d H:i:s.u')
            ]);

            // ✅ Timeout aumentado a 15 segundos para evitar falsos timeouts
            $response = Http::timeout(15)->post('http://localhost:3002/send', [
                'phone' => $formattedPhone,
                'message' => $message,
            ]);

            Log::info("📥 [RESPUESTA] ID: {$messageId}", [
                'message_id' => $messageId,
                'status' => $response->status(),
                'body' => $response->body(),
                'successful' => $response->successful(),
                'timestamp' => now()->format('Y-m-d H:i:s.u')
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Verificar si el envío fue exitoso
                if (isset($data['success']) && $data['success']) {
                    Log::info("✅ WhatsApp enviado correctamente a {$formattedPhone}");
                    return [
                        'success' => true,
                        'customer' => $customerName,
                        'phone' => $formattedPhone,
                    ];
                }

                Log::warning("⚠️ WhatsApp falló (sin success=true)", ['data' => $data]);
                return [
                    'success' => false,
                    'error' => $data['error'] ?? 'Error desconocido del servidor WhatsApp',
                    'phone' => $formattedPhone,
                ];
            }

            Log::error("❌ WhatsApp HTTP error", ['status' => $response->status()]);
            return [
                'success' => false,
                'error' => 'Error en la respuesta del servidor WhatsApp (HTTP ' . $response->status() . ')',
                'phone' => $formattedPhone,
            ];

        } catch (\Exception $e) {
            Log::error("💥 Excepción enviando WhatsApp", [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'phone' => $phone,
            ];
        }
    }

    /**
     * Formatear número de teléfono para WhatsApp
     */
    private function formatPhoneNumber(string $phone)
    {
        // Eliminar espacios, guiones, paréntesis, signos +
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // ✅ VALIDAR que el número sea válido (10 dígitos colombianos)
        if (strlen($phone) === 10 && preg_match('/^3[0-9]{9}$/', $phone)) {
            // Número válido de 10 dígitos que empieza con 3
            $phone = '57' . $phone;
        } elseif (strlen($phone) === 12 && str_starts_with($phone, '57')) {
            // Ya tiene código de país
            $phone = $phone;
        } else {
            // Número inválido
            throw new \Exception("Número de teléfono inválido: {$phone} (debe ser 10 dígitos iniciando con 3)");
        }

        return $phone;
    }

    /**
     * Validar si un número de teléfono es válido para WhatsApp
     */
    private function isValidPhoneNumber(string $phone): bool
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // Validar formatos aceptados:
        // - 10 dígitos empezando con 3 (Colombia): 3001234567
        // - 12 dígitos empezando con 57 (Colombia con código): 573001234567
        return (strlen($cleaned) === 10 && preg_match('/^3[0-9]{9}$/', $cleaned)) ||
               (strlen($cleaned) === 12 && preg_match('/^573[0-9]{9}$/', $cleaned));
    }

    /**
     * Crear un producto desde la IA
     *
     * POST /api/ai/actions/create-product
     *
     * Body:
     * {
     *   "name": "Jabón en polvo",
     *   "sale_price": 10000,
     *   "category_id": 5,
     *   "current_stock": 100,
     *   "description": "Jabón en polvo marca X"
     * }
     */
    public function createProduct(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category_id' => 'required|exists:categories,id',
                'sale_price' => 'required|numeric|min:0',
                'cost_price' => 'nullable|numeric|min:0',
                'current_stock' => 'nullable|integer|min:0',
                'min_stock' => 'nullable|integer|min:0',
                'sku' => 'nullable|string|unique:products,sku',
                'barcode' => 'nullable|string|unique:products,barcode',
            ]);

            // Generar SKU automático si no se proporciona
            if (empty($validated['sku'])) {
                $validated['sku'] = 'PROD-' . strtoupper(Str::random(8));
            }

            // Valores por defecto
            $productData = array_merge([
                'cost_price' => $validated['sale_price'] * 0.6, // 40% de margen por defecto
                'current_stock' => 0,
                'min_stock' => 5,
                'manage_stock' => true,
                'active' => true,
                'unit' => 'unidad',
            ], $validated);

            // Crear producto
            $product = Product::create($productData);

            Log::info('📦 IA creó producto:', [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'category_id' => $product->category_id,
                'sale_price' => $product->sale_price,
                'stock' => $product->current_stock,
            ]);

            return response()->json([
                'success' => true,
                'message' => '¡Producto creado exitosamente!',
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category_id' => $product->category_id,
                    'category_name' => $product->category->name ?? 'Sin categoría',
                    'sale_price' => $product->sale_price,
                    'cost_price' => $product->cost_price,
                    'current_stock' => $product->current_stock,
                    'min_stock' => $product->min_stock,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('❌ Error creando producto desde IA: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear una categoría desde la IA
     *
     * POST /api/ai/actions/create-category
     *
     * Body:
     * {
     *   "name": "Aseo Personal",
     *   "description": "Productos de aseo y cuidado personal"
     * }
     */
    public function createCategory(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'nullable|string',
            ]);

            $category = Category::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? "Categoría creada por IA: {$validated['name']}",
                'active' => true,
            ]);

            Log::info('🏷️ IA creó categoría:', [
                'id' => $category->id,
                'name' => $category->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => '¡Categoría creada exitosamente!',
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('❌ Error creando categoría desde IA: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la categoría: ' . $e->getMessage()
            ], 500);
        }
    }
}

