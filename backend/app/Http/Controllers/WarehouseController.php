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

        // Límites por defecto según plan
        $defaultLimits = [
            'free_trial' => ['max' => 1, 'allowed' => false],
            'basic' => ['max' => 1, 'allowed' => false],
            'premium' => ['max' => 3, 'allowed' => true],
            'enterprise' => ['max' => -1, 'allowed' => true]
        ];

        $planLimits = $defaultLimits[$tenantPlan] ?? ['max' => 0, 'allowed' => false];

        // Si el admin configuró max_warehouses, usar ese valor (override del plan)
        $adminMaxWarehouses = tenant('max_warehouses');
        if ($adminMaxWarehouses !== null && (int)$adminMaxWarehouses > 0) {
            $planLimits['max'] = (int)$adminMaxWarehouses;
            $planLimits['allowed'] = true;
        }

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

        // Determinar límite: admin override > plan default
        $adminMaxWarehouses = tenant('max_warehouses');
        $maxAllowed = null;
        $allowed = false;

        if ($adminMaxWarehouses !== null && (int)$adminMaxWarehouses > 0) {
            $maxAllowed = (int)$adminMaxWarehouses;
            $allowed = true;
        } else {
            $defaultLimits = [
                'free_trial' => ['max' => 1, 'allowed' => false],
                'basic' => ['max' => 1, 'allowed' => false],
                'premium' => ['max' => 3, 'allowed' => true],
                'enterprise' => ['max' => -1, 'allowed' => true]
            ];
            $planLimit = $defaultLimits[$tenantPlan] ?? ['max' => 0, 'allowed' => false];
            $maxAllowed = $planLimit['max'];
            $allowed = $planLimit['allowed'];
        }

        if (!$allowed) {
            return response()->json([
                'message' => 'La funcionalidad Multi-tienda requiere plan Premium o Enterprise',
                'error' => 'plan_restriction'
            ], 403);
        }

        if ($maxAllowed !== -1 && $warehouseCount >= $maxAllowed) {
            return response()->json([
                'message' => "Has alcanzado el límite de {$maxAllowed} sedes. Contacta al administrador para aumentar el límite.",
                'error' => 'warehouse_limit_reached',
                'current_count' => $warehouseCount,
                'max_allowed' => $maxAllowed
            ], 403);
        }

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

            // ✅ NUEVA LÓGICA: NO crear productos automáticamente
            // Los productos solo aparecerán en esta sede cuando:
            // 1. Se haga un traslado desde otra sede
            // 2. Se haga una compra directa para esta sede
            // 3. Se haga un ajuste manual de inventario
            // Esto refleja la realidad: una sede nueva está VACÍA

            DB::commit();

            return response()->json([
                'message' => 'Bodega creada exitosamente',
                'warehouse' => $warehouse->load('products')
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
     * Matriz de distribución de inventario (todos los productos × todas las bodegas)
     */
    public function stockMatrix(Request $request)
    {
        $warehouses = Warehouse::where('active', true)->orderBy('is_default', 'desc')->orderBy('name')->get(['id', 'name', 'is_default']);

        // Obtener todos los registros del pivot product_warehouse
        $pivotRows = DB::table('product_warehouse')
            ->whereIn('warehouse_id', $warehouses->pluck('id'))
            ->get();

        // Obtener todos los productos que tienen stock en alguna bodega
        $productIds = $pivotRows->pluck('product_id')->unique()->values()->toArray();
        $products = Product::whereIn('id', $productIds)
            ->with('category')
            ->where('active', true)
            ->get();

        // Obtener variantes
        $variantIds = $pivotRows->whereNotNull('product_variant_id')->pluck('product_variant_id')->unique()->values()->toArray();
        $variants = \App\Models\ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');

        // Construir la matriz: cada fila es un producto (simple) o variante
        $matrix = [];

        foreach ($products as $product) {
            $productPivots = $pivotRows->where('product_id', $product->id);
            $isVariable = $product->product_type === 'variable';

            if ($isVariable) {
                // Agrupar por variante
                $variantPivots = $productPivots->whereNotNull('product_variant_id')->groupBy('product_variant_id');

                foreach ($variantPivots as $variantId => $rows) {
                    $variant = $variants->get($variantId);
                    if (!$variant) continue;

                    $stockByWarehouse = [];
                    $globalStock = 0;
                    foreach ($warehouses as $wh) {
                        $row = $rows->firstWhere('warehouse_id', $wh->id);
                        $stock = $row ? (int)$row->stock : 0;
                        $stockByWarehouse[$wh->id] = $stock;
                        $globalStock += $stock;
                    }

                    $matrix[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'variant_id' => (int)$variantId,
                        'variant_label' => $this->formatVariantLabel($variant->options_summary),
                        'sku' => $variant->sku ?? $product->sku,
                        'category' => $product->category->name ?? 'Sin categoría',
                        'min_stock' => (int)$product->min_stock,
                        'image_url' => $product->image_url,
                        'stock_by_warehouse' => $stockByWarehouse,
                        'global_stock' => $globalStock,
                    ];
                }
            } else {
                // Producto simple (sin variantes)
                $stockByWarehouse = [];
                $globalStock = 0;
                foreach ($warehouses as $wh) {
                    $row = $productPivots->where('warehouse_id', $wh->id)->whereNull('product_variant_id')->first()
                        ?? $productPivots->where('warehouse_id', $wh->id)->first();
                    $stock = $row ? (int)$row->stock : 0;
                    $stockByWarehouse[$wh->id] = $stock;
                    $globalStock += $stock;
                }

                $matrix[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'variant_id' => null,
                    'variant_label' => null,
                    'sku' => $product->sku,
                    'category' => $product->category->name ?? 'Sin categoría',
                    'min_stock' => (int)$product->min_stock,
                    'image_url' => $product->image_url,
                    'stock_by_warehouse' => $stockByWarehouse,
                    'global_stock' => $globalStock,
                ];
            }
        }

        // Filtro por búsqueda
        $search = $request->query('search');
        if ($search) {
            $term = strtolower($search);
            $matrix = array_values(array_filter($matrix, function ($row) use ($term) {
                return str_contains(strtolower($row['product_name']), $term)
                    || ($row['variant_label'] && str_contains(strtolower($row['variant_label']), $term))
                    || ($row['sku'] && str_contains(strtolower($row['sku']), $term));
            }));
        }

        // Ordenar por nombre de producto, luego por variante
        usort($matrix, function ($a, $b) {
            $cmp = strcmp($a['product_name'], $b['product_name']);
            if ($cmp !== 0) return $cmp;
            return strcmp($a['variant_label'] ?? '', $b['variant_label'] ?? '');
        });

        return response()->json([
            'warehouses' => $warehouses,
            'matrix' => $matrix,
            'total_rows' => count($matrix),
        ]);
    }

    /**
     * Helper para formatear label de variante
     */
    private function formatVariantLabel($optionsSummary)
    {
        $opts = is_string($optionsSummary) ? json_decode($optionsSummary, true) : $optionsSummary;
        if (!is_array($opts)) return '';

        $parts = [];
        foreach ($opts as $opt) {
            $name = $opt['name'] ?? '';
            $value = $opt['value'] ?? '';
            if (strtolower($name) === 'color' && str_starts_with($value, '#')) {
                $parts[] = $name . ': ●';
            } else {
                $parts[] = $name . ': ' . $value;
            }
        }
        return implode(' / ', $parts);
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
            ->unique('id') // Evitar duplicados si hay múltiples variantes
            ->map(function($product) use ($warehouse) {

                $stock = $product->pivot->stock;

                $variants = [];

                // Si es producto variable, sumar stock de todas sus variantes en esta bodega
                if ($product->product_type === 'variable') {
                    $stock = DB::table('product_warehouse')
                        ->where('warehouse_id', $warehouse->id)
                        ->where('product_id', $product->id)
                        ->sum('stock');

                    // Obtener desglose por variante
                    $variantRows = DB::table('product_warehouse')
                        ->where('warehouse_id', $warehouse->id)
                        ->where('product_id', $product->id)
                        ->whereNotNull('product_variant_id')
                        ->get();

                    if ($variantRows->count() > 0) {
                        $variantIds = $variantRows->pluck('product_variant_id')->toArray();
                        $variantModels = \App\Models\ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');

                        foreach ($variantRows as $row) {
                            $vm = $variantModels->get($row->product_variant_id);
                            if ($vm) {
                                $variants[] = [
                                    'id' => $vm->id,
                                    'sku' => $vm->sku,
                                    'options_summary' => $vm->options_summary,
                                    'stock' => (int)$row->stock,
                                    'price' => (float)($vm->price ?? $product->sale_price),
                                ];
                            }
                        }
                    }
                }

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'barcode' => $product->barcode,
                    'image_url' => $product->image_url,
                    'category_name' => $product->category->name ?? 'Sin categoría',
                    'supplier_name' => $product->supplier->name ?? 'Sin proveedor',
                    'sale_price' => (float)$product->sale_price,
                    'cost_price' => (float)$product->cost_price,
                    'stock' => (int)$stock,
                    'min_stock' => (int)$product->min_stock,
                    'max_stock' => (int)$product->max_stock,
                    'measurement_unit' => $product->measurement_unit ?? 'un',
                    'is_low_stock' => $stock > 0 && $stock <= $product->min_stock,
                    'product_type' => $product->product_type,
                    'variants' => $variants,
                ];
            })
            ->values(); // Reindexar array después de unique()

        // Calcular summary
        $totalStock = $products->sum('stock');

        // 🛠️ FIX: Calcular valor con sale_price (precio de venta), no cost_price
        $totalValue = $products->reduce(function($sum, $product) {
            return $sum + ($product['stock'] * $product['sale_price']);
        }, 0);

        // 🛠️ FIX: "Stock Bajo" = productos sin stock (0) + productos con stock bajo (0 < stock <= min_stock)
        $lowStock = $products->filter(function($product) {
            return $product['stock'] <= $product['min_stock'];
        })->count();

        // Contar solo productos sin stock (para referencia interna)
        $outOfStock = $products->filter(function($product) {
            return $product['stock'] === 0;
        })->count();

        return response()->json([
            'warehouse' => $warehouse,
            'products' => $products,
            'summary' => [
                'total_products' => $products->count(),
                'total_stock' => $totalStock,
                'total_value' => $totalValue,
                'out_of_stock_count' => $outOfStock,
                'low_stock_count' => $lowStock
            ]
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
