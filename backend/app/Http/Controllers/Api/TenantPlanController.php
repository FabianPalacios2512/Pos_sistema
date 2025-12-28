<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TenantPlanController extends Controller
{
    /**
     * Actualizar el plan de un tenant recién creado
     */
    public function updatePlan(Request $request)
    {
        \Log::info('TenantPlanController::updatePlan - Peticion recibida', [
            'request_data' => $request->all(),
            'tenant_id' => $request->tenant_id,
            'plan' => $request->plan
        ]);

        // FIX: No usar exists:tenants,id porque puede fallar por conexion de BD
        // En su lugar, validamos manualmente despues
        $validator = Validator::make($request->all(), [
            'tenant_id' => 'required|string|max:100',
            'plan' => 'required|string|in:free_trial,trial_express,basic,premium,enterprise,emprendedor,negocio_pro',
        ]);

        if ($validator->fails()) {
            \Log::warning('TenantPlanController::updatePlan - Validacion fallida', [
                'errors' => $validator->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Datos invalidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // FIX: Buscar tenant manualmente para mejor manejo de errores
            $tenant = Tenant::find($request->tenant_id);

            if (!$tenant) {
                \Log::warning('TenantPlanController::updatePlan - Tenant NO encontrado', [
                    'tenant_id' => $request->tenant_id
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Tenant no encontrado. El registro puede no haberse completado.',
                    'errors' => ['tenant_id' => ['El tenant no existe en el sistema.']]
                ], 404);
            }

            \Log::info('TenantPlanController::updatePlan - Tenant encontrado', [
                'tenant_id' => $tenant->id,
                'current_plan' => $tenant->plan,
                'current_data' => $tenant->data
            ]);

            // Calcular nueva fecha de expiracion segun el plan
            $subscriptionEndsAt = null;
            $plan = $request->plan;

            // FIX: Mapear nombres de planes del frontend a nombres de BD si es necesario
            $planMapping = [
                'trial_express' => 'free_trial',  // Trial express es free_trial con 3 dias
                'emprendedor' => 'basic',         // Emprendedor = basic
                'negocio_pro' => 'premium',       // Negocio Pro = premium
            ];

            $dbPlan = $planMapping[$plan] ?? $plan;

            if ($plan === 'free_trial' || $plan === 'trial_express') {
                // Trial express = 3 dias, free_trial = 7 dias
                $trialDays = $plan === 'trial_express' ? 3 : 7;
                $subscriptionEndsAt = now()->addDays($trialDays);

                \Log::info('Activando trial', [
                    'plan' => $plan,
                    'days' => $trialDays,
                    'ends_at' => $subscriptionEndsAt->toDateTimeString()
                ]);
            } elseif (in_array($dbPlan, ['basic', 'premium', 'enterprise'])) {
                // 🔥 BUSCAR pending_payment para obtener payment_frequency correcto
                // Buscamos en 'pending' o 'completed' para manejar race conditions
                $pendingPayment = \App\Models\PendingPayment::where('tenant_id', $request->tenant_id)
                    ->whereIn('status', ['pending', 'completed'])
                    ->latest()
                    ->first();

                if ($pendingPayment) {
                    // ✅ VALIDACIÓN IDEMPOTENTE: Si ya fue procesado, retornar éxito
                    if ($pendingPayment->status === 'completed' && $tenant->plan === $dbPlan) {
                        \Log::info('TenantPlanController - Plan ya fue activado previamente (IDEMPOTENTE)', [
                            'tenant_id' => $request->tenant_id,
                            'plan' => $dbPlan,
                            'payment_reference' => $pendingPayment->reference,
                        ]);

                        return response()->json([
                            'success' => true,
                            'message' => 'Plan ya está activo',
                            'plan' => $dbPlan,
                            'subscription_ends_at' => $tenant->subscription_ends_at,
                            'idempotent' => true,
                        ]);
                    }

                    // Calcular segun payment_frequency
                    $subscriptionEndsAt = match($pendingPayment->payment_frequency) {
                        'yearly' => now()->addYear(),
                        '24months' => now()->addYears(2),
                        default => now()->addMonth(),
                    };

                    // 🔒 Marcar pago como completado usando updateOrFail para evitar race condition
                    if ($pendingPayment->status === 'pending') {
                        $affected = \App\Models\PendingPayment::where('id', $pendingPayment->id)
                            ->where('status', 'pending')
                            ->update([
                                'status' => 'completed',
                                'updated_at' => now(),
                            ]);

                        if ($affected === 0) {
                            \Log::warning('TenantPlanController - PendingPayment ya fue procesado por otra request', [
                                'tenant_id' => $request->tenant_id,
                                'reference' => $pendingPayment->reference,
                            ]);
                        }
                    }

                    \Log::info('TenantPlanController - Subscription activada desde pending_payment', [
                        'tenant_id' => $request->tenant_id,
                        'payment_frequency' => $pendingPayment->payment_frequency,
                        'subscription_ends_at' => $subscriptionEndsAt->toDateTimeString(),
                    ]);
                } else {
                    // Fallback: 1 mes si no hay pending_payment
                    $subscriptionEndsAt = now()->addMonth();
                    \Log::warning('TenantPlanController - No pending_payment found, usando 1 mes por defecto', [
                        'tenant_id' => $request->tenant_id,
                    ]);
                }
            }

            // Actualizar tenant
            $data = json_decode($tenant->data, true) ?? [];
            $isTrial = in_array($plan, ['free_trial', 'trial_express']);
            $data['trial_started_at'] = $isTrial ? now()->toDateTimeString() : null;
            $data['trial_days'] = $isTrial ? ($plan === 'trial_express' ? 3 : 7) : null;
            $data['payment_status'] = $isTrial ? 'trial' : 'paid';
            $data['plan_pending'] = false;
            $data['plan_selected_at'] = now()->toDateTimeString();

            $tenant->update([
                'plan' => $dbPlan,
                'subscription_ends_at' => $subscriptionEndsAt,
                'data' => json_encode($data),
            ]);

            \Log::info('Plan de tenant actualizado EXITOSAMENTE', [
                'tenant_id' => $tenant->id,
                'old_plan' => $tenant->getOriginal('plan'),
                'new_plan' => $dbPlan,
                'original_plan_name' => $plan,
                'subscription_ends_at' => $subscriptionEndsAt,
                'plan_pending' => false,
                'payment_status' => $data['payment_status']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Plan actualizado correctamente',
                'plan' => $dbPlan,
                'subscription_ends_at' => $subscriptionEndsAt
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al actualizar plan de tenant', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el plan: ' . $e->getMessage()
            ], 500);
        }
    }
}
