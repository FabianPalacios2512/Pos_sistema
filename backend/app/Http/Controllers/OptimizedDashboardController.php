<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class OptimizedDashboardController extends Controller
{
    /**
     * Obtener todos los datos del dashboard con cache inteligente
     */
    public function getDashboardData(Request $request)
    {
        $cacheKey = 'dashboard_data_' . now()->format('Y_m_d_H_i');
        $cacheMinutes = 2; // Cache por 2 minutos

        return Cache::remember($cacheKey, $cacheMinutes * 60, function () {
            return [
                'metrics' => $this->getMainMetrics(),
                'recent_transactions' => $this->getRecentTransactions(10),
                'top_products' => $this->getTopProducts(5),
                'sales_today' => $this->getSalesToday(),
                'cache_timestamp' => now()->toDateTimeString()
            ];
        });
    }

    /**
     * Métricas principales optimizadas
     */
    public function getMainMetrics()
    {
        $cacheKey = 'main_metrics_' . now()->format('Y_m_d_H');

        return Cache::remember($cacheKey, 3600, function () { // 1 hora de cache
            // Una sola query para todas las métricas del día
            $today = now()->format('Y-m-d');

            $metrics = DB::select("
                SELECT
                    COUNT(CASE WHEN DATE(i.date) = ? THEN 1 END) as invoices_today,
                    SUM(CASE WHEN DATE(i.date) = ? THEN i.total ELSE 0 END) as sales_today,
                    SUM(CASE WHEN DATE(i.date) = ? THEN 1 ELSE 0 END) as transactions_today,
                    COUNT(CASE WHEN i.status = 'paid' THEN 1 END) as paid_invoices,
                    (SELECT COUNT(*) FROM products WHERE active = 1) as active_products,
                    (SELECT COUNT(*) FROM customers WHERE active = 1) as active_customers,
                    (SELECT COUNT(*) FROM cash_sessions WHERE status = 'open') as open_sessions
                FROM invoices i
            ", [$today, $today, $today]);

            return $metrics[0] ?? (object) [
                'invoices_today' => 0,
                'sales_today' => 0,
                'transactions_today' => 0,
                'paid_invoices' => 0,
                'active_products' => 0,
                'active_customers' => 0,
                'open_sessions' => 0
            ];
        });
    }

    /**
     * Transacciones recientes optimizadas
     */
    public function getRecentTransactions($limit = 10)
    {
        $cacheKey = "recent_transactions_{$limit}_" . now()->format('Y_m_d_H_i');

        return Cache::remember($cacheKey, 300, function () use ($limit) { // 5 minutos
            return DB::table('invoices as i')
                ->leftJoin('customers as c', 'i.customer_id', '=', 'c.id')
                ->leftJoin('users as u', 'i.user_id', '=', 'u.id')
                ->select([
                    'i.id',
                    'i.invoice_number',
                    'i.total',
                    'i.status',
                    'i.date',
                    'i.payment_method',
                    'c.first_name as customer_name',
                    'c.last_name as customer_lastname',
                    'u.name as user_name'
                ])
                ->orderBy('i.created_at', 'DESC')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Productos más vendidos optimizados
     */
    public function getTopProducts($limit = 5)
    {
        $cacheKey = "top_products_{$limit}_" . now()->format('Y_m_d');

        return Cache::remember($cacheKey, 1800, function () use ($limit) { // 30 minutos
            return DB::table('invoice_items as ii')
                ->join('invoices as i', 'ii.invoice_id', '=', 'i.id')
                ->where('i.status', 'paid')
                ->where('i.date', '>=', now()->subDays(7)) // Últimos 7 días
                ->groupBy('ii.product_id', 'ii.product_name')
                ->select([
                    'ii.product_id',
                    'ii.product_name',
                    DB::raw('SUM(ii.quantity) as total_quantity'),
                    DB::raw('SUM(ii.quantity * ii.unit_price) as total_revenue'),
                    DB::raw('COUNT(DISTINCT i.id) as invoice_count')
                ])
                ->orderBy('total_quantity', 'DESC')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Ventas de hoy optimizadas
     */
    public function getSalesToday()
    {
        $cacheKey = 'sales_today_' . now()->format('Y_m_d_H');

        return Cache::remember($cacheKey, 1800, function () { // 30 minutos
            $today = now()->format('Y-m-d');

            return DB::table('invoices')
                ->where('date', $today)
                ->where('status', 'paid')
                ->select([
                    DB::raw('COUNT(*) as total_invoices'),
                    DB::raw('SUM(total) as total_amount'),
                    DB::raw('AVG(total) as average_sale'),
                    DB::raw('MAX(total) as highest_sale'),
                    DB::raw('MIN(total) as lowest_sale')
                ])
                ->first();
        });
    }

    /**
     * Limpiar cache del dashboard
     */
    public function clearCache()
    {
        $patterns = [
            'dashboard_data',
            'main_metrics',
            'recent_transactions',
            'top_products',
            'sales_today'
        ];

        $cleared = 0;

        // Para cache de archivos, limpiamos por prefijo
        try {
            foreach ($patterns as $pattern) {
                // Buscar claves que contengan el patrón
                $cacheKeys = [];

                // Generar algunas claves probables basadas en tiempo actual y reciente
                $now = now();
                for ($i = 0; $i < 24; $i++) { // Últimas 24 horas
                    $time = $now->copy()->subHours($i);
                    $cacheKeys[] = $pattern . '_' . $time->format('Y_m_d_H');
                    $cacheKeys[] = $pattern . '_' . $time->format('Y_m_d_H_i');
                    $cacheKeys[] = $pattern . '_' . $time->format('Y_m_d');
                }

                // Agregar claves específicas
                if ($pattern === 'recent_transactions') {
                    foreach ([5, 10, 20] as $limit) {
                        foreach ($cacheKeys as $baseKey) {
                            $cacheKeys[] = $baseKey . "_{$limit}";
                        }
                    }
                }

                if ($pattern === 'top_products') {
                    foreach ([5, 10, 15] as $limit) {
                        foreach ($cacheKeys as $baseKey) {
                            $cacheKeys[] = $baseKey . "_{$limit}";
                        }
                    }
                }

                // Limpiar las claves
                foreach ($cacheKeys as $key) {
                    if (Cache::has($key)) {
                        Cache::forget($key);
                        $cleared++;
                    }
                }
            }
        } catch (\Exception $e) {
            // Si falla, limpiamos todo el cache
            Cache::flush();
            $cleared = 'all';
        }

        return response()->json([
            'message' => 'Cache limpiado exitosamente',
            'keys_cleared' => $cleared,
            'cache_driver' => config('cache.default'),
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * Estado del cache
     */
    public function getCacheStatus()
    {
        $cacheKeys = [
            'dashboard_data_' . now()->format('Y_m_d_H_i'),
            'main_metrics_' . now()->format('Y_m_d_H'),
            'recent_transactions_10_' . now()->format('Y_m_d_H_i'),
            'top_products_5_' . now()->format('Y_m_d'),
            'sales_today_' . now()->format('Y_m_d_H')
        ];

        $status = [];
        foreach ($cacheKeys as $key) {
            $status[$key] = Cache::has($key) ? 'cached' : 'not_cached';
        }

        return response()->json([
            'cache_status' => $status,
            'cache_driver' => config('cache.default'),
            'timestamp' => now()->toDateTimeString()
        ]);
    }
}
