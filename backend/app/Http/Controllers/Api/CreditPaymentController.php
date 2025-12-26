<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CreditPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CreditPaymentController extends Controller
{
    /**
     * Get all credit payments for a customer
     */
    public function index(Request $request)
    {
        try {
            $query = CreditPayment::with('customer')
                ->orderBy('created_at', 'desc');

            // Filter by customer if provided
            if ($request->has('customer_id')) {
                $query->where('customer_id', $request->customer_id);
            }

            // Filter by date range
            if ($request->has('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->has('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            $payments = $query->paginate($request->get('per_page', 50));

            return response()->json([
                'success' => true,
                'data' => $payments->items(),
                'meta' => [
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'per_page' => $payments->perPage(),
                    'total' => $payments->total(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching credit payments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los pagos de crédito'
            ], 500);
        }
    }

    /**
     * Register a credit payment (abono)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|string|in:cash,card,transfer',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $customer = Customer::findOrFail($request->customer_id);

            // Validate payment amount doesn't exceed debt
            if ($request->amount > $customer->current_debt) {
                return response()->json([
                    'success' => false,
                    'message' => 'El monto del abono no puede ser mayor a la deuda actual'
                ], 422);
            }

            // Create payment record
            $payment = CreditPayment::create([
                'customer_id' => $request->customer_id,
                'amount' => $request->amount,
                'method' => $request->method,
                'notes' => $request->notes,
                'user_id' => auth()->id() ?? null
            ]);

            // Update customer debt
            $customer->current_debt -= $request->amount;

            // Si la deuda llega a $0, limpiar fecha de inicio de deuda
            if ($customer->current_debt <= 0) {
                $customer->current_debt = 0;
                $customer->debt_since = null;
                Log::info('✅ Cliente liquidó deuda completa, limpiando debt_since', [
                    'customer_id' => $customer->id
                ]);
            }

            $customer->save();

            DB::commit();

            Log::info('Credit payment registered', [
                'payment_id' => $payment->id,
                'customer_id' => $customer->id,
                'amount' => $request->amount,
                'new_debt' => $customer->current_debt
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Abono registrado exitosamente',
                'data' => $payment,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'current_debt' => $customer->current_debt,
                    'credit_limit' => $customer->credit_limit
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error registering credit payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el abono: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send payment reminder to customer via WhatsApp and/or Email
     */
    public function sendReminder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente inválido',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $customer = Customer::findOrFail($request->customer_id);

            // Validate customer has debt
            if (!$customer->current_debt || $customer->current_debt <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'El cliente no tiene deuda pendiente'
                ], 422);
            }

            // Check if customer has phone or email
            if (!$customer->phone && !$customer->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'El cliente no tiene teléfono ni email registrado'
                ], 422);
            }

            $sentChannels = [];
            $errors = [];

            // Preparar mensaje de recordatorio
            $debtAmount = number_format($customer->current_debt, 0, ',', '.');
            $creditLimit = number_format($customer->credit_limit, 0, ',', '.');
            $availableCredit = number_format(max(0, $customer->credit_limit - $customer->current_debt), 0, ',', '.');

            // Calcular días de mora
            $daysPastDue = 0;
            if ($customer->debt_since) {
                $debtDate = new \DateTime($customer->debt_since);
                $today = new \DateTime();
                $daysPastDue = $today->diff($debtDate)->days;
            }

            // 📱 ENVIAR POR WHATSAPP
            if ($customer->phone) {
                try {
                    $whatsappMessage = "🔔 *RECORDATORIO DE PAGO - CreditiTenda*\n\n";
                    $whatsappMessage .= "Hola *{$customer->name}*,\n\n";
                    $whatsappMessage .= "Le recordamos que tiene una deuda pendiente:\n\n";
                    $whatsappMessage .= "💰 *Deuda Actual:* \${$debtAmount}\n";
                    $whatsappMessage .= "📊 *Cupo de Crédito:* \${$creditLimit}\n";
                    $whatsappMessage .= "✅ *Crédito Disponible:* \${$availableCredit}\n";

                    if ($daysPastDue > 0) {
                        $whatsappMessage .= "📅 *Días de Mora:* {$daysPastDue} días\n";
                    }

                    $whatsappMessage .= "\n_Por favor, realice su pago lo antes posible para mantener su cupo disponible._\n\n";
                    $whatsappMessage .= "¡Gracias por su preferencia! 😊";

                    // Formatear número al formato colombiano
                    $phone = $customer->phone;
                    if (!str_starts_with($phone, '+57')) {
                        // Remover caracteres no numéricos
                        $phone = preg_replace('/[^0-9]/', '', $phone);
                        // Agregar prefijo +57
                        if (strlen($phone) === 10) {
                            $phone = '+57' . $phone;
                        }
                    }

                    // Enviar vía WhatsApp usando el servicio del sistema
                    $whatsappUrl = 'http://localhost:3001/send-message';
                    $response = \Illuminate\Support\Facades\Http::timeout(10)->post($whatsappUrl, [
                        'phone' => $phone,
                        'message' => $whatsappMessage
                    ]);

                    if ($response->successful()) {
                        $sentChannels[] = 'WhatsApp';
                        Log::info('✅ Recordatorio WhatsApp enviado', [
                            'customer_id' => $customer->id,
                            'phone' => $phone
                        ]);
                    } else {
                        $errors[] = 'WhatsApp: ' . ($response->json()['error'] ?? 'Error desconocido');
                        Log::warning('⚠️ Error enviando WhatsApp', [
                            'customer_id' => $customer->id,
                            'phone' => $phone,
                            'error' => $response->body()
                        ]);
                    }
                } catch (\Exception $e) {
                    $errors[] = 'WhatsApp: ' . $e->getMessage();
                    Log::error('❌ Excepción enviando WhatsApp', [
                        'customer_id' => $customer->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // 📧 ENVIAR POR EMAIL
            if ($customer->email) {
                try {
                    $emailSubject = "Recordatorio de Pago - CreditiTenda";
                    $emailBody = "
                        <h2>Recordatorio de Pago</h2>
                        <p>Estimado/a <strong>{$customer->name}</strong>,</p>
                        <p>Le recordamos que tiene una deuda pendiente en su cuenta de CreditiTenda:</p>
                        <ul>
                            <li><strong>Deuda Actual:</strong> \${$debtAmount}</li>
                            <li><strong>Cupo de Crédito:</strong> \${$creditLimit}</li>
                            <li><strong>Crédito Disponible:</strong> \${$availableCredit}</li>
                    ";

                    if ($daysPastDue > 0) {
                        $emailBody .= "<li><strong>Días de Mora:</strong> {$daysPastDue} días</li>";
                    }

                    $emailBody .= "
                        </ul>
                        <p>Por favor, realice su pago lo antes posible para mantener su cupo de crédito disponible.</p>
                        <p>¡Gracias por su preferencia!</p>
                    ";

                    \Illuminate\Support\Facades\Mail::html($emailBody, function($message) use ($customer, $emailSubject) {
                        $message->to($customer->email)
                                ->subject($emailSubject);
                    });

                    $sentChannels[] = 'Email';
                    Log::info('✅ Recordatorio Email enviado', [
                        'customer_id' => $customer->id,
                        'email' => $customer->email
                    ]);
                } catch (\Exception $e) {
                    $errors[] = 'Email: ' . $e->getMessage();
                    Log::error('❌ Excepción enviando Email', [
                        'customer_id' => $customer->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Preparar respuesta
            if (count($sentChannels) > 0) {
                $message = 'Recordatorio enviado por: ' . implode(' y ', $sentChannels);
                if (count($errors) > 0) {
                    $message .= '. Errores: ' . implode(', ', $errors);
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'customer_name' => $customer->name,
                        'debt_amount' => $customer->current_debt,
                        'sent_channels' => $sentChannels,
                        'errors' => $errors,
                        'sent_at' => now()
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo enviar el recordatorio. Errores: ' . implode(', ', $errors)
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error sending payment reminder: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el recordatorio: ' . $e->getMessage()
            ], 500);
        }
    }
}
