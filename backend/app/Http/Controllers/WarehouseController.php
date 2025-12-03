<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    /**
     * Listar todas las bodegas/sedes
     * Incluye información de límites según plan
     */
    public function index()
    {
        $warehouses = Warehouse::with(['products' => function($query) {
            $query->select('products.id', 'products.name');
        }])
        ->withCount('products')
        ->orderBy('is_default', 'desc')
        ->orderBy('name')
        ->get();

        // Obtener información del plan y límites
        $tenantPlan = tenant('plan') ?? 'free_trial';
        $warehouseCount = $warehouses->count();

        $limits = [
            'free_trial' => ['max' => 0, 'allowed' => false],
            'basic' => ['max' => 0, 'allowed' => false],
            'premium' => ['max' => 3, 'allowed' => true],
            'enterprise' => ['max' => -1, 'allowed' => true] // -1 = ilimitado
        ];

        $planLimits = $limits[$tenantPlan] ?? ['max' => 0, 'allowed' => false];

        return response()->json([
            'warehouses' => $warehouses,
            'plan_info' => [
                'current_plan' => $tenantPlan,
                'current_count' => $warehouseCount,
                'max_allowed' => $planLimits['max'],
                'can_create' => $planLimits['allowed'] && ($planLimits['max'] === -1 || $warehouseCount < $planLimits['max'])
            ]
        ]);
    }

    /**
     * Obtener una bodega específica
     */
    public function show($id)
    {
        $warehouse = Warehouse::with([
            'products' => function($query) {
                $query->select('products.*', 'product_warehouse.stock as warehouse_stock')
                    ->orderBy('products.name');
            }
        ])->findOrFail($id);

        return response()->json($warehouse);
    }

    /**
     * Crear una nueva bodega
     * 🎯 Validación por plan: Premium (max 3), Enterprise (ilimitado)
     */
    public function store(Request $request)
    {
        // Validar límite de tiendas según plan
        $tenantPlan = tenant('plan') ?? 'free_trial';
        $warehouseCount = Warehouse::count();

        // Planes que NO pueden usar multi-tienda
        if (in_array($tenantPlan, ['free_trial', 'basic'])) {
            return response()->json([
                'message' => '🔒 La funcionalidad Multi-tienda requiere plan Premium o Enterprise',
                'error' => 'plan_restriction'
            ], 403);
        }

        // Premium: máximo 3 tiendas
        if ($tenantPlan === 'premium' && $warehouseCount >= 3) {
            return response()->json([
                'message' => '⚠️ Has alcanzado el límite de 3 tiendas para tu plan Premium. Actualiza a Enterprise para tiendas ilimitadas.',
                'error' => 'warehouse_limit_reached',
                'current_count' => $warehouseCount,
                'max_allowed' => 3
            ], 403);
        }

        // Enterprise: sin límite (no hay validación adicional)

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
            'active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Si se marca como default, desmarcar las demás
            if ($validated['is_default'] ?? false) {
                Warehouse::where('is_default', true)->update(['is_default' => false]);
            }

            $warehouse = Warehouse::create($validated);

            // Agregar todos los productos con stock 0 a la nueva bodega
            $products = Product::all();
            foreach ($products as $product) {
                $warehouse->products()->attach($product->id, ['stock' => 0]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Bodega creada exitosamente',
                'warehouse' => $warehouse
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear la bodega',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar una bodega
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
            'active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $warehouse = Warehouse::findOrFail($id);

            // Si se marca como default, desmarcar las demás
            if (isset($validated['is_default']) && $validated['is_default']) {
                Warehouse::where('id', '!=', $id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            $warehouse->update($validated);

            DB::commit();

            return response()->json([
                'message' => 'Bodega actualizada exitosamente',
                'warehouse' => $warehouse
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar la bodega',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una bodega
     */
    public function destroy($id)
    {
        try {
            $warehouse = Warehouse::findOrFail($id);

            // No permitir eliminar la bodega por defecto
            if ($warehouse->is_default) {
                return response()->json([
                    'message' => 'No se puede eliminar la bodega por defecto'
                ], 422);
            }

            // Verificar si tiene stock
            $hasStock = $warehouse->products()->wherePivot('stock', '>', 0)->exists();
            if ($hasStock) {
                return response()->json([
                    'message' => 'No se puede eliminar una bodega con stock. Traslade los productos primero.'
                ], 422);
            }

            $warehouse->delete();

            return response()->json([
                'message' => 'Bodega eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la bodega',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener inventario de una bodega
     */
    public function inventory($id)
    {
        $warehouse = Warehouse::findOrFail($id);

        $products = $warehouse->products()
            ->with(['category', 'supplier'])
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category->name ?? 'Sin categoría',
                    'supplier' => $product->supplier->name ?? 'Sin proveedor',
                    'sale_price' => $product->sale_price,
                    'cost_price' => $product->cost_price,
                    'stock' => $product->pivot->stock,
                    'min_stock' => $product->min_stock,
                    'is_low_stock' => $product->pivot->stock <= $product->min_stock,
                ];
            });

        return response()->json([
            'warehouse' => $warehouse,
            'products' => $products
        ]);
    }

    /**
     * Actualizar stock de un producto en una bodega
     */
    public function updateStock(Request $request, $id)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
            'type' => 'required|in:add,subtract,adjust',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $warehouse = Warehouse::findOrFail($id);
            $product = Product::findOrFail($validated['product_id']);

            $currentStock = $warehouse->getProductStock($product->id);
            $newStock = $currentStock;

            switch ($validated['type']) {
                case 'add':
                    $newStock = $currentStock + $validated['quantity'];
                    break;
                case 'subtract':
                    $newStock = $currentStock - $validated['quantity'];
                    break;
                case 'adjust':
                    $newStock = $validated['quantity'];
                    break;
            }

            if ($newStock < 0) {
                return response()->json([
                    'message' => 'El stock no puede ser negativo'
                ], 422);
            }

            $warehouse->updateProductStock($product->id, $newStock);

            // Registrar movimiento
            \App\Models\InventoryMovement::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'type' => 'adjustment',
                'quantity' => $newStock - $currentStock,
                'previous_stock' => $currentStock,
                'new_stock' => $newStock,
                'reason' => $validated['notes'] ?? 'Ajuste manual de inventario',
                'user_id' => auth()->id(),
                'movement_date' => now(),
            ]);

            // Actualizar el current_stock del producto (suma de todas las bodegas)
            $totalStock = $product->warehouses()->sum('product_warehouse.stock');
            $product->update(['current_stock' => $totalStock]);

            DB::commit();

            return response()->json([
                'message' => 'Stock actualizado exitosamente',
                'new_stock' => $newStock
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar el stock',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener bodega por defecto
     */
    public function getDefault()
    {
        $warehouse = Warehouse::where('is_default', true)->first();

        if (!$warehouse) {
            // Si no hay bodega por defecto, tomar la primera activa
            $warehouse = Warehouse::where('active', true)->first();
        }

        return response()->json($warehouse);
    }
}
