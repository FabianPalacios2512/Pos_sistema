<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Tenant;

class WompiPaymentController extends Controller
{
    private $publicKey;
    private $privateKey;
    private $eventsSecret;
    private $integritySecret;
    private $apiUrl;
    private $currency;

    public function __construct()
    {
        $this->publicKey = config('services.wompi.public_key');
        $this->privateKey = config('services.wompi.private_key');
        $this->eventsSecret = config('services.wompi.events_secret');
        $this->integritySecret = config('services.wompi.integrity_secret');
        $this->apiUrl = config('services.wompi.api_url');
        $this->currency = config('services.wompi.currency');
    }

    /**
     * Crear una transacción de pago con Wompi
     */
    public function createTransaction(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount_in_cents' => 'required|integer|min:100',
                'reference' => 'required|string',
                'customer_email' => 'required|email',
                'customer_data' => 'sometimes|array',
                'redirect_url' => 'sometimes|url',
            ]);

            // Generar firma de integridad
            $concatenatedString =
                $validated['reference'] .
                $validated['amount_in_cents'] .
                $this->currency;

            $signature = hash('sha256', $concatenatedString . $this->integritySecret);

            $paymentData = [
                'public_key' => $this->publicKey,
                'currency' => $this->currency,
                'amount_in_cents' => $validated['amount_in_cents'],
                'reference' => $validated['reference'],
                'signature' => [
                    'integrity' => $signature
                ],
                'redirect_url' => $validated['redirect_url'] ?? config('app.url') . '/payment/success',
                'customer_email' => $validated['customer_email'],
            ];

            // Agregar datos del cliente si existen
            if (isset($validated['customer_data'])) {
                $paymentData['customer_data'] = $validated['customer_data'];
            }

            Log::info('Wompi: Creando transacción', $paymentData);

            return response()->json([
                'success' => true,
                'data' => $paymentData,
                'checkout_url' => 'https://checkout.wompi.co/p/',
            ]);

        } catch (\Exception $e) {
            Log::error('Error creando transacción Wompi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear un link de pago directo
     */
    public function createPaymentLink(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount_in_cents' => 'required|integer|min:100',
                'reference' => 'required|string',
                'customer_email' => 'required|email',
                'description' => 'sometimes|string',
                'redirect_url' => 'sometimes|url',
                // 🔥 CRÍTICO: Validar payment_frequency y plan para calcular subscription_ends_at correctamente
                'payment_frequency' => 'required|in:monthly,yearly,24months',
                'plan' => 'required|in:basic,premium,enterprise',
                'tenant_id' => 'required|string',
            ]);

            // Obtener URL de redirección
            $redirectUrl = $validated['redirect_url'] ?? config('app.url') . '/payment/success';

            // 🔍 Log para debug
            Log::info('Wompi: Creando link de pago', [
                'redirect_url' => $redirectUrl,
                'amount_in_cents' => $validated['amount_in_cents'],
                'reference' => $validated['reference'],
                'payment_frequency' => $validated['payment_frequency'],
                'plan' => $validated['plan'],
                'tenant_id' => $validated['tenant_id'],
            ]);

            // Crear transacción
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->privateKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/payment_links', [
                'name' => $validated['description'] ?? 'Pago ' . $validated['reference'],
                'description' => $validated['description'] ?? 'Pago de plan',
                'single_use' => true,
                'collect_shipping' => false,
                'currency' => $this->currency,
                'amount_in_cents' => $validated['amount_in_cents'],
                'redirect_url' => $redirectUrl,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Wompi: Link de pago creado', $data);

                // Wompi devuelve el ID del payment link, construimos la URL
                $paymentLinkId = $data['data']['id'];
                $paymentLinkUrl = 'https://checkout.wompi.co/l/' . $paymentLinkId;

                // 🔥 GUARDAR datos del pago pendiente para recuperarlos en el webhook
                \App\Models\PendingPayment::create([
                    'reference' => $validated['reference'],
                    'tenant_id' => $validated['tenant_id'],
                    'plan' => $validated['plan'],
                    'payment_frequency' => $validated['payment_frequency'],
                    'amount_in_cents' => $validated['amount_in_cents'],
                    'customer_email' => $validated['customer_email'],
                    'payment_link_id' => $paymentLinkId,
                    'status' => 'pending',
                ]);

                Log::info('Wompi: Pending payment guardado', [
                    'reference' => $validated['reference'],
                    'payment_frequency' => $validated['payment_frequency'],
                    'plan' => $validated['plan'],
                ]);

                return response()->json([
                    'success' => true,
                    'payment_link_url' => $paymentLinkUrl,
                    'payment_link_id' => $paymentLinkId,
                    'data' => $data['data'],
                ]);
            }

            throw new \Exception('Error en la API de Wompi: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Error creando link de pago Wompi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook para recibir notificaciones de Wompi
     */
    public function webhook(Request $request)
    {
        try {
            // Verificar firma del evento
            $signature = $request->header('X-Event-Checksum');
            $eventData = $request->all();

            $expectedSignature = hash_hmac('sha256', json_encode($eventData), $this->eventsSecret);

            if ($signature !== $expectedSignature) {
                Log::warning('Wompi Webhook: Firma inválida');
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            $event = $request->input('event');
            $data = $request->input('data');

            Log::info('Wompi Webhook recibido', [
                'event' => $event,
                'transaction_id' => $data['transaction']['id'] ?? null,
                'status' => $data['transaction']['status'] ?? null,
            ]);

            // Procesar según el tipo de evento
            switch ($event) {
                case 'transaction.updated':
                    $this->handleTransactionUpdated($data);
                    break;

                default:
                    Log::info('Evento Wompi no manejado: ' . $event);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error procesando webhook Wompi: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Manejar actualización de transacción
     */
    private function handleTransactionUpdated($data)
    {
        $transaction = $data['transaction'];
        $reference = $transaction['reference'];
        $status = $transaction['status'];

        Log::info('Transacción actualizada', [
            'reference' => $reference,
            'status' => $status,
            'payment_method_type' => $transaction['payment_method_type'] ?? null,
        ]);

        // 🔥 BUSCAR datos del pago pendiente para obtener payment_frequency y plan
        $pendingPayment = \App\Models\PendingPayment::where('reference', $reference)->first();

        if (!$pendingPayment) {
            Log::warning('Wompi: No se encontró pending_payment para reference: ' . $reference);
            return;
        }

        Log::info('Wompi: Pending payment encontrado', [
            'tenant_id' => $pendingPayment->tenant_id,
            'plan' => $pendingPayment->plan,
            'payment_frequency' => $pendingPayment->payment_frequency,
        ]);

        if ($status === 'APPROVED') {
            // 🔥 Activar plan con la duración correcta según payment_frequency
            $this->activateTenantPlan($pendingPayment, $transaction);

            // Marcar pago como completado
            $pendingPayment->status = 'completed';
            $pendingPayment->save();
        } elseif ($status === 'DECLINED' || $status === 'ERROR') {
            // Marcar pago como fallido
            $pendingPayment->status = 'failed';
            $pendingPayment->save();
        }
    }

    /**
     * Activar plan del tenant después de pago exitoso
     * 🔥 CRÍTICO: Calcula subscription_ends_at correctamente según payment_frequency
     */
    private function activateTenantPlan($pendingPayment, $transaction)
    {
        try {
            $tenant = Tenant::find($pendingPayment->tenant_id);

            if (!$tenant) {
                Log::error('Tenant no encontrado: ' . $pendingPayment->tenant_id);
                return;
            }

            // 🔥 CALCULAR subscription_ends_at según payment_frequency
            $subscriptionEndsAt = match($pendingPayment->payment_frequency) {
                'yearly' => now()->addYear(),
                '24months' => now()->addYears(2), // ✅ 24 meses = 2 años
                default => now()->addMonth(), // monthly
            };

            // Actualizar tenant con plan y fecha de expiración
            $tenant->plan = $pendingPayment->plan;
            $tenant->plan_status = 'active';
            $tenant->subscription_ends_at = $subscriptionEndsAt;
            $tenant->save();

            Log::info('✅ Plan activado correctamente', [
                'tenant_id' => $pendingPayment->tenant_id,
                'plan' => $pendingPayment->plan,
                'payment_frequency' => $pendingPayment->payment_frequency,
                'subscription_ends_at' => $subscriptionEndsAt->toDateTimeString(),
                'transaction_id' => $transaction['id'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('Error activando plan: ' . $e->getMessage(), [
                'tenant_id' => $pendingPayment->tenant_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Consultar estado de una transacción
     */
    public function getTransactionStatus($transactionId)
    {
        try {
            // Si es una referencia (no ID numérico), buscar por referencia
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->publicKey,
            ])->get($this->apiUrl . '/transactions/' . $transactionId);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'transaction' => $response->json('data'),
                ]);
            }

            // Si falla, podría ser una referencia, intentar buscar por referencia
            if ($response->status() === 404) {
                Log::info('Transacción no encontrada por ID, buscando por referencia: ' . $transactionId);

                return response()->json([
                    'success' => false,
                    'message' => 'Transacción no encontrada. Puede que aún esté procesándose.',
                    'transaction' => [
                        'status' => 'PENDING',
                        'reference' => $transactionId
                    ]
                ]);
            }

            throw new \Exception('Error consultando transacción: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Error consultando transacción Wompi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'No se pudo verificar el estado del pago. El pago puede estar procesándose.'
            ], 200); // 200 para no romper el flujo
        }
    }

    /**
     * Obtener métodos de pago aceptados
     */
    public function getAcceptedPaymentMethods()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->publicKey,
            ])->get($this->apiUrl . '/merchants/' . $this->publicKey);

            if ($response->successful()) {
                $data = $response->json();

                return response()->json([
                    'success' => true,
                    'payment_methods' => $data['data']['payment_methods'] ?? [],
                    'presential_payment_methods' => $data['data']['presential_payment_methods'] ?? [],
                ]);
            }

            throw new \Exception('Error obteniendo métodos de pago');

        } catch (\Exception $e) {
            Log::error('Error obteniendo métodos de pago: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar referencia única para pago
     */
    public static function generateReference($prefix = 'plan')
    {
        return $prefix . '_' . time() . '_' . Str::random(8);
    }
}
