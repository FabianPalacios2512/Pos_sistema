<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductAnalyticsController extends Controller
{
    /**
     * Obtener productos con métricas de rotación y rentabilidad
     */
    public function getProductsWithMetrics(Request $request)
    {
        try {
            // Período de análisis (últimos 90 días por defecto)
            $days = $request->get('days', 90);

            $products = DB::table('products as p')
                ->select([
                    'p.id',
                    'p.name',
                    'p.sku',
                    'p.cost_price',
                    'p.sale_price',
                    'p.current_stock',
                    'p.min_stock',
                    'p.unit',
                    'p.active',
                    'c.name as category_name',
                    's.name as supplier_name',
                    DB::raw('COALESCE(SUM(ii.quantity), 0) as units_sold'),
                    DB::raw('COUNT(DISTINCT i.id) as sales_count'),
                    DB::raw('ROUND(((p.sale_price - p.cost_price) / p.sale_price) * 100, 1) as margin_percentage'),
                    DB::raw('CASE
                        WHEN COALESCE(SUM(ii.quantity), 0) >= 50 THEN "A"
                        WHEN COALESCE(SUM(ii.quantity), 0) >= 15 THEN "B"
                        ELSE "C"
                    END as rotation_class')
                ])
                ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
                ->leftJoin('suppliers as s', 'p.supplier_id', '=', 's.id')
                ->leftJoin('invoice_items as ii', 'p.id', '=', 'ii.product_id')
                ->leftJoin('invoices as i', function($join) use ($days) {
                    $join->on('ii.invoice_id', '=', 'i.id')
                         ->where('i.date', '>=', DB::raw("DATE_SUB(CURDATE(), INTERVAL {$days} DAY)"))
                         ->where('i.status', '=', 'paid');
                })
                ->where('p.active', 1)
                ->groupBy([
                    'p.id', 'p.name', 'p.sku', 'p.cost_price', 'p.sale_price',
                    'p.current_stock', 'p.min_stock', 'p.unit', 'p.active',
                    'c.name', 's.name'
                ])
                ->orderBy('units_sold', 'desc')
                ->get();

            // Calcular estadísticas generales
            $totalProducts = $products->count();
            $classA = $products->where('rotation_class', 'A')->count();
            $classB = $products->where('rotation_class', 'B')->count();
            $classC = $products->where('rotation_class', 'C')->count();

            $avgMargin = $products->avg('margin_percentage');
            $totalRevenue = $products->sum(function($p) {
                return $p->units_sold * $p->sale_price;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $products,
                    'summary' => [
                        'total_products' => $totalProducts,
                        'class_a_count' => $classA,
                        'class_b_count' => $classB,
                        'class_c_count' => $classC,
                        'avg_margin' => round($avgMargin, 1),
                        'total_revenue' => $totalRevenue,
                        'analysis_period_days' => $days
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener métricas de productos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
