<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\PendingPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PlanUpgradeController extends Controller
{
    /**
     * Procesar upgrade de plan DESPUÉS del pago (públicamente accessible)
     *
     * Usado por PaymentSuccess.vue después de que Wompi redirige
     * No requiere autenticación porque viene de Wompi (servidor externo)
     * Valida usando: tenant_id + reference (que vinculan el pago con el upgrade)
     */
    public function processUpgrade(Request $request)
    {
        // 🔥 LOG AGRESIVO PARA DEBUG

        // ✅ VALIDAR DATOS REQUERIDOS
        $validator = Validator::make($request->all(), [
            'tenant_id' => 'required|string',
            'plan' => 'required|string|in:basic,premium,enterprise',
            'reference' => 'required|string',
            'is_upgrade' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            Log::warning('PlanUpgradeController::processUpgrade - Validación fallida', [
                'errors' => $validator->errors(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Datos de upgrade inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $tenantId = $request->tenant_id;
            $newPlan = $request->plan;
            $reference = $request->reference;

            // ✅ VERIFICAR TENANT EXISTE
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                Log::error('PlanUpgradeController::processUpgrade - Tenant no encontrado', [
                    'tenant_id' => $tenantId,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Negocio no encontrado',
                ], 404);
            }

            // ✅ VERIFICAR QUE EXISTE UN PENDING PAYMENT CON ESTE REFERENCE
            // Buscar en cualquier status (pending o completed por webhook)
            $pendingPayment = PendingPayment::where('reference', $reference)
                ->where('tenant_id', $tenantId)
                ->whereIn('status', ['pending', 'completed'])  // 🔥 Aceptar ambos estados
                ->latest()
                ->first();

            if (!$pendingPayment) {
                Log::error('PlanUpgradeController::processUpgrade - Pago pendiente no encontrado', [
                    'tenant_id' => $tenantId,
                    'reference' => $reference,
                    'searched_statuses' => ['pending', 'completed'],
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Pago no encontrado. Contacta a soporte.',
                ], 404);
            }

            // ✅ VERIFICAR QUE EL PLAN COINCIDE
            if ($pendingPayment->plan !== $newPlan) {
                Log::warning('PlanUpgradeController::processUpgrade - Plan no coincide', [
                    'tenant_id' => $tenantId,
                    'pending_plan' => $pendingPayment->plan,
                    'request_plan' => $newPlan,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'El plan no coincide con el pago',
                ], 400);
            }

            $oldPlan = $tenant->plan;

            // ✅ VALIDAR QUE ES UN UPGRADE (nuevo plan > plan actual)
            // PERO si el plan ya fue actualizado por el webhook, está bien
            $planHierarchy = [
                'free_trial' => 0,
                'trial_express' => 0,
                'basic' => 1,
                'premium' => 2,
                'enterprise' => 3,
            ];

            $oldLevel = $planHierarchy[$oldPlan] ?? -1;
            $newLevel = $planHierarchy[$newPlan] ?? -1;

            // Si el plan ya es el nuevo plan (actualizado por webhook), retornar éxito
            if ($oldPlan === $newPlan) {
                return response()->json([
                    'success' => true,
                    'message' => 'Plan ya está actualizado',
                    'data' => [
                        'old_plan' => $oldPlan,
                        'new_plan' => $newPlan,
                        'subscription_ends_at' => $tenant->subscription_ends_at->toDateTimeString(),
                    ],
                ]);
            }

            if ($newLevel <= $oldLevel) {
                Log::warning('PlanUpgradeController::processUpgrade - No es upgrade válido', [
                    'tenant_id' => $tenantId,
                    'old_plan' => $oldPlan,
                    'new_plan' => $newPlan,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede cambiar a un plan inferior',
                ], 400);
            }

            // ✅ CALCULAR FECHA DE EXPIRACIÓN SEGÚN payment_frequency
            $paymentFrequency = $pendingPayment->payment_frequency;
            $subscriptionEndsAt = match($paymentFrequency) {
                'yearly' => now()->addYear(),
                '24months' => now()->addYears(2),
                default => now()->addMonth(),  // monthly
            };


            // ✅ ACTUALIZAR TENANT
            $data = json_decode($tenant->data, true) ?? [];
            $data['plan_upgraded_at'] = now()->toDateTimeString();
            $data['previous_plan'] = $oldPlan;
            $data['upgrade_payment_frequency'] = $paymentFrequency;
            $data['plan_pending'] = false;

            $tenant->update([
                'plan' => $newPlan,
                'subscription_ends_at' => $subscriptionEndsAt,
                'data' => json_encode($data),
            ]);

            // ✅ MARCAR PAGO COMO COMPLETADO (si aún está pending)
            if ($pendingPayment->status === 'pending') {
                $pendingPayment->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            } else {
            }


            return response()->json([
                'success' => true,
                'message' => 'Plan actualizado correctamente',
                'data' => [
                    'old_plan' => $oldPlan,
                    'new_plan' => $newPlan,
                    'payment_frequency' => $paymentFrequency,
                    'subscription_ends_at' => $subscriptionEndsAt->toDateTimeString(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('PlanUpgradeController::processUpgrade - Error procesando upgrade', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el upgrade: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Solicitar upgrade de plan (autenticado)
     *
     * Usado por usuarios autenticados desde SettingsView
     * Requiere autenticación porque el usuario está en sesión
     */
    public function upgrade(Request $request)
    {

        // ✅ VALIDAR AUTENTICACIÓN
        if (!$request->user()) {
            Log::warning('PlanUpgradeController::upgrade - No autenticado');
            return response()->json([
                'success' => false,
                'message' => 'No autorizado. Debes estar autenticado.',
            ], 401);
        }

        // ✅ OBTENER TENANT_ID DEL USUARIO AUTENTICADO
        $tenantId = $request->user()->tenant_id;

        if (!$tenantId) {
            Log::error('PlanUpgradeController::upgrade - Usuario sin tenant_id', [
                'user_id' => $request->user()->id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Usuario no asociado a ningún negocio.',
            ], 403);
        }

        // ✅ VALIDAR DATOS
        $validator = Validator::make($request->all(), [
            'plan' => 'required|string|in:basic,premium,enterprise',
            'payment_frequency' => 'required|string|in:monthly,yearly,24months',
            'is_upgrade' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            Log::warning('PlanUpgradeController::upgrade - Validación fallida', [
                'errors' => $validator->errors(),
                'tenant_id' => $tenantId,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Datos de upgrade inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // ✅ VERIFICAR QUE TENANT EXISTE
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                Log::error('PlanUpgradeController::upgrade - Tenant no encontrado', [
                    'tenant_id' => $tenantId,
                    'user_id' => $request->user()->id,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Negocio no encontrado',
                ], 404);
            }

            $oldPlan = $tenant->plan;
            $newPlan = $request->plan;

            // ✅ VALIDAR QUE ES UN UPGRADE (nuevo plan > plan actual)
            $planHierarchy = [
                'free_trial' => 0,
                'trial_express' => 0,
                'basic' => 1,
                'premium' => 2,
                'enterprise' => 3,
            ];

            $oldLevel = $planHierarchy[$oldPlan] ?? -1;
            $newLevel = $planHierarchy[$newPlan] ?? -1;

            if ($newLevel <= $oldLevel) {
                Log::warning('PlanUpgradeController::upgrade - Intento de downgrade o upgrade al mismo plan', [
                    'tenant_id' => $tenantId,
                    'old_plan' => $oldPlan,
                    'new_plan' => $newPlan,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes cambiar a un plan inferior o al mismo plan',
                ], 400);
            }

            // ✅ BUSCAR pending_payment PARA OBTENER payment_frequency
            $pendingPayment = PendingPayment::where('tenant_id', $tenantId)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if (!$pendingPayment) {
                Log::error('PlanUpgradeController::upgrade - No se encontró pending_payment', [
                    'tenant_id' => $tenantId,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del pago. Intenta de nuevo.',
                ], 404);
            }

            // ✅ CALCULAR NUEVA FECHA DE EXPIRACIÓN SEGÚN payment_frequency
            $paymentFrequency = $pendingPayment->payment_frequency;
            $subscriptionEndsAt = match($paymentFrequency) {
                'yearly' => now()->addYear(),
                '24months' => now()->addYears(2),
                default => now()->addMonth(),  // monthly
            };


            // ✅ ACTUALIZAR TENANT
            $data = json_decode($tenant->data, true) ?? [];
            $data['plan_upgraded_at'] = now()->toDateTimeString();
            $data['previous_plan'] = $oldPlan;
            $data['upgrade_payment_frequency'] = $paymentFrequency;
            $data['plan_pending'] = false;

            $tenant->update([
                'plan' => $newPlan,
                'subscription_ends_at' => $subscriptionEndsAt,
                'data' => json_encode($data),
            ]);

            // ✅ MARCAR PENDING PAYMENT COMO COMPLETADO
            $pendingPayment->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);


            return response()->json([
                'success' => true,
                'message' => 'Plan actualizado correctamente',
                'data' => [
                    'old_plan' => $oldPlan,
                    'new_plan' => $newPlan,
                    'payment_frequency' => $paymentFrequency,
                    'subscription_ends_at' => $subscriptionEndsAt->toDateTimeString(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('PlanUpgradeController::upgrade - Error procesando upgrade', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId,
                'user_id' => $request->user()?->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el upgrade: ' . $e->getMessage(),
            ], 500);
        }
    }
}