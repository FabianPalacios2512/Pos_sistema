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

            // 🎯 Calcular reducción proporcional de subtotal_debt
            // Si current_debt = subtotal_debt * factor, entonces:
            // subtotal_debt a reducir = amount / factor
            $systemSettings = \App\Models\SystemSetting::first();
            $surchargePercent = floatval($systemSettings->credit_surcharge_percentage ?? 10);
            $factor = 1 + ($surchargePercent / 100);
            $subtotalReduction = $request->amount / $factor;

            // Update customer debt
            $customer->current_debt -= $request->amount;
            $customer->subtotal_debt -= $subtotalReduction;

            // Si la deuda llega a $0, limpiar fecha de inicio de deuda
            if ($customer->current_debt <= 0) {
                $customer->current_debt = 0;
                $customer->subtotal_debt = 0;
                $customer->debt_since = null;
                Log::info('✅ Cliente liquidó deuda completa, limpiando debt_since', [
                    'customer_id' => $customer->id
                ]);
            }

            // Asegurar que subtotal_debt no sea negativo
            if ($customer->subtotal_debt < 0) $customer->subtotal_debt = 0;

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

            // Calcular disponible basado en subtotal_debt del cliente (sin recargo)
            // El recargo no cuenta contra el cupo del cliente
            // 🎯 Usar directamente el campo del cliente en lugar de calcular de facturas
            $subtotalPendiente = floatval($customer->subtotal_debt ?? 0);
            $availableCreditValue = max(0, $customer->credit_limit - $subtotalPendiente);
            $availableCredit = number_format($availableCreditValue, 0, ',', '.');

            // Calcular días de mora
            $daysPastDue = 0;
            if ($customer->debt_since) {
                $debtDate = new \DateTime($customer->debt_since);
                $today = new \DateTime();
                $daysPastDue = $today->diff($debtDate)->days;
            }

            // Generar link al portal de crédito
            $tenantId = tenant('id') ?? request()->header('X-Tenant-Id');
            $portalUrl = '';
            if ($customer->credit_access_token) {
                // Usar el dominio del request actual
                $host = request()->getHost();
                $port = request()->getPort();
                $scheme = request()->getScheme();

                // Si es localhost con puerto, incluir el puerto
                if (str_contains($host, 'localhost') && $port && $port != 80 && $port != 443) {
                    $domain = "{$host}:{$port}";
                } else {
                    $domain = $host;
                }

                $portalUrl = "{$scheme}://{$domain}/mi-credito?token={$customer->credit_access_token}";
            }

            // 📱 ENVIAR POR WHATSAPP
            if ($customer->phone) {
                try {
                    $whatsappMessage = "🔔 *RECORDATORIO DE PAGO - CreditiTenda*\n\n";
                    $whatsappMessage .= "Hola *{$customer->name}*,\n\n";
                    $whatsappMessage .= "Le recordamos que tiene una deuda pendiente:\n\n";
                    $whatsappMessage .= "💰 *Deuda Actual:* \${$debtAmount}\n";
                    $whatsappMessage .= "📊 *Cupo de Crédito:* \${$creditLimit}\n";
                    $whatsappMessage .= "✅ *Disponible:* \${$availableCredit}\n";

                    if ($daysPastDue > 0) {
                        $whatsappMessage .= "📅 *Días de Mora:* {$daysPastDue} días\n";
                    }

                    $whatsappMessage .= "\n_Por favor, realice su pago lo antes posible para mantener su cupo disponible._\n\n";

                    // Agregar link al portal si existe
                    if ($portalUrl) {
                        $whatsappMessage .= "📱 *Consulta tu estado de cuenta aquí:*\n{$portalUrl}\n\n";
                    }

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

                    // Obtener tenant_id del contexto actual
                    // IMPORTANTE: Convertir guion bajo a guion para coincidir con sesiones WhatsApp
                    $tenantId = tenant('id') ?? request()->header('X-Tenant-Id');
                    $tenantId = str_replace('_', '-', $tenantId);

                    // Enviar vía WhatsApp usando el servidor multi-tenant (puerto 3002)
                    $whatsappUrl = 'http://localhost:3002/send';
                    $response = \Illuminate\Support\Facades\Http::timeout(10)
                        ->withHeaders([
                            'X-Tenant-Id' => $tenantId
                        ])
                        ->post($whatsappUrl, [
                            'phone' => $phone,
                            'message' => $whatsappMessage
                        ]);

                    if ($response->successful()) {
                        $responseData = $response->json();
                        if (isset($responseData['success']) && $responseData['success']) {
                            $sentChannels[] = 'WhatsApp';
                            Log::info('✅ Recordatorio WhatsApp enviado', [
                                'customer_id' => $customer->id,
                                'phone' => $phone,
                                'tenant_id' => $tenantId
                            ]);
                        } else {
                            $errors[] = 'WhatsApp: ' . ($responseData['error'] ?? 'Error desconocido');
                            Log::warning('⚠️ Error enviando WhatsApp', [
                                'customer_id' => $customer->id,
                                'phone' => $phone,
                                'error' => $responseData
                            ]);
                        }
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

            // Preparar respuesta - SIEMPRE devolver éxito parcial si se intentó enviar
            $totalAttempts = ($customer->phone ? 1 : 0) + ($customer->email ? 1 : 0);

            if (count($sentChannels) > 0) {
                $message = 'Recordatorio enviado por: ' . implode(' y ', $sentChannels);
                if (count($errors) > 0) {
                    $message .= '. (Algunos canales fallaron: ' . count($errors) . ')';
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
                // Ningún canal funcionó, pero damos un mensaje más amigable
                $friendlyErrors = [];
                foreach ($errors as $error) {
                    if (str_contains($error, 'no está conectado')) {
                        $friendlyErrors[] = 'WhatsApp no está conectado. Conéctalo desde el menú de configuración.';
                    } elseif (str_contains($error, 'smtp') || str_contains($error, 'mail') || str_contains($error, 'getaddrinfo')) {
                        $friendlyErrors[] = 'Email no disponible temporalmente.';
                    } else {
                        $friendlyErrors[] = $error;
                    }
                }

                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo enviar el recordatorio. ' . implode(' ', array_unique($friendlyErrors)),
                    'errors' => $friendlyErrors,
                    'requires_whatsapp' => $customer->phone && !in_array('WhatsApp', $sentChannels)
                ], 200); // Devolver 200 para que no sea un error fatal
            }
        } catch (\Exception $e) {
            Log::error('Error sending payment reminder: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el recordatorio. Verifica que WhatsApp esté conectado.',
                'error_detail' => $e->getMessage()
            ], 200); // Devolver 200 para manejo graceful en frontend
        }
    }

    /**
     * Get reminder settings for CreditiTenda
     */
    public function getReminderSettings()
    {
        try {
            $settings = \App\Models\SystemSetting::first();

            return response()->json([
                'success' => true,
                'data' => [
                    'frequency' => $settings->reminder_frequency ?? 'manual',
                    'send_hour' => $settings->reminder_send_hour ?? '9',
                    'min_days_overdue' => $settings->reminder_min_days_overdue ?? 1
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting reminder settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la configuración de recordatorios'
            ], 500);
        }
    }

    /**
     * Save reminder settings for CreditiTenda
     */
    public function saveReminderSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'frequency' => 'required|in:manual,smart,daily,weekly,biweekly',
            'send_hour' => 'sometimes|integer|min:6|max:20',
            'min_days_overdue' => 'sometimes|integer|min:0|max:90'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $settings = \App\Models\SystemSetting::first();

            if (!$settings) {
                $settings = new \App\Models\SystemSetting();
            }

            $settings->reminder_frequency = $request->frequency;

            // Para modo smart, usar valores inteligentes por defecto
            if ($request->frequency === 'smart') {
                $settings->reminder_send_hour = 9; // 9 AM
                $settings->reminder_min_days_overdue = 20; // 20 días de mora mínimo
            } else {
                $settings->reminder_send_hour = $request->send_hour ?? 9;
                $settings->reminder_min_days_overdue = $request->min_days_overdue ?? 1;
            }

            $settings->save();

            Log::info('✅ Reminder settings saved', [
                'frequency' => $request->frequency,
                'send_hour' => $settings->reminder_send_hour,
                'min_days_overdue' => $settings->reminder_min_days_overdue
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Configuración de recordatorios guardada exitosamente',
                'data' => [
                    'frequency' => $settings->reminder_frequency,
                    'send_hour' => $settings->reminder_send_hour,
                    'min_days_overdue' => $settings->reminder_min_days_overdue
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving reminder settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la configuración de recordatorios: ' . $e->getMessage()
            ], 500);
        }
    }
}
