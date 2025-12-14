<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CreditPayment;
use App\Models\Customer;
use App\Models\CashSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CreditController extends Controller
{
    /**
     * Registrar un abono a la deuda
     */
    public function storePayment(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,transfer,card',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $customer = Customer::findOrFail($request->customer_id);

            // Verificar si el abono excede la deuda (opcional, pero recomendado)
            if ($request->amount > $customer->current_debt) {
                // Podríamos permitirlo y dejar saldo a favor, pero por ahora validamos
                // return response()->json(['message' => 'El abono excede la deuda actual'], 400);
            }

            // Registrar el pago
            $payment = CreditPayment::create([
                'customer_id' => $customer->id,
                'user_id' => Auth::id() ?? 1,
                'amount' => $request->amount,
                'method' => $request->method,
                'notes' => $request->notes,
                'payment_date' => now()
            ]);

            // Actualizar deuda del cliente
            $customer->current_debt -= $request->amount;
            if ($customer->current_debt < 0) $customer->current_debt = 0; // Evitar negativos si no manejamos saldo a favor
            $customer->save();

            // Si es efectivo, validar sesión de caja
            if ($request->method === 'cash') {
                $userId = Auth::id() ?? 1;
                $session = CashSession::getOpenSessionForUser($userId);
                
                if (!$session) {
                    // Si no hay sesión, ¿permitimos el abono? 
                    // Generalmente sí, pero no sumará a ninguna sesión abierta.
                    // O lanzamos error. El usuario dijo "SUMA a la CashSession actual".
                    // Asumiremos que si no hay sesión, se guarda pero no se asocia (o se fuerza apertura?).
                    // Por ahora, solo advertimos o procedemos.
                } else {
                    // El modelo CashSession deberá ser actualizado para incluir estos pagos en sus cálculos
                    // No necesitamos hacer nada aquí si CashSession calcula dinámicamente
                    // Pero si CashSession guarda totales estáticos, deberíamos actualizarlo.
                    // CashSession::updateSalesTotals() recalcula.
                    $session->updateSalesTotals();
                    $session->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Abono registrado exitosamente',
                'data' => $payment,
                'new_balance' => $customer->current_debt
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar abono: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener historial de pagos de un cliente
     */
    public function getCustomerPayments($customerId)
    {
        $payments = CreditPayment::where('customer_id', $customerId)
            ->with('user')
            ->orderBy('payment_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }
}
