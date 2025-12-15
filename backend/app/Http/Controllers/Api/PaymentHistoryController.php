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
     * PÚBLICO - Valida usando tenantId directamente
     */
    public function getPaymentHistory(Request $request, $tenantId)
    {
        try {
            // Validar que el tenantId es válido
            if (!$tenantId || !is_string($tenantId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID de negocio inválido',
                ], 400);
            }

            // Verificar que el tenant existe
            $tenant = \App\Models\Tenant::find($tenantId);
            if (!$tenant) {
                Log::warning('PaymentHistoryController::getPaymentHistory - Tenant no encontrado', [
                    'tenant_id' => $tenantId,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Negocio no encontrado',
                ], 404);
            }            // Obtener pagos del tenant, ordenados por más recientes primero
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
