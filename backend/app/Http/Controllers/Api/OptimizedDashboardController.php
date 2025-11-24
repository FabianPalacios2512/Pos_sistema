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
                    'customer_name' => $customers[$transaction->customer_id] ?? 'Cliente General',
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
                    'start' => $now->startOfDay()->toDateTimeString(),
                    'end' => $now->endOfDay()->toDateTimeString()
                ];
            case 'week':
                return [
                    'start' => $now->startOfWeek()->toDateTimeString(),
                    'end' => $now->endOfWeek()->toDateTimeString()
                ];
            case 'month':
                return [
                    'start' => $now->startOfMonth()->toDateTimeString(),
                    'end' => $now->endOfMonth()->toDateTimeString()
                ];
            default:
                return [
                    'start' => $now->startOfDay()->toDateTimeString(),
                    'end' => $now->endOfDay()->toDateTimeString()
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
}
