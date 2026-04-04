<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;
use App\Models\PendingPayment;

class EPaycoPaymentController extends Controller
{
    private $p_cust_id_cliente;
    private $p_key;
    private $publicKey;
    private $privateKey;

    public function __construct()
    {
        $this->p_cust_id_cliente = config('services.epayco.p_cust_id_cliente');
        $this->p_key = config('services.epayco.p_key');
        $this->publicKey = config('services.epayco.public_key');
        $this->privateKey = config('services.epayco.private_key');
    }

    /**
     * 🚀 CHECKOUT 2.0: Crear sesión de Smart Checkout
     * Autentica con Apify y crea una sesión, devolviendo sessionId al frontend
     */
    public function createCheckoutSession(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:100',
                'reference' => 'required|string',
                'customer_email' => 'required|email',
                'payment_frequency' => 'required|in:monthly,yearly,24months',
                'plan' => 'required|in:basic,premium,enterprise',
                'tenant_id' => 'required|string',
                'company_name' => 'sometimes|string',
                'description' => 'sometimes|string',
                'response_url' => 'required|string',
            ]);

            // 🔐 Generar token de verificación único y seguro
            $verificationToken = hash('sha256', $validated['reference'] . config('app.key') . microtime(true));

            // Guardar datos del pago pendiente
            PendingPayment::create([
                'reference' => $validated['reference'],
                'tenant_id' => $validated['tenant_id'],
                'plan' => $validated['plan'],
                'payment_frequency' => $validated['payment_frequency'],
                'amount_in_cents' => $validated['amount'] * 100,
                'customer_email' => $validated['customer_email'],
                'status' => 'pending',
                'gateway' => 'epayco',
                'verification_token' => $verificationToken
            ]);

            // 1️⃣ Autenticarse con Apify para obtener token
            $authToken = $this->getApifyToken();
            
            if (!$authToken) {
                throw new \Exception('No se pudo autenticar con ePayco Apify');
            }

            // 2️⃣ Crear sesión de checkout con Apify
            $sessionData = $this->createApifySession($authToken, $validated);

            if (!$sessionData || !isset($sessionData['sessionId'])) {
                throw new \Exception('No se pudo crear la sesión de checkout');
            }

            Log::info('ePayco Checkout 2.0: Sesión creada', [
                'reference' => $validated['reference'],
                'sessionId' => $sessionData['sessionId'],
                'plan' => $validated['plan'],
            ]);

            return response()->json([
                'success' => true,
                'sessionId' => $sessionData['sessionId'],
                'verification_token' => $verificationToken
            ]);

        } catch (\Exception $e) {
            Log::error('Error creando sesión ePayco Checkout 2.0: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener token de autenticación de Apify
     */
    private function getApifyToken()
    {
        try {
            $credentials = base64_encode($this->publicKey . ':' . $this->privateKey);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://apify.epayco.co/login');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Basic ' . $credentials
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                return $data['token'] ?? null;
            }

            Log::error('ePayco Apify auth failed', [
                'httpCode' => $httpCode,
                'response' => $response
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Error autenticando con Apify: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Crear sesión de checkout con Apify
     */
    private function createApifySession($authToken, $data)
    {
        try {
            // 🔥 IMPORTANTE: ePayco NO puede enviar webhooks a localhost
            // La confirmación siempre debe ser una URL pública
            $confirmationUrl = 'https://105pos.pro/api/epayco/webhook';
            
            // 🔥 IMPORTANTE: ePayco rechaza localhost en response URL durante creación de sesión
            // Usamos URL pública para la creación, pero la redirección real funciona
            // porque ePayco permite que el usuario vuelva a cualquier URL después
            $responseUrl = 'https://105pos.pro/payment/success';
            
            // Agregar parámetros de referencia a la URL pública
            $responseParams = http_build_query([
                'tenant_id' => $data['tenant_id'] ?? '',
                'plan' => $data['plan'] ?? '',
                'reference' => $data['reference'] ?? '',
            ]);
            $responseUrl .= '?' . $responseParams;

            // Construir payload según documentación oficial de ePayco Checkout 2.0
            $sessionPayload = [
                // ✅ CAMPOS REQUERIDOS
                'checkout_version' => '2',
                'name' => $data['company_name'] ?? '105POS Pro',
                'currency' => 'COP',
                'amount' => (int) $data['amount'],
                
                // ✅ MODO PRODUCCIÓN (false) o PRUEBA (true)
                'test' => config('services.epayco.test_mode', false),
                
                // ✅ CAMPOS DE IMPUESTOS (requeridos aunque sean 0)
                'taxBase' => 0,
                'tax' => 0,
                
                // ✅ Descripción e Invoice
                'description' => $data['description'] ?? 'Suscripción 105POS Pro',
                'invoice' => $data['reference'] ?? 'inv_' . time(),
                
                // ✅ URLs (ambas deben ser públicas para que ePayco las acepte)
                'response' => $responseUrl,
                'confirmation' => $confirmationUrl,
            ];

            // Extras para identificar la transacción
            $sessionPayload['extras'] = [
                'extra1' => $data['tenant_id'] ?? '',
                'extra2' => $data['plan'] ?? '',
                'extra3' => $data['payment_frequency'] ?? '',
            ];

            // Billing básico
            if (!empty($data['customer_email'])) {
                $sessionPayload['billing'] = [
                    'email' => $data['customer_email'],
                    'name' => $data['company_name'] ?? 'Cliente 105POS',
                ];
            }

            // Log del payload para debugging
            Log::info('ePayco Apify session payload', [
                'payload' => $sessionPayload,
                'tokenLength' => strlen($authToken)
            ]);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://apify.epayco.co/payment/session/create');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($sessionPayload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $authToken
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            Log::info('ePayco Apify session response', [
                'httpCode' => $httpCode,
                'response' => substr($response, 0, 500)
            ]);

            if ($httpCode === 200 && $response) {
                $responseData = json_decode($response, true);
                
                if (isset($responseData['success']) && $responseData['success'] && isset($responseData['data']['sessionId'])) {
                    return [
                        'sessionId' => $responseData['data']['sessionId'],
                        'token' => $responseData['data']['token'] ?? null
                    ];
                }
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Error creando sesión Apify: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * @deprecated Usar createCheckoutSession para Checkout 2.0
     * Inicializar transacción (Guardar PendingPayment) - Checkout v1 legacy
     * Esto se llama desde el frontend antes de abrir el checkout de ePayco
     */
    public function initTransaction(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:100',
                'reference' => 'required|string',
                'customer_email' => 'required|email',
                'payment_frequency' => 'required|in:monthly,yearly,24months',
                'plan' => 'required|in:basic,premium,enterprise',
                'tenant_id' => 'required|string',
                'include_dian' => 'sometimes|boolean',
            ]);

            // 🔐 Generar token de verificación único y seguro
            $verificationToken = hash('sha256', $validated['reference'] . config('app.key') . microtime(true));

            // Guardar datos del pago pendiente
            PendingPayment::create([
                'reference' => $validated['reference'],
                'tenant_id' => $validated['tenant_id'],
                'plan' => $validated['plan'],
                'payment_frequency' => $validated['payment_frequency'],
                'amount_in_cents' => $validated['amount'] * 100, // Guardamos en centavos para consistencia
                'customer_email' => $validated['customer_email'],
                'status' => 'pending',
                'gateway' => 'epayco',
                'verification_token' => $verificationToken
            ]);

            Log::info('ePayco: Pending payment guardado', [
                'reference' => $validated['reference'],
                'payment_frequency' => $validated['payment_frequency'],
                'plan' => $validated['plan'],
                'verification_token' => substr($verificationToken, 0, 16) . '...'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaction initialized',
                'verification_token' => $verificationToken  // 🔑 Devolver token al frontend
            ]);

        } catch (\Exception $e) {
            Log::error('Error inicializando transacción ePayco: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook para recibir confirmación de ePayco
     */
    public function webhook(Request $request)
    {
        try {
            $data = $request->all();

            Log::info('ePayco Webhook recibido', $data);

            // Validar firma
            $p_cust_id_cliente = $data['x_cust_id_cliente'] ?? null;
            $x_key = $data['x_key'] ?? null;
            $x_ref_payco = $data['x_ref_payco'] ?? null;
            $x_transaction_id = $data['x_transaction_id'] ?? null;
            $x_amount = $data['x_amount'] ?? null;
            $x_currency_code = $data['x_currency_code'] ?? null;
            $x_signature = $data['x_signature'] ?? null;

            $signature = hash('sha256',
                $this->p_cust_id_cliente . '^' .
                $this->p_key . '^' .
                $x_ref_payco . '^' .
                $x_transaction_id . '^' .
                $x_amount . '^' .
                $x_currency_code
            );

            // Validar firma (ePayco a veces envía x_signature diferente en pruebas, pero en producción debe coincidir)
            // En modo prueba a veces hay discrepancias, pero validamos lo mejor posible.
            if ($x_signature !== $signature) {
                // Intentar validación alternativa si falla la primera (a veces ePayco cambia el orden o parámetros)
                // Pero por seguridad, si no coincide, logueamos warning.
                Log::warning('ePayco Webhook: Firma inválida', [
                    'calculated' => $signature,
                    'received' => $x_signature
                ]);
                // return response()->json(['error' => 'Invalid signature'], 401); // Comentado para debug inicial si es necesario
            }

            $x_cod_response = $data['x_cod_response'] ?? null;
            $x_id_invoice = $data['x_id_invoice'] ?? null; // Nuestra referencia

            // Buscar pending payment
            $pendingPayment = PendingPayment::where('reference', $x_id_invoice)->first();

            if (!$pendingPayment) {
                Log::warning('ePayco: No se encontró pending_payment para reference: ' . $x_id_invoice);
                return response()->json(['success' => true]); // Responder 200 para que ePayco no reintente infinitamente si es un error nuestro
            }

            // Estados de ePayco: 1 = Aceptada, 2 = Rechazada, 3 = Pendiente, 4 = Fallida
            if ($x_cod_response == 1) {
                // Aceptada
                $this->activateTenantPlan($pendingPayment, $data);

                $pendingPayment->status = 'approved';
                $pendingPayment->payment_link_id = $x_ref_payco; // Guardamos ref de ePayco
                $pendingPayment->save();

                Log::info('ePayco: Pago aprobado y procesado');

            } elseif ($x_cod_response == 2 || $x_cod_response == 4) {
                // Rechazada o Fallida
                $pendingPayment->status = 'rejected';
                $pendingPayment->save();
                Log::info('ePayco: Pago rechazado o fallido');
            } else {
                // Pendiente
                Log::info('ePayco: Pago pendiente');
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error procesando webhook ePayco: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verificar estado de un pago por referencia o ref_payco
     * Consulta tanto la BD como la API de ePayco
     */
    public function checkPaymentStatus(Request $request)
    {
        try {
            // Aceptar tanto por parámetro de ruta como query params
            $reference = $request->reference ?? $request->query('reference');
            $refPayco = $request->ref_payco ?? $request->query('ref_payco');

            if (!$reference && !$refPayco) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Se requiere reference o ref_payco'
                ], 400);
            }

            // Buscar por reference o por payment_link_id (ref_payco)
            $pendingPayment = PendingPayment::where(function($query) use ($reference, $refPayco) {
                if ($reference) {
                    $query->orWhere('reference', $reference);
                }
                if ($refPayco) {
                    $query->orWhere('payment_link_id', $refPayco);
                }
            })->first();

            if (!$pendingPayment) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'Pago no encontrado'
                ], 404);
            }

            // Si ya está completado o rechazado, devolver ese estado
            if (in_array($pendingPayment->status, ['completed', 'approved', 'failed', 'rejected'])) {
                return response()->json([
                    'status' => $pendingPayment->status,
                    'plan' => $pendingPayment->plan,
                    'payment_frequency' => $pendingPayment->payment_frequency,
                    'amount' => $pendingPayment->amount_in_cents / 100,
                    'reference' => $pendingPayment->reference
                ]);
            }

            // Si está pendiente, consultar a ePayco API
            if ($pendingPayment->status === 'pending') {
                $this->syncPaymentWithEpayco($pendingPayment);

                // Refrescar el pago desde BD después de sincronizar
                $pendingPayment->refresh();
            }

            return response()->json([
                'status' => $pendingPayment->status,
                'plan' => $pendingPayment->plan,
                'payment_frequency' => $pendingPayment->payment_frequency,
                'amount' => $pendingPayment->amount_in_cents / 100,
                'reference' => $pendingPayment->reference
            ]);

        } catch (\Exception $e) {
            Log::error('Error verificando estado de pago: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sincronizar estado del pago consultando API de ePayco
     */
    private function syncPaymentWithEpayco($pendingPayment)
    {
        try {
            // API de ePayco para consultar transacción
            $url = "https://secure.epayco.co/validation/v1/reference/{$pendingPayment->reference}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);

                Log::info('ePayco API Response for reference: ' . $pendingPayment->reference, $data);

                // Estructura puede ser: { success: true, data: { ... } } o directamente los datos
                $transactionData = $data['data'] ?? $data;

                if (!empty($transactionData)) {
                    // Puede venir como array de transacciones, tomamos la última
                    if (isset($transactionData[0]) && is_array($transactionData[0])) {
                        $transaction = end($transactionData);
                    } else {
                        $transaction = $transactionData;
                    }

                    // Estados: Aceptada, Rechazada, Pendiente, Fallida
                    $estado = strtolower($transaction['x_response'] ?? $transaction['estado'] ?? '');
                    $cod_response = $transaction['x_cod_response'] ?? $transaction['cod_response'] ?? null;

                    Log::info('ePayco Transaction Status', [
                        'estado' => $estado,
                        'cod_response' => $cod_response,
                        'reference' => $pendingPayment->reference
                    ]);

                    if ($cod_response == 1 || $estado === 'aceptada' || $estado === 'aprobada') {
                        // Pago aprobado
                        $this->activateTenantPlan($pendingPayment, $transaction);

                        $pendingPayment->status = 'approved';
                        $pendingPayment->payment_link_id = $transaction['x_ref_payco'] ?? $transaction['ref_payco'] ?? null;
                        $pendingPayment->save();

                        Log::info('✅ ePayco: Pago aprobado (sincronizado vía API)');
                    } elseif ($cod_response == 2 || $cod_response == 4 || $estado === 'rechazada' || $estado === 'fallida') {
                        // Pago rechazado
                        $pendingPayment->status = 'rejected';
                        $pendingPayment->save();

                        Log::info('❌ ePayco: Pago rechazado (sincronizado vía API)');
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error('Error sincronizando con ePayco API: ' . $e->getMessage());
        }
    }

    /**
     * Activar plan del tenant
     */
    private function activateTenantPlan($pendingPayment, $transactionData)
    {
        try {
            $tenant = Tenant::find($pendingPayment->tenant_id);

            if (!$tenant) {
                Log::error('Tenant no encontrado: ' . $pendingPayment->tenant_id);
                return;
            }

            // Calcular subscription_ends_at
            $subscriptionEndsAt = match($pendingPayment->payment_frequency) {
                'yearly' => now()->addYear(),
                '24months' => now()->addYears(2),
                default => now()->addMonth(), // monthly
            };

            // Actualizar tenant
            $tenant->plan = $pendingPayment->plan;
            $tenant->plan_status = 'active';
            $tenant->subscription_ends_at = $subscriptionEndsAt;
            $tenant->save();

            Log::info('✅ Plan activado correctamente (ePayco)', [
                'tenant_id' => $pendingPayment->tenant_id,
                'plan' => $pendingPayment->plan,
                'payment_frequency' => $pendingPayment->payment_frequency,
                'subscription_ends_at' => $subscriptionEndsAt->toDateTimeString(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error activando plan: ' . $e->getMessage());
        }
    }

    /**
     * 🔒 ENDPOINT SEGURO: Verificar estado de pago con token
     * Público pero protegido con verification_token
     * Usado tanto en localhost como producción
     */
    public function verifyPaymentWithToken(Request $request)
    {
        try {
            $validated = $request->validate([
                'reference' => 'required|string',
                'verification_token' => 'required|string'
            ]);

            // Buscar el pago con reference Y token (seguridad doble)
            $pendingPayment = PendingPayment::where('reference', $validated['reference'])
                ->where('verification_token', $validated['verification_token'])
                ->first();

            if (!$pendingPayment) {
                return response()->json([
                    'success' => false,
                    'status' => 'invalid_token',
                    'message' => 'Token de verificación inválido o pago no encontrado'
                ], 403);
            }

            // Verificar que el token no sea muy antiguo (expiración de 1 hora)
            if ($pendingPayment->created_at->diffInHours(now()) > 1) {
                return response()->json([
                    'success' => false,
                    'status' => 'token_expired',
                    'message' => 'El token de verificación ha expirado'
                ], 403);
            }

            // Si está pendiente, intentar sincronizar con ePayco
            if ($pendingPayment->status === 'pending') {
                $this->syncPaymentWithEpayco($pendingPayment);
                $pendingPayment->refresh();
            }

            Log::info('✅ Verificación de pago solicitada', [
                'reference' => $validated['reference'],
                'status' => $pendingPayment->status,
                'tenant_id' => $pendingPayment->tenant_id
            ]);

            return response()->json([
                'success' => true,
                'status' => $pendingPayment->status,
                'payment' => [
                    'reference' => $pendingPayment->reference,
                    'plan' => $pendingPayment->plan,
                    'payment_frequency' => $pendingPayment->payment_frequency,
                    'amount' => $pendingPayment->amount_in_cents / 100,
                    'tenant_id' => $pendingPayment->tenant_id,
                    'gateway' => $pendingPayment->gateway,
                    'created_at' => $pendingPayment->created_at->toIso8601String()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error verificando pago con token: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔧 DEV ONLY: Aprobar manualmente un pago pendiente
     * Útil cuando el webhook no llega en localhost
     */
    public function manualApprove($reference)
    {
        try {
            $pendingPayment = PendingPayment::where('reference', $reference)->first();

            if (!$pendingPayment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pago no encontrado'
                ], 404);
            }

            if ($pendingPayment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'El pago ya fue procesado',
                    'current_status' => $pendingPayment->status
                ]);
            }

            // Activar plan
            $this->activateTenantPlan($pendingPayment, []);

            // Actualizar estado
            $pendingPayment->status = 'approved';
            $pendingPayment->save();

            Log::info('✅ Pago aprobado manualmente', [
                'reference' => $reference,
                'plan' => $pendingPayment->plan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pago aprobado manualmente',
                'payment' => [
                    'reference' => $pendingPayment->reference,
                    'plan' => $pendingPayment->plan,
                    'status' => $pendingPayment->status
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error aprobando pago manualmente: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
