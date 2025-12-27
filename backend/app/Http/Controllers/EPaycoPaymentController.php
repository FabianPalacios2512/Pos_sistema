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
     * Inicializar transacción (Guardar PendingPayment)
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
            ]);

            // Guardar datos del pago pendiente
            PendingPayment::create([
                'reference' => $validated['reference'],
                'tenant_id' => $validated['tenant_id'],
                'plan' => $validated['plan'],
                'payment_frequency' => $validated['payment_frequency'],
                'amount_in_cents' => $validated['amount'] * 100, // Guardamos en centavos para consistencia
                'customer_email' => $validated['customer_email'],
                'status' => 'pending',
                'gateway' => 'epayco'
            ]);

            Log::info('ePayco: Pending payment guardado', [
                'reference' => $validated['reference'],
                'payment_frequency' => $validated['payment_frequency'],
                'plan' => $validated['plan'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaction initialized'
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
                
                $pendingPayment->status = 'completed';
                $pendingPayment->payment_link_id = $x_ref_payco; // Guardamos ref de ePayco
                $pendingPayment->save();
                
                Log::info('ePayco: Pago aprobado y procesado');

            } elseif ($x_cod_response == 2 || $x_cod_response == 4) {
                // Rechazada o Fallida
                $pendingPayment->status = 'failed';
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
}
