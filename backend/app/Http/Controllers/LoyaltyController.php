<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\LoyaltyTransaction;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoyaltyController extends Controller
{
    /**
     * Obtener configuración del sistema de loyalty
     */
    public function getSettings(): JsonResponse
    {
        try {
            $settings = SystemSetting::first();

            if (!$settings) {
                return response()->json([
                    'success' => false,
                    'message' => 'Configuración del sistema no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'enabled' => $settings->enable_loyalty_system ?? false,
                    'points_per_currency' => $settings->loyalty_points_per_currency ?? 0.001,
                    'point_value' => $settings->loyalty_point_value ?? 10,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener configuración',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcular puntos a ganar por un monto
     */
    public function calculatePointsToEarn(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $points = Customer::calculatePointsToEarn($request->amount);

            return response()->json([
                'success' => true,
                'data' => [
                    'amount' => $request->amount,
                    'points' => $points
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al calcular puntos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcular valor en dinero de puntos
     */
    public function calculatePointsValue(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'points' => 'required|integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $value = Customer::calculatePointsValue($request->points);

            return response()->json([
                'success' => true,
                'data' => [
                    'points' => $request->points,
                    'value' => $value
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al calcular valor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener puntos de un cliente
     */
    public function getCustomerPoints($customerId): JsonResponse
    {
        try {
            $customer = Customer::findOrFail($customerId);

            return response()->json([
                'success' => true,
                'data' => [
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'loyalty_points' => $customer->loyalty_points,
                    'points_value' => $customer->loyalty_points_value
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Obtener historial de transacciones de puntos de un cliente
     */
    public function getCustomerTransactions($customerId): JsonResponse
    {
        try {
            $customer = Customer::findOrFail($customerId);

            $transactions = LoyaltyTransaction::where('customer_id', $customerId)
                ->with(['invoice', 'createdBy'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'points' => $transaction->points,
                        'type' => $transaction->type,
                        'description' => $transaction->description,
                        'balance_after' => $transaction->balance_after,
                        'invoice_number' => $transaction->invoice ? $transaction->invoice->number : null,
                        'created_by' => $transaction->createdBy ? $transaction->createdBy->name : null,
                        'created_at' => $transaction->created_at->format('Y-m-d H:i:s')
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'customer' => [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'current_points' => $customer->loyalty_points
                    ],
                    'transactions' => $transactions
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener transacciones',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar si se pueden redimir puntos
     */
    public function validateRedemption(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
                'points' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $customer = Customer::findOrFail($request->customer_id);

            // Verificar que el sistema esté habilitado
            $settings = SystemSettings::first();
            if (!$settings || !$settings->enable_loyalty_system) {
                return response()->json([
                    'success' => false,
                    'message' => 'El sistema de puntos no está habilitado'
                ], 400);
            }

            // Verificar que tenga suficientes puntos
            if (!$customer->hasLoyaltyPoints($request->points)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Puntos insuficientes',
                    'data' => [
                        'available_points' => $customer->loyalty_points,
                        'requested_points' => $request->points,
                        'missing_points' => $request->points - $customer->loyalty_points
                    ]
                ], 400);
            }

            $discountValue = Customer::calculatePointsValue($request->points);

            return response()->json([
                'success' => true,
                'message' => 'Redención válida',
                'data' => [
                    'customer_id' => $customer->id,
                    'available_points' => $customer->loyalty_points,
                    'points_to_redeem' => $request->points,
                    'discount_value' => $discountValue,
                    'remaining_points' => $customer->loyalty_points - $request->points
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al validar redención',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ajuste manual de puntos (solo admin)
     */
    public function adjustPoints(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
                'points' => 'required|integer',
                'description' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $customer = Customer::findOrFail($request->customer_id);

            // Verificar que el ajuste no deje puntos negativos
            if ($customer->loyalty_points + $request->points < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'El ajuste resultaría en puntos negativos'
                ], 400);
            }

            $transaction = LoyaltyTransaction::recordAdjustment(
                $request->customer_id,
                $request->points,
                $request->description,
                Auth::id()
            );

            Log::info('🔧 Ajuste manual de puntos', [
                'customer_id' => $customer->id,
                'points' => $request->points,
                'description' => $request->description,
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Puntos ajustados correctamente',
                'data' => [
                    'customer_id' => $customer->id,
                    'points_adjusted' => $request->points,
                    'new_balance' => $customer->fresh()->loyalty_points,
                    'transaction_id' => $transaction->id
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al ajustar puntos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
