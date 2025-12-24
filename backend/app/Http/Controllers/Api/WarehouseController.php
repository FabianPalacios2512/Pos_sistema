<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Obtener lista de bodegas/tiendas activas
     */
    public function getActive(): JsonResponse
    {
        try {
            $warehouses = Warehouse::where('active', true)
                ->orderBy('is_default', 'desc')
                ->orderBy('name', 'asc')
                ->get(['id', 'name', 'address', 'phone', 'is_default', 'active']);

            return response()->json([
                'success' => true,
                'data' => $warehouses,
                'count' => $warehouses->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener bodegas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener productos de una sede específica con su stock
     */
    public function getWarehouseProducts($warehouseId): JsonResponse
    {
        try {
            $warehouse = Warehouse::findOrFail($warehouseId);

            // ✅ Obtener SOLO productos que existen en esta bodega (INNER JOIN)
            $products = \DB::table('products as p')
                ->join('product_warehouse as pw', function($join) use ($warehouseId) {
                    $join->on('p.id', '=', 'pw.product_id')
                         ->where('pw.warehouse_id', '=', $warehouseId);
                })
                ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
                ->where('p.active', true)
                ->select(
                    'p.id',
                    'p.name',
                    'p.sku',
                    'p.barcode',
                    'p.image_url',
                    'p.product_type',  // 🆕 Necesitamos saber si es 'variable'
                    'p.cost_price',
                    'p.sale_price',
                    'p.min_stock',
                    'p.max_stock',
                    'p.unit',
                    'p.measurement_unit',
                    'c.name as category_name',
                    'pw.stock as stock'  // Ya no necesitamos COALESCE porque siempre habrá valor
                )
                ->orderBy('p.name', 'asc')
                ->get()
                ->map(function($product) {
                    // 🆕 Para productos fashion, calcular costo desde variantes
                    if ($product->product_type === 'variable') {
                        $variantCosts = \DB::table('product_variants')
                            ->where('product_id', $product->id)
                            ->whereNotNull('cost_price')
                            ->where('cost_price', '>', 0)
                            ->pluck('cost_price');

                        if ($variantCosts->isNotEmpty()) {
                            // Calcular rango de costos
                            $minCost = $variantCosts->min();
                            $maxCost = $variantCosts->max();

                            $product->cost_price = $minCost; // Costo mínimo por defecto
                            $product->cost_price_display = $minCost == $maxCost
                                ? '$' . number_format($minCost, 0, ',', '.')
                                : '$' . number_format($minCost, 0, ',', '.') . ' - $' . number_format($maxCost, 0, ',', '.');
                        } else {
                            $product->cost_price_display = '$0';
                        }
                    } else {
                        // Producto simple: usar cost_price directo
                        $product->cost_price_display = '$' . number_format($product->cost_price ?? 0, 0, ',', '.');
                    }

                    return $product;
                });

            // Calcular resumen
            $totalStock = $products->sum('stock');
            $totalValue = $products->sum(function($p) {
                return $p->stock * $p->sale_price;
            });
            $lowStockCount = $products->filter(function($p) {
                return $p->stock > 0 && $p->stock <= $p->min_stock;
            })->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'warehouse' => [
                        'id' => $warehouse->id,
                        'name' => $warehouse->name,
                        'address' => $warehouse->address,
                        'phone' => $warehouse->phone,
                        'is_default' => $warehouse->is_default,
                        'active' => $warehouse->active
                    ],
                    'summary' => [
                        'total_products' => $products->count(),
                        'total_stock' => $totalStock,
                        'total_value' => round($totalValue, 2),
                        'low_stock_count' => $lowStockCount,
                        'out_of_stock_count' => $products->where('stock', 0)->count()
                    ],
                    'products' => $products
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener productos de la bodega',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
