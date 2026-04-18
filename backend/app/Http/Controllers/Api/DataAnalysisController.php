<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DataAnalysisController extends Controller
{
    public function compareOctoberSales()
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Datos de tabla SALES octubre 2025
        $salesData = Sale::where('status', 'completed')
            ->whereYear('sale_date', $currentYear)
            ->whereMonth('sale_date', $currentMonth)
            ->selectRaw('COUNT(*) as count, SUM(total_amount) as total')
            ->first();

        // Datos de tabla INVOICES octubre 2025
        $invoicesData = DB::table('invoices')
            ->where('status', 'paid')
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->selectRaw('COUNT(*) as count, SUM(total) as total')
            ->first();

        return response()->json([
            'analysis' => [
                'sales_table' => [
                    'count' => $salesData->count ?? 0,
                    'total' => $salesData->total ?? 0
                ],
                'invoices_table' => [
                    'count' => $invoicesData->count ?? 0,
                    'total' => $invoicesData->total ?? 0
                ],
                'combined_total' => ($salesData->total ?? 0) + ($invoicesData->total ?? 0),
                'month_year' => "{$currentMonth}/{$currentYear}"
            ]
        ]);
    }

    public function analyzeInventoryValue()
    {
        // Helper: calcular valor de inventario con variantes
        $calcInventoryValue = function ($activeOnly = true, $positiveStockOnly = false) {
            $simpleQ = DB::table('products');
            if ($activeOnly) $simpleQ->where('active', true);
            if ($positiveStockOnly) $simpleQ->where('current_stock', '>', 0);
            $simple = (float) $simpleQ
                ->where(function ($q) { $q->whereNull('product_type')->orWhere('product_type', 'simple'); })
                ->selectRaw('COALESCE(SUM(current_stock * cost_price), 0) as val')
                ->value('val');

            $varQ = DB::table('product_variants as pv')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->where('p.product_type', 'variable')->where('pv.active', true);
            if ($activeOnly) $varQ->where('p.active', true);
            if ($positiveStockOnly) $varQ->where('pv.stock', '>', 0);
            $variable = (float) $varQ
                ->selectRaw('COALESCE(SUM(pv.stock * COALESCE(pv.cost_price, p.cost_price, 0)), 0) as val')
                ->value('val');

            return $simple + $variable;
        };

        $onlyPositiveStock = $calcInventoryValue(true, true);
        $allActiveProducts = $calcInventoryValue(true, false);
        $allProducts = $calcInventoryValue(false, false);

        // Productos inactivos con valor
        $inactiveProducts = DB::select('SELECT id, name, current_stock, cost_price, (current_stock * cost_price) as valor, active FROM products WHERE active = 0');

        // Productos con stock negativo
        $negativeStockProducts = DB::select('SELECT id, name, current_stock, cost_price, (current_stock * cost_price) as valor, active FROM products WHERE current_stock < 0');

        // Detalle productos activos
        $activeProductsDetail = DB::select('SELECT id, name, current_stock, cost_price, (current_stock * cost_price) as valor FROM products WHERE active = 1 ORDER BY valor DESC LIMIT 5');

        return response()->json([
            'inventory_analysis' => [
                'only_positive_stock_active' => $onlyPositiveStock,
                'all_active_products' => $allActiveProducts,
                'all_products_including_inactive' => $allProducts,
                'difference_with_inactive' => $allProducts - $allActiveProducts,
                'inactive_products' => $inactiveProducts,
                'negative_stock_products' => $negativeStockProducts,
                'top_value_products' => $activeProductsDetail
            ]
        ]);
    }
}
