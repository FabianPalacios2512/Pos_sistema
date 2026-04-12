<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use App\Models\Tenant;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        // ✅ SDK v3.x API correcta
        MercadoPagoConfig::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));
    }

    /**
     * Crear preferencia de pago en Mercado Pago (SDK v3.x)
     */
    public function createPreference(Request $request)
    {
        try {
            $validated = $request->validate([
                'tenant_id' => 'required',
                'plan' => 'required|string',
                'payment_frequency' => 'required|in:monthly,yearly,24months',
                'amount' => 'required|numeric|min:1000',
                'include_dian' => 'boolean',
                'company_name' => 'nullable|string'
            ]);

            // Mapear nombres de planes a títulos legibles
            $planTitles = [
                'emprendedor' => 'Plan Emprendedor',
                'negocio_pro' => 'Plan Negocio Pro',
                'enterprise' => 'Plan Enterprise'
            ];

            $frequencyLabels = [
                'monthly' => 'Mensual',
                'yearly' => 'Anual',
                '24months' => '24 Meses'
            ];

            $title = ($planTitles[$validated['plan']] ?? 'Plan') . ' - ' . ($frequencyLabels[$validated['payment_frequency']] ?? '');
            $description = 'Suscripción ' . $title . ' para ' . ($validated['company_name'] ?? 'tu negocio');


            // ✅ Usar PreferenceClient (SDK v3.x)
            $client = new PreferenceClient();

            // Construir URLs de redirección
            $successUrl = $this->getRedirectUrl($request, '/payment/success');
            $failureUrl = $this->getRedirectUrl($request, '/payment/failure');
            $pendingUrl = $this->getRedirectUrl($request, '/payment/pending');
            $webhookUrl = $this->getRedirectUrl($request, '/api/mercadopago/webhook');


            // Preparar datos de preferencia - MODO TEST OPTIMIZADO
            $preferenceData = [
                'items' => [
                    [
                        'title' => $title,
                        'description' => $description,
                        'quantity' => 1,
                        'unit_price' => (float) $validated['amount'],
                        'currency_id' => 'COP'
                    ]
                ],

                // URLs de retorno - SIMPLIFICADAS para TEST
                'back_urls' => [
                    'success' => $successUrl,
                    'failure' => $failureUrl,
                    'pending' => $pendingUrl
                ],

                // ⚠️ CRÍTICO: auto_return debe estar desactivado en TEST con localhost
                // 'auto_return' => 'approved',

                // Metadata para identificar el pago
                'external_reference' => $validated['tenant_id'],
                'metadata' => [
                    'tenant_id' => $validated['tenant_id'],
                    'plan' => $validated['plan'],
                    'payment_frequency' => $validated['payment_frequency'],
                    'include_dian' => $validated['include_dian'] ?? false
                ],

                // ⚠️ WEBHOOK desactivado temporalmente para evitar conflictos en TEST
                // 'notification_url' => $webhookUrl,

                // Configuración adicional para TEST
                'purpose' => 'wallet_purchase',
                'marketplace_fee' => 0
            ];

            // ✅ Crear preferencia con SDK v3.x
            $preference = $client->create($preferenceData);


            // Registrar transacción pendiente en BD
            PaymentTransaction::create([
                'tenant_id' => $validated['tenant_id'],
                'preference_id' => $preference->id,
                'plan' => $validated['plan'],
                'frequency' => $validated['payment_frequency'],
                'amount' => $validated['amount'],
                'include_dian' => $validated['include_dian'] ?? false,
                'status' => 'pending',
                'metadata' => $validated
            ]);

            return response()->json([
                'success' => true,
                'preference_id' => $preference->id,
                'init_point' => $preference->init_point
            ]);

        } catch (MPApiException $e) {
            $apiResponse = $e->getApiResponse();
            $responseContent = $apiResponse ? $apiResponse->getContent() : null;

            Log::error('❌ Error API Mercado Pago', [
                'error' => $e->getMessage(),
                'status_code' => $apiResponse ? $apiResponse->getStatusCode() : null,
                'response_content' => $responseContent
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error al procesar el pago: ' . $e->getMessage(),
                'details' => $responseContent
            ], 500);

        } catch (\Exception $e) {
            Log::error('❌ Error creando preferencia MP', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error al procesar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper para construir URLs de redirección
     */
    private function getRedirectUrl(Request $request, string $path): string
    {
        $protocol = $request->secure() ? 'https://' : 'http://';
        $host = $request->getHost();
        $port = $request->getPort();

        $baseUrl = $protocol . $host;
        if ($port && $port != 80 && $port != 443) {
            $baseUrl .= ':3000'; // Frontend en puerto 3000
        }

        return $baseUrl . $path;
    }

    /**
     * Webhook de Mercado Pago (notificaciones IPN) - SDK v3.x
     */
    public function webhook(Request $request)
    {
        try {

            // Verificar que sea notificación de pago
            if ($request->type !== 'payment') {
                return response()->json(['status' => 'ignored']);
            }

            $paymentId = $request->input('data.id');

            // ✅ Obtener información del pago con SDK v3.x
            $paymentClient = new \MercadoPago\Client\Payment\PaymentClient();
            $payment = $paymentClient->get($paymentId);

            if (!$payment) {
                Log::error('❌ Pago no encontrado', ['payment_id' => $paymentId]);
                return response()->json(['status' => 'error'], 404);
            }

            // Obtener tenant_id desde external_reference
            $tenantId = $payment->external_reference;
            $metadata = $payment->metadata;


            // Actualizar transacción en BD
            $transaction = PaymentTransaction::where('preference_id', $payment->preference_id ?? null)
                ->orWhere('payment_id', $paymentId)
                ->first();

            if ($transaction) {
                $transaction->update([
                    'payment_id' => $paymentId,
                    'status' => $payment->status,
                    'payment_type' => $payment->payment_type_id ?? null,
                    'payment_method' => $payment->payment_method_id ?? null,
                    'payer_email' => $payment->payer->email ?? null,
                    'collection_id' => $payment->id
                ]);
            }

            // Si el pago fue aprobado, activar plan
            if ($payment->status === 'approved') {
                $tenant = Tenant::where('tenant_id', $tenantId)->first();

                if ($tenant) {
                    $plan = $metadata['plan'] ?? 'emprendedor';
                    $frequency = $metadata['payment_frequency'] ?? 'monthly';

                    // Calcular fecha de expiración
                    $expiresAt = match($frequency) {
                        'yearly' => now()->addYear(),
                        '24months' => now()->addYears(2),
                        default => now()->addMonth()
                    };

                    $tenant->update([
                        'plan' => $plan,
                        'subscription_ends_at' => $expiresAt,
                        'status' => 'active'
                    ]);

                }
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('❌ Error procesando webhook MP', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Verificar estado de pago - SDK v3.x
     */
    public function checkPaymentStatus(Request $request)
    {
        try {
            $paymentId = $request->input('payment_id');

            // ✅ SDK v3.x
            $paymentClient = new \MercadoPago\Client\Payment\PaymentClient();
            $payment = $paymentClient->get($paymentId);

            return response()->json([
                'success' => true,
                'status' => $payment->status,
                'status_detail' => $payment->status_detail,
                'amount' => $payment->transaction_amount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}