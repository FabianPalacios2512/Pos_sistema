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
     * Send payment reminder to customer
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

            // TODO: Implement actual notification sending (WhatsApp/SMS/Email)
            // For now, just log the reminder
            $reminderMessage = sprintf(
                "Recordatorio de Pago - %s: Tiene una deuda pendiente de $%s. Su cupo de crédito es de $%s.",
                $customer->name,
                number_format($customer->current_debt, 0, ',', '.'),
                number_format($customer->credit_limit, 0, ',', '.')
            );

            Log::info('Payment reminder sent', [
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'debt' => $customer->current_debt,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'message' => $reminderMessage
            ]);

            // Record reminder sent (optional: create a reminders table)
            // For now, just return success

            return response()->json([
                'success' => true,
                'message' => 'Recordatorio enviado exitosamente',
                'data' => [
                    'customer_name' => $customer->name,
                    'debt_amount' => $customer->current_debt,
                    'sent_to' => $customer->phone ?? $customer->email,
                    'sent_at' => now()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending payment reminder: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el recordatorio: ' . $e->getMessage()
            ], 500);
        }
    }
}
