<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DiscountsController extends Controller
{
    /**
     * Listar descuentos
     */
    public function index(Request $request)
    {
        try {
            $query = Discount::query();

            // Filtros
            if ($request->has('active')) {
                $query->where('active', $request->boolean('active'));
            }

            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->has('type')) {
                $query->where('type', $request->get('type'));
            }

            if ($request->has('applies_to')) {
                $query->where('applies_to', $request->get('applies_to'));
            }

            // Verificar expiración
            if ($request->has('check_expiry') && $request->boolean('check_expiry')) {
                $query->where(function($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>', Carbon::now());
                });
            }

            $discounts = $query->orderBy('created_at', 'desc')->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $discounts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener descuentos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear descuento
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'code' => 'nullable|string|max:50|unique:discounts,code',
                'type' => 'required|in:percentage,fixed_amount',
                'value' => 'required|numeric|min:0',
                'applies_to' => 'required|in:all_products,specific_products,categories,customers',
                'applicable_items' => 'nullable|array',
                'minimum_amount' => 'nullable|numeric|min:0',
                'maximum_discount' => 'nullable|numeric|min:0',
                'usage_limit' => 'nullable|integer|min:1',
                'starts_at' => 'nullable|date',
                'expires_at' => 'nullable|date|after:starts_at',
                'active' => 'boolean',
                'stackable' => 'boolean',
                'allow_multiple_uses_per_user' => 'boolean',
            ]);

            // Generar código automáticamente si no se proporciona
            if (empty($validated['code'])) {
                $validated['code'] = $this->generateDiscountCode();
            }

            // Validaciones específicas
            if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
                return response()->json([
                    'success' => false,
                    'message' => 'El porcentaje no puede ser mayor a 100%'
                ], 422);
            }

            $discount = Discount::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Descuento creado correctamente',
                'data' => $discount
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
                'message' => 'Error al crear descuento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar descuento específico
     */
    public function show(Discount $discount)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $discount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener descuento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar descuento
     */
    public function update(Request $request, Discount $discount)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'code' => 'required|string|max:50|unique:discounts,code,' . $discount->id,
                'type' => 'required|in:percentage,fixed_amount',
                'value' => 'required|numeric|min:0',
                'applies_to' => 'required|in:all_products,specific_products,categories,customers',
                'applicable_items' => 'nullable|array',
                'minimum_amount' => 'nullable|numeric|min:0',
                'maximum_discount' => 'nullable|numeric|min:0',
                'usage_limit' => 'nullable|integer|min:1',
                'starts_at' => 'nullable|date',
                'expires_at' => 'nullable|date|after:starts_at',
                'active' => 'boolean',
                'stackable' => 'boolean',
                'allow_multiple_uses_per_user' => 'boolean',
            ]);

            // Validaciones específicas
            if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
                return response()->json([
                    'success' => false,
                    'message' => 'El porcentaje no puede ser mayor a 100%'
                ], 422);
            }

            $discount->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Descuento actualizado correctamente',
                'data' => $discount->fresh()
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
                'message' => 'Error al actualizar descuento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar descuento
     */
    public function destroy(Discount $discount)
    {
        try {
            $discount->delete();

            return response()->json([
                'success' => true,
                'message' => 'Descuento eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar descuento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar código de descuento
     */
    public function validateCode(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string',
                'customer_id' => 'nullable|integer|exists:customers,id',
                'cart_total' => 'required|numeric|min:0',
                'product_ids' => 'nullable|array',
                'category_ids' => 'nullable|array',
                'customer_email' => 'nullable|email',
                'customer_phone' => 'nullable|string',
                'user_identifier' => 'nullable|string', // IP, session, etc.
            ]);

            $discount = Discount::where('code', $request->code)
                ->where('active', true)
                ->first();

            if (!$discount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Código de descuento no válido'
                ], 404);
            }

            // Usar el método isValid() del modelo para verificar todas las condiciones
            if (!$discount->isValid()) {
                // Determinar qué condición específica falló para dar el mensaje correcto
                $now = Carbon::now();

                if ($discount->starts_at && $now->lt($discount->starts_at)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El descuento aún no está disponible'
                    ], 400);
                }

                if ($discount->expires_at && $now->gt($discount->expires_at)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El código de descuento ha expirado'
                    ], 400);
                }

                // Usar el conteo real de usos (como en el modelo)
                if ($discount->usage_limit && $discount->usages()->count() >= $discount->usage_limit) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Se ha alcanzado el límite de uso para este descuento'
                    ], 400);
                }

                // Si llegamos aquí, el descuento no está activo
                return response()->json([
                    'success' => false,
                    'message' => 'El descuento no está disponible'
                ], 400);
            }

            // Verificar si el usuario ya ha usado este descuento
            $userIdentifier = $request->user_identifier ?: $request->ip();
            $customerEmail = $request->customer_email;
            $customerPhone = $request->customer_phone;

            if (!$discount->canBeUsedBy($userIdentifier, $customerEmail, $customerPhone)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya has usado este código de descuento anteriormente'
                ], 400);
            }

            // Verificar monto mínimo
            if ($discount->minimum_amount && $request->cart_total < $discount->minimum_amount) {
                return response()->json([
                    'success' => false,
                    'message' => "El monto mínimo para aplicar este descuento es $" . number_format($discount->minimum_amount, 0, ',', '.')
                ], 400);
            }

            // Calcular descuento aplicable
            $applicableAmount = $this->calculateDiscount($discount, $request->cart_total, $request->all());

            // Calcular usos restantes usando el conteo real
            $usagesRemaining = null;
            if ($discount->usage_limit) {
                $realUsageCount = $discount->usages()->count();
                $usagesRemaining = $discount->usage_limit - $realUsageCount;
            }

            return response()->json([
                'success' => true,
                'message' => 'Código de descuento válido',
                'data' => [
                    'discount' => $discount,
                    'applicable_amount' => $applicableAmount,
                    'final_total' => $request->cart_total - $applicableAmount,
                    'usages_remaining' => $usagesRemaining,
                    'user_usage_count' => $discount->getUsageCountBy($userIdentifier, $customerEmail, $customerPhone)
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
                'message' => 'Error al validar código: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar código de descuento automático
     */
    private function generateDiscountCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Discount::where('code', $code)->exists());

        return $code;
    }

    /**
     * Calcular monto de descuento aplicable
     */
    private function calculateDiscount(Discount $discount, float $cartTotal, array $context)
    {
        $discountAmount = 0;

        if ($discount->type === 'percentage') {
            $discountAmount = ($cartTotal * $discount->value) / 100;
        } else {
            $discountAmount = $discount->value;
        }

        // Aplicar límite máximo si existe
        if ($discount->maximum_discount && $discountAmount > $discount->maximum_discount) {
            $discountAmount = $discount->maximum_discount;
        }

        // No puede ser mayor al total del carrito
        if ($discountAmount > $cartTotal) {
            $discountAmount = $cartTotal;
        }

        return round($discountAmount, 2);
    }

    /**
     * Registrar el uso de un descuento cuando se completa la venta
     */
    public function recordUsage(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string',
                'customer_id' => 'nullable|integer|exists:customers,id',
                'customer_email' => 'nullable|email',
                'customer_phone' => 'nullable|string',
                'user_identifier' => 'nullable|string',
                'invoice_id' => 'nullable|integer',
                'amount_used' => 'required|numeric|min:0'
            ]);

            $discount = Discount::where('code', $request->code)
                ->where('active', true)
                ->first();

            if (!$discount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Código de descuento no encontrado'
                ], 404);
            }

            // Registrar el uso
            $userIdentifier = $request->user_identifier ?: $request->ip();
            $customerEmail = $request->customer_email;
            $customerPhone = $request->customer_phone;

            $discount->recordUsage($userIdentifier, $customerEmail, $customerPhone, $request->invoice_id, $request->amount_used);

            // Obtener contadores actualizados
            $usagesRemaining = null;
            if ($discount->usage_limit) {
                $usagesRemaining = $discount->usage_limit - $discount->fresh()->used_count;
            }

            return response()->json([
                'success' => true,
                'message' => 'Uso de descuento registrado correctamente',
                'data' => [
                    'used_count' => $discount->fresh()->used_count,
                    'usage_limit' => $discount->usage_limit,
                    'usages_remaining' => $usagesRemaining,
                    'user_usage_count' => $discount->getUsageCountBy($userIdentifier, $customerEmail, $customerPhone)
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
                'message' => 'Error al registrar uso: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar código promocional aleatorio
     */
    public function generateCode()
    {
        try {
            $code = $this->generateDiscountCode();

            return response()->json([
                'success' => true,
                'data' => ['code' => $code]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar código: ' . $e->getMessage()
            ], 500);
        }
    }
}
