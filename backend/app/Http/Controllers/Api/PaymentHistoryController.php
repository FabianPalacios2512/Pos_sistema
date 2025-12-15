<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PendingPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentHistoryController extends Controller
{
    /**
     * Obtener historial de pagos de un tenant
     *
     * GET /api/payment-history/{tenantId}
     * Requiere autenticación
     */
    public function getPaymentHistory(Request $request, $tenantId)
    {
        try {
            // Validar que el usuario está autenticado
            if (!$request->user()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado',
                ], 401);
            }

            // Validar que el usuario pertenece a este tenant
            if ($request->user()->tenant_id !== $tenantId) {
                Log::warning('PaymentHistoryController::getPaymentHistory - Usuario intenta acceder a otro tenant', [
                    'user_id' => $request->user()->id,
                    'user_tenant_id' => $request->user()->tenant_id,
                    'requested_tenant_id' => $tenantId,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes acceso a este historial',
                ], 403);
            }

            // Obtener pagos del tenant, ordenados por más recientes primero
            $payments = PendingPayment::where('tenant_id', $tenantId)
                ->orderBy('created_at', 'desc')
                ->limit(50)  // Últimos 50 pagos
                ->get();

            Log::info('PaymentHistoryController::getPaymentHistory - Historial obtenido', [
                'tenant_id' => $tenantId,
                'count' => $payments->count(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $payments,
            ]);

        } catch (\Exception $e) {
            Log::error('PaymentHistoryController::getPaymentHistory - Error', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el historial de pagos',
            ], 500);
        }
    }
}
