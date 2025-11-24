<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats()
    {
        try {
            $today = Carbon::today();
            $thisMonth = Carbon::now()->startOfMonth();

            // Ventas de hoy
            $todaySales = Invoice::whereDate('date', $today)
                ->where('status', 'paid')
                ->sum('total');
            $todaySalesCount = Invoice::whereDate('date', $today)
                ->where('status', 'paid')
                ->count();

            // Ventas del mes
            $monthSales = Invoice::where('date', '>=', $thisMonth)
                ->where('status', 'paid')
                ->sum('total');
            $monthSalesCount = Invoice::where('date', '>=', $thisMonth)
                ->where('status', 'paid')
                ->count();

            // Productos con stock bajo
            $lowStockProducts = Product::whereRaw('current_stock <= min_stock')
                ->where('manage_stock', true)
                ->where('active', true)
                ->count();

            // Total de productos activos
            $totalProducts = Product::where('active', true)->count();

            // Total de clientes activos
            $totalCustomers = Customer::where('active', true)->count();

            // Categorías más vendidas (últimos 30 días)
            $topCategories = Category::select('categories.name', 'categories.color')
                ->join('products', 'categories.id', '=', 'products.category_id')
                ->join('invoice_items', 'products.id', '=', 'invoice_items.product_id')
                ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->where('invoices.date', '>=', Carbon::now()->subDays(30))
                ->where('invoices.status', 'paid')
                ->groupBy('categories.id', 'categories.name', 'categories.color')
                ->selectRaw('SUM(invoice_items.quantity) as total_sold')
                ->orderBy('total_sold', 'desc')
                ->limit(5)
                ->get();

            // Productos más vendidos (últimos 30 días)
            $topProducts = Product::select('products.name', 'products.sale_price')
                ->join('invoice_items', 'products.id', '=', 'invoice_items.product_id')
                ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->where('invoices.date', '>=', Carbon::now()->subDays(30))
                ->where('invoices.status', 'paid')
                ->groupBy('products.id', 'products.name', 'products.sale_price')
                ->selectRaw('SUM(invoice_items.quantity) as total_sold')
                ->selectRaw('SUM(invoice_items.subtotal + invoice_items.tax_amount) as total_revenue')
                ->orderBy('total_sold', 'desc')
                ->limit(10)
                ->get();

            // Ventas por día (últimos 7 días)
            $dailySales = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $sales = Invoice::whereDate('date', $date->toDateString())
                    ->where('status', 'paid')
                    ->sum('total');
                $dailySales[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => $date->format('M j'),
                    'sales' => (float) $sales
                ];
            }

            // Inventario por categoría
            $inventoryByCategory = Category::select('categories.name', 'categories.color')
                ->join('products', 'categories.id', '=', 'products.category_id')
                ->where('products.active', true)
                ->groupBy('categories.id', 'categories.name', 'categories.color')
                ->selectRaw('COUNT(products.id) as product_count')
                ->selectRaw('SUM(products.current_stock * products.cost_price) as total_value')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => [
                        'today_sales' => [
                            'amount' => (float) $todaySales,
                            'count' => $todaySalesCount
                        ],
                        'month_sales' => [
                            'amount' => (float) $monthSales,
                            'count' => $monthSalesCount
                        ],
                        'low_stock_products' => $lowStockProducts,
                        'total_products' => $totalProducts,
                        'total_customers' => $totalCustomers
                    ],
                    'charts' => [
                        'daily_sales' => $dailySales,
                        'top_categories' => $topCategories,
                        'top_products' => $topProducts,
                        'inventory_by_category' => $inventoryByCategory
                    ]
                ],
                'message' => 'Estadísticas obtenidas exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
