<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Customer;
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
            $validated = $request->validate([
                'message' => 'required|string|max:1000',
                'target' => 'required|in:all,active,specific',
                'customer_ids' => 'nullable|array',
                'customer_ids.*' => 'integer|exists:customers,id',
            ]);

            // Obtener clientes según target
            $customers = $this->getTargetCustomers($validated['target'], $validated['customer_ids'] ?? []);

            if ($customers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron clientes para enviar el mensaje'
                ], 404);
            }

            // Verificar que WhatsApp esté conectado
            $whatsappStatus = $this->checkWhatsAppStatus();
            if (!$whatsappStatus['connected']) {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp no está conectado. Por favor, conecta WhatsApp primero.'
                ], 503);
            }

            // Enviar mensajes
            $sent = 0;
            $failed = 0;
            $errors = [];

            foreach ($customers as $customer) {
                if (empty($customer->phone)) {
                    $failed++;
                    $errors[] = "Cliente {$customer->name} no tiene teléfono";
                    continue;
                }

                $result = $this->sendWhatsAppMessage($customer->phone, $validated['message'], $customer->name);

                if ($result['success']) {
                    $sent++;
                } else {
                    $failed++;
                    $errors[] = "Error enviando a {$customer->name}: {$result['error']}";
                }

                // Esperar 1 segundo entre mensajes para evitar spam
                sleep(1);
            }

            Log::info('📱 IA envió mensajes WhatsApp masivos:', [
                'total_clientes' => $customers->count(),
                'enviados' => $sent,
                'fallidos' => $failed,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Mensajes enviados: {$sent} exitosos, {$failed} fallidos",
                'stats' => [
                    'total' => $customers->count(),
                    'sent' => $sent,
                    'failed' => $failed,
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
            $response = Http::timeout(5)->get('http://localhost:3000/status');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'connected' => $data['isReady'] ?? false,
                    'status' => $data['status'] ?? 'disconnected',
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
            // Formatear número (agregar código de país si no existe)
            $formattedPhone = $this->formatPhoneNumber($phone);

            $response = Http::timeout(10)->post('http://localhost:3000/send-message', [
                'number' => $formattedPhone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'customer' => $customerName,
                    'phone' => $formattedPhone,
                ];
            }

            return [
                'success' => false,
                'error' => 'Error en la respuesta del servidor WhatsApp',
                'phone' => $formattedPhone,
            ];

        } catch (\Exception $e) {
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
        // Eliminar espacios, guiones, paréntesis
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Si no tiene código de país, agregar +57 (Colombia)
        if (!str_starts_with($phone, '57') && strlen($phone) === 10) {
            $phone = '57' . $phone;
        }

        return $phone;
    }
}
