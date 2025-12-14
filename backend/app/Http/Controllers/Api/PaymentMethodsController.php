<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodsController extends Controller
{
    /**
     * Listar métodos de pago
     */
    public function index(Request $request)
    {
        try {
            $query = PaymentMethod::query();

            // Filtros
            if ($request->has('active')) {
                $query->where('active', $request->boolean('active'));
            }

            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->has('type')) {
                $query->where('type', $request->get('type'));
            }

            if ($request->has('requires_change')) {
                $query->where('requires_change', $request->boolean('requires_change'));
            }

            $paymentMethods = $query->orderBy('sort_order')->orderBy('name')->get();

            return response()->json([
                'success' => true,
                'data' => $paymentMethods
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener métodos de pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear método de pago
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:payment_methods,name',
                'description' => 'nullable|string',
                'type' => 'required|in:cash,card,digital,bank_transfer,check,credit',
                'icon' => 'nullable|string|max:255',
                'requires_change' => 'boolean',
                'fee_percentage' => 'nullable|numeric|min:0|max:100',
                'fee_fixed' => 'nullable|numeric|min:0',
                'configuration' => 'nullable|array',
                'active' => 'boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            // Asignar sort_order automáticamente si no se proporciona
            if (!isset($validated['sort_order'])) {
                $validated['sort_order'] = PaymentMethod::max('sort_order') + 1;
            }

            $paymentMethod = PaymentMethod::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Método de pago creado correctamente',
                'data' => $paymentMethod
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear método de pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar método de pago específico
     */
    public function show(PaymentMethod $paymentMethod)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $paymentMethod
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener método de pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar método de pago
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:payment_methods,name,' . $paymentMethod->id,
                'description' => 'nullable|string',
                'type' => 'required|in:cash,card,digital,bank_transfer,check,credit',
                'icon' => 'nullable|string|max:255',
                'requires_change' => 'boolean',
                'fee_percentage' => 'nullable|numeric|min:0|max:100',
                'fee_fixed' => 'nullable|numeric|min:0',
                'configuration' => 'nullable|array',
                'active' => 'boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            $paymentMethod->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Método de pago actualizado correctamente',
                'data' => $paymentMethod->fresh()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar método de pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar método de pago
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        try {
            $paymentMethod->delete();

            return response()->json([
                'success' => true,
                'message' => 'Método de pago eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar método de pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reordenar métodos de pago
     */
    public function reorder(Request $request)
    {
        try {
            $validated = $request->validate([
                'payment_methods' => 'required|array',
                'payment_methods.*.id' => 'required|integer|exists:payment_methods,id',
                'payment_methods.*.sort_order' => 'required|integer|min:0',
            ]);

            foreach ($validated['payment_methods'] as $methodData) {
                PaymentMethod::where('id', $methodData['id'])
                    ->update(['sort_order' => $methodData['sort_order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Orden de métodos de pago actualizado correctamente'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reordenar métodos de pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Alternar estado activo de método de pago
     */
    public function toggleStatus(PaymentMethod $paymentMethod)
    {
        try {
            $paymentMethod->update(['active' => !$paymentMethod->active]);

            $status = $paymentMethod->active ? 'activado' : 'desactivado';

            return response()->json([
                'success' => true,
                'message' => "Método de pago {$status} correctamente",
                'data' => $paymentMethod->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado del método de pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcular comisión para un método de pago
     */
    public function calculateFee(Request $request, PaymentMethod $paymentMethod)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0',
            ]);

            $amount = $validated['amount'];
            $fee = 0;

            // Calcular comisión porcentual
            if ($paymentMethod->fee_percentage > 0) {
                $fee += ($amount * $paymentMethod->fee_percentage) / 100;
            }

            // Agregar comisión fija
            if ($paymentMethod->fee_fixed > 0) {
                $fee += $paymentMethod->fee_fixed;
            }

            $totalAmount = $amount + $fee;

            return response()->json([
                'success' => true,
                'data' => [
                    'original_amount' => $amount,
                    'fee_amount' => round($fee, 2),
                    'total_amount' => round($totalAmount, 2),
                    'payment_method' => $paymentMethod->name
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al calcular comisión: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener métodos de pago activos para POS
     */
    public function forPos()
    {
        try {
            // 🔒 Obtener plan del tenant
            $tenantPlan = \DB::connection('mysql')
                ->table('tenants')
                ->where('id', tenant('id'))
                ->value('plan');

            $paymentMethods = PaymentMethod::where('active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            // 🔒 SEGURIDAD: Filtrar "Crédito en Tienda" si es plan gratuito
            if (($tenantPlan ?? 'free_trial') === 'free_trial') {
                $paymentMethods = $paymentMethods->filter(function($method) {
                    return $method->code !== 'credito_tienda';
                })->values();
            }

            return response()->json([
                'success' => true,
                'data' => $paymentMethods
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener métodos de pago para POS: ' . $e->getMessage()
            ], 500);
        }
    }
}
