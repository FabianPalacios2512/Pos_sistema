<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

/**
 * Controller optimizado para dashboard con cache inteligente
 */
class OptimizedDashboardController extends Controller
{
    /**
     * Obtener datos del dashboard con cache inteligente
     */
    public function getDashboardData()
    {
        // Cache por 5 minutos para datos del dashboard
        $cacheKey = 'dashboard_data_' . now()->format('Y-m-d_H-i');

        return Cache::remember($cacheKey, 300, function () {
            $today = now()->format('Y-m-d');

            // Consulta optimizada con índices para métricas principales
            $metrics = DB::select("
                SELECT
                    COUNT(CASE WHEN DATE(i.created_at) = ? THEN 1 END) as ventas_hoy,
                    COALESCE(SUM(CASE WHEN DATE(i.created_at) = ? THEN i.total END), 0) as ingresos_hoy,
                    COUNT(CASE WHEN i.status = 'paid' THEN 1 END) as total_transacciones,
                    COALESCE(AVG(i.total), 0) as promedio_venta,
                    COUNT(CASE WHEN p.current_stock < p.min_stock THEN 1 END) as productos_agotados
                FROM invoices i
                CROSS JOIN products p
                WHERE i.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                   OR p.active = 1
            ", [$today, $today]);

            // Productos más vendidos (últimos 30 días) - optimizado con índices
            $topProducts = DB::select("
                SELECT
                    p.name,
                    p.current_stock,
                    SUM(ii.quantity) as cantidad_vendida,
                    SUM(ii.quantity * ii.unit_price) as total_vendido
                FROM invoice_items ii
                INNER JOIN products p ON ii.product_id = p.id
                INNER JOIN invoices i ON ii.invoice_id = i.id
                WHERE i.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                  AND i.status = 'paid'
                GROUP BY p.id, p.name, p.current_stock
                ORDER BY cantidad_vendida DESC
                LIMIT 5
            ");

            // Sesiones de caja activas
            $activeSessions = DB::select("
                SELECT
                    cs.id,
                    u.name as cajero,
                    cs.opening_amount,
                    COALESCE(SUM(i.total), 0) as ventas_actuales,
                    cs.opening_date
                FROM cash_sessions cs
                INNER JOIN users u ON cs.user_id = u.id
                LEFT JOIN invoices i ON cs.id = i.cash_session_id AND i.status = 'paid'
                WHERE cs.status = 'open'
                GROUP BY cs.id, u.name, cs.opening_amount, cs.opening_date
            ");

            return [
                'success' => true,
                'data' => [
                    'metrics' => $metrics[0] ?? null,
                    'top_products' => $topProducts,
                    'active_sessions' => $activeSessions,
                    'cache_info' => [
                        'generated_at' => now()->toISOString(),
                        'cache_key' => 'dashboard_data_' . now()->format('Y-m-d_H-i')
                    ]
                ]
            ];
        });
    }

    /**
     * Últimas transacciones optimizadas con índices
     */
    public function getRecentTransactions(Request $request)
    {
        $limit = $request->get('limit', 10);
        $cacheKey = "recent_transactions_{$limit}_" . floor(time() / 60); // Cache por 1 minuto

        return Cache::remember($cacheKey, 60, function () use ($limit) {

            // Consulta optimizada con índices específicos
            $transactions = DB::table('invoices')
                ->select([
                    'id',
                    'number',
                    'total',
                    'status',
                    'date',
                    'customer_id',
                    'payment_method'
                ])
                ->where('status', '!=', 'cancelled')
                ->orderByDesc('id') // Más rápido que orderBy('date') si id es autoincrement
                ->limit($limit)
                ->get();

            // Obtener nombres de clientes en una sola consulta
            $customerIds = $transactions->pluck('customer_id')->unique()->filter();
            $customers = [];

            if ($customerIds->isNotEmpty()) {
                $customers = DB::table('customers')
                    ->whereIn('id', $customerIds)
                    ->pluck('name', 'id');
            }

            // Mapear resultados
            $formattedTransactions = $transactions->map(function ($transaction) use ($customers) {
                return [
                    'id' => $transaction->id,
                    'number' => $transaction->number,
                    'total' => number_format($transaction->total, 0),
                    'total_raw' => $transaction->total,
                    'status' => $transaction->status,
                    'date' => Carbon::parse($transaction->date)->format('d/m/Y, H:i'),
                    'customer_name' => $customers[$transaction->customer_id] ?? 'Cliente Final',
                    'payment_method' => $transaction->payment_method
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedTransactions,
                'cached_at' => now()->format('H:i:s')
            ]);
        });
    }

    /**
     * Métricas principales optimizadas
     */
    public function getMainMetrics(Request $request)
    {
        $period = $request->get('period', 'today');
        $cacheKey = "main_metrics_{$period}_" . floor(time() / 300); // Cache por 5 minutos

        return Cache::remember($cacheKey, 300, function () use ($period) {
            $dateRange = $this->getDateRange($period);

            // Una sola consulta con agregaciones múltiples
            $metrics = DB::table('invoices')
                ->selectRaw('
                    COUNT(*) as total_transactions,
                    SUM(total) as total_sales,
                    AVG(total) as average_sale,
                    MAX(total) as highest_sale,
                    MIN(total) as lowest_sale
                ')
                ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
                ->where('status', '!=', 'cancelled')
                ->first();

            // Productos más vendidos en una consulta optimizada
            $topProducts = DB::table('invoice_items')
                ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('products', 'invoice_items.product_id', '=', 'products.id')
                ->selectRaw('
                    products.name,
                    SUM(invoice_items.quantity) as total_quantity,
                    SUM(invoice_items.subtotal) as total_revenue
                ')
                ->whereBetween('invoices.date', [$dateRange['start'], $dateRange['end']])
                ->where('invoices.status', '!=', 'cancelled')
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('total_quantity')
                ->limit(5)
                ->get();

            // Sesiones activas
            $activeSessions = DB::table('cash_sessions')
                ->join('users', 'cash_sessions.user_id', '=', 'users.id')
                ->select([
                    'cash_sessions.id',
                    'users.name as cashier',
                    'cash_sessions.opening_time',
                    'cash_sessions.total_sales',
                    'cash_sessions.status'
                ])
                ->where('cash_sessions.status', 'open')
                ->get();

            return response()->json([
                'success' => true,
                'metrics' => [
                    'total_sales' => $metrics->total_sales ?: 0,
                    'total_transactions' => $metrics->total_transactions ?: 0,
                    'average_sale' => round($metrics->average_sale ?: 0, 2),
                    'highest_sale' => $metrics->highest_sale ?: 0,
                    'lowest_sale' => $metrics->lowest_sale ?: 0,
                ],
                'top_products' => $topProducts,
                'active_sessions' => $activeSessions,
                'period' => $period,
                'cached_at' => now()->format('H:i:s')
            ]);
        });
    }

    /**
     * Limpiar cache manualmente
     */
    public function clearCache()
    {
        // Limpiar cache del dashboard
        Cache::flush(); // En producción, ser más específico

        return response()->json([
            'success' => true,
            'message' => 'Cache limpiado correctamente'
        ]);
    }

    /**
     * Construir datos del dashboard sin cache
     */
    private function buildDashboardData($period)
    {
        $dateRange = $this->getDateRange($period);

        // Consulta optimizada principal
        $mainStats = DB::table('invoices')
            ->selectRaw('
                COUNT(*) as total_transactions,
                SUM(total) as total_sales,
                AVG(total) as average_sale
            ')
            ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
            ->where('status', '!=', 'cancelled')
            ->first();

        // Sesiones activas
        $activeSessions = DB::table('cash_sessions')
            ->join('users', 'cash_sessions.user_id', '=', 'users.id')
            ->select([
                'cash_sessions.id',
                'users.name as cashier',
                'cash_sessions.opening_time',
                'cash_sessions.total_sales',
                'cash_sessions.status'
            ])
            ->where('cash_sessions.status', 'open')
            ->get();

        return response()->json([
            'success' => true,
            'total_sales' => $mainStats->total_sales ?: 0,
            'total_transactions' => $mainStats->total_transactions ?: 0,
            'average_sale' => round($mainStats->average_sale ?: 0, 2),
            'active_sessions' => $activeSessions,
            'period' => $period,
            'generated_at' => now()->format('H:i:s')
        ]);
    }

    /**
     * Obtener rango de fechas optimizado
     */
    private function getDateRange($period)
    {
        $now = Carbon::now();

        switch ($period) {
            case 'today':
                return [
                    'start' => $now->copy()->startOfDay()->toDateTimeString(),
                    'end' => $now->copy()->endOfDay()->toDateTimeString()
                ];
            case 'week':
                return [
                    'start' => $now->copy()->startOfWeek()->toDateTimeString(),
                    'end' => $now->copy()->endOfWeek()->toDateTimeString()
                ];
            case 'month':
                return [
                    'start' => $now->copy()->startOfMonth()->toDateTimeString(),
                    'end' => $now->copy()->endOfMonth()->toDateTimeString()
                ];
            case 'year':
                return [
                    'start' => $now->copy()->startOfYear()->toDateTimeString(),
                    'end' => $now->copy()->endOfYear()->toDateTimeString()
                ];
            default:
                return [
                    'start' => $now->copy()->startOfDay()->toDateTimeString(),
                    'end' => $now->copy()->endOfDay()->toDateTimeString()
                ];
        }
    }

    /**
     * Tiempo de cache según el período
     */
    private function getCacheTime($period)
    {
        switch ($period) {
            case 'today':
                return 120; // 2 minutos para datos del día actual
            case 'week':
                return 600; // 10 minutos para datos semanales
            case 'month':
                return 1800; // 30 minutos para datos mensuales
            default:
                return 60; // 1 minuto por defecto
        }
    }

    /**
     * 📊 DATOS FINANCIEROS COMPLETOS
     * Endpoint específico para el módulo de Reportes Financieros
     */
    public function getFinancialData(Request $request)
    {
        $period = $request->get('period', 'month');
        $cacheKey = "financial_data_{$period}_" . floor(time() / 300);

        return Cache::remember($cacheKey, 300, function () use ($period) {
            $dateRange = $this->getDateRange($period);

            // ═══════════════════════════════════════════════════════════════
            // 1. VENTAS POR MÉTODO DE PAGO
            // ═══════════════════════════════════════════════════════════════
            $salesByPayment = DB::table('invoices')
                ->selectRaw("
                    COALESCE(SUM(CASE WHEN payment_method = 'efectivo' THEN total ELSE 0 END), 0) as cash_sales,
                    COALESCE(SUM(CASE WHEN payment_method = 'tarjeta' THEN total ELSE 0 END), 0) as card_sales,
                    COALESCE(SUM(CASE WHEN payment_method = 'transferencia' THEN total ELSE 0 END), 0) as transfer_sales,
                    COALESCE(SUM(CASE WHEN payment_method = 'creditienda' THEN total ELSE 0 END), 0) as credit_sales,
                    COALESCE(SUM(total), 0) as total_sales,
                    COUNT(*) as total_transactions
                ")
                ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
                ->whereIn('status', ['paid', 'completed'])
                ->first();

            // ═══════════════════════════════════════════════════════════════
            // 2. DEVOLUCIONES
            // ═══════════════════════════════════════════════════════════════
            $returns = DB::table('invoices')
                ->selectRaw("
                    COALESCE(SUM(ABS(total)), 0) as total_returns,
                    COUNT(*) as returns_count
                ")
                ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
                ->where('type', 'return')
                ->first();

            // ═══════════════════════════════════════════════════════════════
            // 3. GASTOS POR CATEGORÍA
            // ═══════════════════════════════════════════════════════════════
            $expensesByCategory = DB::table('expenses')
                ->leftJoin('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
                ->selectRaw("
                    COALESCE(expense_categories.name, 'Sin categoría') as name,
                    COALESCE(expense_categories.color, '#6B7280') as color,
                    SUM(expenses.amount) as amount,
                    COUNT(*) as count
                ")
                ->whereBetween('expenses.date', [$dateRange['start'], $dateRange['end']])
                ->groupBy('expense_categories.id', 'expense_categories.name', 'expense_categories.color')
                ->orderByDesc('amount')
                ->get();

            $totalExpenses = $expensesByCategory->sum('amount');

            // ═══════════════════════════════════════════════════════════════
            // 4. CARTERA CREDITIENDA (Cuentas por Cobrar)
            // ═══════════════════════════════════════════════════════════════
            $creditData = DB::table('customers')
                ->selectRaw("
                    COALESCE(SUM(current_debt), 0) as total_receivables,
                    COUNT(CASE WHEN current_debt > 0 THEN 1 END) as customers_with_debt,
                    COALESCE(AVG(CASE WHEN current_debt > 0 THEN DATEDIFF(NOW(), debt_since) END), 0) as avg_days_overdue
                ")
                ->where('current_debt', '>', 0)
                ->first();

            // Abonos recibidos hoy
            $todayCreditPayments = 0;
            if (DB::getSchemaBuilder()->hasTable('credit_payments')) {
                $todayCreditPayments = DB::table('credit_payments')
                    ->whereDate('payment_date', Carbon::today())
                    ->sum('amount');
            }

            // ═══════════════════════════════════════════════════════════════
            // 5. VALOR DEL INVENTARIO (con soporte correcto para variantes)
            // ═══════════════════════════════════════════════════════════════
            // Productos simples
            $simpleInv = DB::table('products')
                ->where('active', true)
                ->where(function ($q) { $q->whereNull('product_type')->orWhere('product_type', 'simple'); })
                ->selectRaw('COALESCE(SUM(current_stock * cost_price), 0) as cost_value, COALESCE(SUM(current_stock * sale_price), 0) as sale_value')
                ->first();
            // Productos variables (por variante)
            $variableInv = DB::table('product_variants as pv')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->where('p.active', true)->where('p.product_type', 'variable')->where('pv.active', true)
                ->selectRaw('COALESCE(SUM(pv.stock * COALESCE(pv.cost_price, p.cost_price, 0)), 0) as cost_value, COALESCE(SUM(pv.stock * COALESCE(pv.price, p.sale_price, 0)), 0) as sale_value')
                ->first();
            $activeProductCount = DB::table('products')->where('active', true)->count();
            $inventory = (object) [
                'cost_value' => ($simpleInv->cost_value ?? 0) + ($variableInv->cost_value ?? 0),
                'sale_value' => ($simpleInv->sale_value ?? 0) + ($variableInv->sale_value ?? 0),
                'active_products' => $activeProductCount,
            ];

            // ═══════════════════════════════════════════════════════════════
            // 6. CÁLCULO DE MARGEN BRUTO
            // ═══════════════════════════════════════════════════════════════
            $grossProfit = DB::table('invoice_items')
                ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->selectRaw("
                    COALESCE(SUM(invoice_items.subtotal), 0) as total_revenue,
                    COALESCE(SUM(invoice_items.quantity * invoice_items.cost_price), 0) as total_cost
                ")
                ->whereBetween('invoices.date', [$dateRange['start'], $dateRange['end']])
                ->whereIn('invoices.status', ['paid', 'completed'])
                ->first();

            $grossMargin = $grossProfit->total_revenue > 0 
                ? round((($grossProfit->total_revenue - $grossProfit->total_cost) / $grossProfit->total_revenue) * 100, 1)
                : 0;

            // ═══════════════════════════════════════════════════════════════
            // 7. TRANSACCIONES RECIENTES (Ingresos y Gastos combinados)
            // ═══════════════════════════════════════════════════════════════
            // Últimas ventas
            $recentSales = DB::table('invoices')
                ->select([
                    'date',
                    DB::raw("'income' as type"),
                    'number as description',
                    'payment_method',
                    'total as amount'
                ])
                ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
                ->whereIn('status', ['paid', 'completed'])
                ->orderByDesc('date')
                ->limit(5)
                ->get()
                ->map(function($item) {
                    return [
                        'date' => $item->date,
                        'type' => $item->type,
                        'description' => 'Factura ' . $item->description,
                        'paymentMethod' => ucfirst($item->payment_method ?? 'N/A'),
                        'amount' => $item->amount
                    ];
                });

            // Últimos gastos
            $recentExpenses = DB::table('expenses')
                ->leftJoin('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
                ->select([
                    'expenses.date',
                    DB::raw("'expense' as type"),
                    'expenses.description',
                    'expenses.payment_method',
                    'expenses.amount'
                ])
                ->whereBetween('expenses.date', [$dateRange['start'], $dateRange['end']])
                ->orderByDesc('expenses.date')
                ->limit(5)
                ->get()
                ->map(function($item) {
                    return [
                        'date' => $item->date,
                        'type' => $item->type,
                        'description' => $item->description,
                        'paymentMethod' => ucfirst($item->payment_method ?? 'N/A'),
                        'amount' => $item->amount
                    ];
                });

            // Combinar y ordenar
            $recentTransactions = $recentSales->concat($recentExpenses)
                ->sortByDesc('date')
                ->values()
                ->take(10);

            // ═══════════════════════════════════════════════════════════════
            // 8. CÁLCULOS FINALES
            // ═══════════════════════════════════════════════════════════════
            $totalRevenue = $salesByPayment->total_sales ?? 0;
            $netProfit = $totalRevenue - $totalExpenses - ($returns->total_returns ?? 0);
            $profitMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 1) : 0;

            // Flujo de caja (solo efectivo)
            $cashInflow = $salesByPayment->cash_sales ?? 0;
            $cashExpenses = DB::table('expenses')
                ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
                ->where('payment_method', 'efectivo')
                ->sum('amount');
            $netCashFlow = $cashInflow - $cashExpenses;

            // ROI del inventario
            $inventoryCostValue = $inventory->cost_value ?? 0;
            $inventoryROI = $inventoryCostValue > 0 ? round(($netProfit / $inventoryCostValue) * 100, 1) : 0;
            $inventoryTurnover = $inventoryCostValue > 0 ? round(($grossProfit->total_cost / $inventoryCostValue), 2) : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    // KPIs Principales
                    'totalRevenue' => $totalRevenue,
                    'totalExpenses' => $totalExpenses,
                    'netProfit' => $netProfit,
                    'profitMargin' => $profitMargin,
                    'grossMargin' => $grossMargin,
                    'revenueGrowth' => 0, // TODO: Calcular vs período anterior
                    'expensesGrowth' => 0, // TODO: Calcular vs período anterior
                    'inventoryROI' => $inventoryROI,
                    'inventoryTurnover' => $inventoryTurnover,

                    // Desglose Ingresos
                    'cashSales' => $salesByPayment->cash_sales ?? 0,
                    'cardSales' => $salesByPayment->card_sales ?? 0,
                    'transferSales' => $salesByPayment->transfer_sales ?? 0,
                    'creditSales' => $salesByPayment->credit_sales ?? 0,
                    'totalTransactions' => $salesByPayment->total_transactions ?? 0,
                    'returns' => $returns->total_returns ?? 0,
                    'returnsCount' => $returns->returns_count ?? 0,

                    // Gastos
                    'expensesByCategory' => $expensesByCategory,

                    // Flujo de Caja
                    'cashInflow' => $cashInflow,
                    'cashOutflow' => $cashExpenses,
                    'netCashFlow' => $netCashFlow,

                    // Cartera CrediTienda
                    'totalReceivables' => $creditData->total_receivables ?? 0,
                    'customersWithDebt' => $creditData->customers_with_debt ?? 0,
                    'avgDaysOverdue' => round($creditData->avg_days_overdue ?? 0),
                    'todayCreditPayments' => $todayCreditPayments,

                    // Inventario
                    'inventoryCostValue' => $inventory->cost_value ?? 0,
                    'inventorySaleValue' => $inventory->sale_value ?? 0,
                    'activeProducts' => $inventory->active_products ?? 0,

                    // Transacciones
                    'recentTransactions' => $recentTransactions
                ],
                'period' => $period,
                'cached_at' => now()->format('H:i:s')
            ]);
        });
    }
}
