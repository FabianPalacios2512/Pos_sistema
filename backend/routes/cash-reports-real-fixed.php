<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Reportes de Caja API - DATOS REALES
|--------------------------------------------------------------------------
|
| Endpoints para reportes avanzados de caja usando datos reales
|
*/

function getCashReportDateRange($period) {
    // CORREGIDO: Usar zona horaria de Colombia para definir "hoy", "ayer", etc.
    $nowColombia = Carbon::now()->setTimezone('America/Bogota');

    switch ($period) {
        case 'today':
            // Hoy en Colombia: 00:00 a 23:59 hora Colombia
            // CORREGIDO: NO convertir a UTC, trabajar directamente en hora Colombia
            $startColombia = $nowColombia->copy()->startOfDay(); // 00:00 Colombia
            $endColombia = $nowColombia->copy()->endOfDay();     // 23:59 Colombia

            return [
                'start' => $startColombia->toDateTimeString(), // Mantener en timezone Colombia
                'end' => $endColombia->toDateTimeString()       // Mantener en timezone Colombia
            ];
        case 'yesterday':
            $yesterdayColombia = $nowColombia->copy()->subDay();
            $startColombia = $yesterdayColombia->copy()->startOfDay();
            $endColombia = $yesterdayColombia->copy()->endOfDay();

            return [
                'start' => $startColombia->toDateTimeString(), // Mantener en timezone Colombia
                'end' => $endColombia->toDateTimeString()       // Mantener en timezone Colombia
            ];
        case 'week':
            $startColombia = $nowColombia->copy()->startOfWeek();
            $endColombia = $nowColombia->copy()->endOfWeek();

            return [
                'start' => $startColombia->toDateTimeString(), // Mantener en timezone Colombia
                'end' => $endColombia->toDateTimeString()       // Mantener en timezone Colombia
            ];
        case 'last_week':
            $lastWeekColombia = $nowColombia->copy()->subWeek();
            $startColombia = $lastWeekColombia->copy()->startOfWeek();
            $endColombia = $lastWeekColombia->copy()->endOfWeek();

            return [
                'start' => $startColombia->toDateTimeString(),
                'end' => $endColombia->toDateTimeString()
            ];
        case 'month':
            $startColombia = $nowColombia->copy()->startOfMonth();
            $endColombia = $nowColombia->copy()->endOfMonth();

            return [
                'start' => $startColombia->toDateTimeString(),
                'end' => $endColombia->toDateTimeString()
            ];
        case 'last_month':
            $lastMonthColombia = $nowColombia->copy()->subMonth();
            $startColombia = $lastMonthColombia->copy()->startOfMonth();
            $endColombia = $lastMonthColombia->copy()->endOfMonth();

            return [
                'start' => $startColombia->toDateTimeString(),
                'end' => $endColombia->toDateTimeString()
            ];
        case 'year':
            $startColombia = $nowColombia->copy()->startOfYear();
            $endColombia = $nowColombia->copy()->endOfYear();

            return [
                'start' => $startColombia->toDateTimeString(),
                'end' => $endColombia->toDateTimeString()
            ];
        case 'custom':
            $customDate = request()->get('custom_date');
            $customEndDate = request()->get('custom_end_date');

            if (!$customDate) {
                // Si no hay fecha personalizada, usar hoy en Colombia
                $startColombia = $nowColombia->copy()->startOfDay();
                $endColombia = $nowColombia->copy()->endOfDay();

                return [
                    'start' => $startColombia->setTimezone('UTC')->toDateTimeString(),
                    'end' => $endColombia->setTimezone('UTC')->toDateTimeString()
                ];
            }

            // Interpretar fechas personalizadas en zona horaria de Colombia
            $startDate = Carbon::createFromFormat('Y-m-d', $customDate, 'America/Bogota')->startOfDay();
            $endDate = $customEndDate ?
                Carbon::createFromFormat('Y-m-d', $customEndDate, 'America/Bogota')->endOfDay() :
                $startDate->copy()->endOfDay();

            return [
                'start' => $startDate->setTimezone('UTC')->toDateTimeString(),
                'end' => $endDate->setTimezone('UTC')->toDateTimeString()
            ];
        default:
            // Por defecto usar hoy en Colombia
            $startColombia = $nowColombia->copy()->startOfDay();
            $endColombia = $nowColombia->copy()->endOfDay();

            return [
                'start' => $startColombia->setTimezone('UTC')->toDateTimeString(),
                'end' => $endColombia->setTimezone('UTC')->toDateTimeString()
            ];
    }
}

function getCashDashboardData() {
    try {
        $period = request()->get('period', 'today');
        $nowColombia = Carbon::now()->setTimezone('America/Bogota');

        // CORREGIDO: Para 'today', usar fecha actual de Colombia y convertir UTC
        if ($period === 'today') {
            $todayString = $nowColombia->format('Y-m-d');

            // Total de ventas de hoy (convirtiendo UTC a hora Colombia)
            $totalSales = DB::table('invoices')
                ->whereRaw('DATE(CONVERT_TZ(created_at, "+00:00", "-05:00")) = ?', [$todayString])
                ->where('status', 'paid')
                ->where('type', 'invoice')
                ->sum('total') ?: 0;

            // Total de transacciones de hoy
            $totalTransactions = DB::table('invoices')
                ->whereRaw('DATE(CONVERT_TZ(created_at, "+00:00", "-05:00")) = ?', [$todayString])
                ->where('status', 'paid')
                ->where('type', 'invoice')
                ->count();
        } else {
            // Para otros períodos, usar el rango de fechas
            $dateRange = getCashReportDateRange($period);

            $totalSales = DB::table('invoices')
                ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
                ->where('status', '!=', 'cancelled')
                ->sum('total') ?: 0;

            $totalTransactions = DB::table('invoices')
                ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
                ->where('status', '!=', 'cancelled')
                ->count();
        }

        // Promedio por transacción
        $averageSale = $totalTransactions > 0 ? round($totalSales / $totalTransactions, 2) : 0;

        // Sesiones activas
        $activeSessions = DB::table('cash_sessions')
            ->join('users', 'cash_sessions.user_id', '=', 'users.id')
            ->where('cash_sessions.status', 'open')
            ->select([
                'cash_sessions.id',
                'users.name as cashier',
                'cash_sessions.opening_time',
                'cash_sessions.total_sales',
                'cash_sessions.status'
            ])
            ->get();

        return response()->json([
            'success' => true,
            'total_sales' => $totalSales,
            'total_transactions' => $totalTransactions,
            'average_sale' => $averageSale,
            'active_sessions' => $activeSessions,
            'period' => $period
        ]);


    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

function getCashierComparison() {
    try {
        $period = request()->get('period', 'today');
        $dateRange = getCashReportDateRange($period);

        // Usar invoices con cash_sessions para obtener datos consistentes
        $cashiers = DB::table('invoices')
            ->join('cash_sessions', 'invoices.cash_session_id', '=', 'cash_sessions.id')
            ->join('users', 'cash_sessions.user_id', '=', 'users.id')
            ->whereBetween('invoices.date', [$dateRange['start'], $dateRange['end']])
            ->where('invoices.status', '!=', 'cancelled')
            ->groupBy('cash_sessions.user_id', 'users.name')
            ->select([
                'users.name',
                DB::raw('COUNT(*) as transactions'),
                DB::raw('SUM(invoices.total) as total_sales'),
                DB::raw('AVG(invoices.total) as average_sale'),
                DB::raw('ROUND((SUM(invoices.total) * 100.0 / (SELECT SUM(total) FROM invoices WHERE status != "cancelled" AND date BETWEEN "' . $dateRange['start'] . '" AND "' . $dateRange['end'] . '")), 2) as efficiency')
            ])
            ->orderBy('total_sales', 'desc')
            ->get()
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => $cashiers
        ]);    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

function getHourlyEfficiency() {
    try {
        $period = request()->get('period', 'today');
        $nowColombia = Carbon::now()->setTimezone('America/Bogota');

        // CORREGIDO: Para 'today', usar fecha de Colombia y el campo 'date'
        if ($period === 'today') {
            $todayString = $nowColombia->format('Y-m-d');

            // Obtener datos por hora del día usando el campo 'date' y la hora de 'created_at'
            $hourlyData = DB::table('invoices')
                ->whereDate('date', $todayString)
                ->where('status', 'paid')
                ->where('type', 'invoice')
                ->select([
                    DB::raw('HOUR(CONVERT_TZ(created_at, "+00:00", "-05:00")) as hour_colombia'),
                    DB::raw('COUNT(*) as transactions'),
                    DB::raw('SUM(total) as sales')
                ])
                ->groupBy(DB::raw('HOUR(CONVERT_TZ(created_at, "+00:00", "-05:00"))'))
                ->orderBy('hour_colombia')
                ->get();
        } else {
            // Para otros períodos, usar el rango de fechas original
            $dateRange = getCashReportDateRange($period);
            $hourlyData = DB::table('invoices')
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->where('status', 'paid')
                ->select([
                    DB::raw('HOUR(created_at) as hour_colombia'),
                    DB::raw('COUNT(*) as transactions'),
                    DB::raw('SUM(total) as sales')
                ])
                ->groupBy(DB::raw('HOUR(created_at)'))
                ->orderBy('hour_colombia')
                ->get();
        }

        // Crear array completo de 24 horas (0-23) con datos reales o ceros
        $fullDayData = [];
        for ($i = 0; $i < 24; $i++) {
            $hourData = $hourlyData->firstWhere('hour_colombia', $i);
            $fullDayData[] = [
                'hour' => sprintf('%02d:00', $i),
                'transactions' => $hourData ? (int) $hourData->transactions : 0,
                'sales' => $hourData ? (float) $hourData->sales : 0,
                'efficiency' => $hourData ? min(100, round(((int) $hourData->transactions) * 3, 0)) : 0 // Estimación de eficiencia
            ];
        }

        // Calcular hora pico
        $peakHour = '';
        $peakSales = 0;
        foreach ($fullDayData as $data) {
            if ($data['sales'] > $peakSales) {
                $peakSales = $data['sales'];
                $peakHour = $data['hour'];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'hourly_data' => $fullDayData, // Todas las 24 horas para gráfico continuo
                'hourly_data_filtered' => array_filter($fullDayData, function($data) { return $data['sales'] > 0; }), // Solo horas con actividad para tablas
                'peak_hour' => $peakHour,
                'peak_sales' => $peakSales,
                'timezone' => 'America/Bogota (UTC-5)',
                'total_sales' => array_sum(array_column($fullDayData, 'sales')),
                'total_transactions' => array_sum(array_column($fullDayData, 'transactions'))
            ]
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

function getPaymentMethodsAnalysis() {
    try {
        $period = request()->get('period', 'today');
        $dateRange = getCashReportDateRange($period);

        $paymentMethods = DB::table('invoices')
            ->leftJoin('payment_methods', 'invoices.payment_method', '=', 'payment_methods.code')
            ->whereBetween('invoices.date', [$dateRange['start'], $dateRange['end']])
            ->where('invoices.status', 'paid')
            ->where('invoices.payment_method', '!=', null) // Solo incluir facturas con método de pago
            ->select([
                'invoices.payment_method as method',
                'payment_methods.name as method_name',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(invoices.total) as amount'),
                DB::raw('ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM invoices WHERE status = "paid" AND payment_method IS NOT NULL AND date BETWEEN "' . $dateRange['start'] . '" AND "' . $dateRange['end'] . '")), 2) as percentage')
            ])
            ->groupBy('invoices.payment_method', 'payment_methods.name')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => $paymentMethods
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

function getTopSessions() {
    try {
        $period = request()->get('period', 'week');
        $limit = request()->get('limit', 5);
        $dateRange = getCashReportDateRange($period);

        // CORREGIDO: Buscar sesiones que tuvieron ventas en el período, no sesiones creadas en el período
        $topSessions = DB::table('cash_sessions')
            ->join('users', 'cash_sessions.user_id', '=', 'users.id')
            ->join('invoices', 'invoices.cash_session_id', '=', 'cash_sessions.id')
            ->whereBetween(DB::raw('CONVERT_TZ(invoices.created_at, "+00:00", "-05:00")'), [$dateRange['start'], $dateRange['end']])
            ->where('cash_sessions.status', '!=', 'cancelled')
            ->where('invoices.status', '!=', 'cancelled')
            ->select([
                'cash_sessions.id',
                'users.name as cashier',
                DB::raw('SUM(invoices.total) as sales'),
                DB::raw('COUNT(invoices.id) as transactions'),
                DB::raw('DATE(CONVERT_TZ(cash_sessions.created_at, "+00:00", "-05:00")) as date'),
                DB::raw('ROUND(TIMESTAMPDIFF(HOUR, cash_sessions.opening_time, COALESCE(cash_sessions.closing_time, NOW())), 1) as duration')
            ])
            ->groupBy('cash_sessions.id', 'users.name', 'cash_sessions.created_at', 'cash_sessions.opening_time', 'cash_sessions.closing_time')
            ->orderBy('sales', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => $topSessions
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

function getCashAlerts() {
    try {
        $alerts = [];
        $now = Carbon::now();

        // 1. Verificar actividad reciente (últimas 2 horas)
        $recentSales = DB::table('invoices')
            ->where('created_at', '>=', $now->copy()->subHours(2))
            ->where('status', 'paid')
            ->count();

        $recentTotal = DB::table('invoices')
            ->where('created_at', '>=', $now->copy()->subHours(2))
            ->where('status', 'paid')
            ->sum('total');

        if ($recentSales < 3) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Actividad Baja',
                'message' => "Pocas ventas en las últimas 2 horas ({$recentSales} transacciones)",
                'time' => $now->format('H:i')
            ];
        } elseif ($recentSales > 15) {
            $alerts[] = [
                'type' => 'success',
                'title' => 'Alto Rendimiento',
                'message' => "Excelente actividad: {$recentSales} ventas (₡" . number_format($recentTotal) . " en 2 horas)",
                'time' => $now->format('H:i')
            ];
        }

        // 2. Verificar sesiones de caja abiertas
        $openSessions = DB::table('cash_sessions')
            ->where('status', 'open')
            ->count();

        if ($openSessions > 3) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Múltiples Sesiones',
                'message' => "{$openSessions} sesiones de caja actualmente abiertas",
                'time' => $now->format('H:i')
            ];
        } elseif ($openSessions == 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Sin Sesiones Activas',
                'message' => "No hay sesiones de caja abiertas actualmente",
                'time' => $now->format('H:i')
            ];
        }

        // 3. Verificar ventas del día vs promedio
        $todaySales = DB::table('invoices')
            ->whereDate('date', $now->toDateString())
            ->where('status', 'paid')
            ->sum('total');

        // Calcular promedio de ventas diarias de la última semana
        $avgDailySales = DB::select("
            SELECT AVG(daily_total) as avg_total
            FROM (
                SELECT DATE(date) as day, SUM(total) as daily_total
                FROM invoices
                WHERE status = 'paid'
                AND date >= ?
                GROUP BY DATE(date)
            ) as daily_sales
        ", [$now->copy()->subDays(7)->toDateString()]);

        $avgDailySales = $avgDailySales[0]->avg_total ?? 0;

        if ($todaySales > $avgDailySales * 1.2) {
            $alerts[] = [
                'type' => 'success',
                'title' => 'Día Excepcional',
                'message' => "Ventas del día superan el promedio en " . number_format((($todaySales / $avgDailySales) - 1) * 100, 1) . "%",
                'time' => $now->format('H:i')
            ];
        }

        // 4. Estado del sistema (siempre mostrar)
        $systemStatus = [
            'type' => 'info',
            'title' => 'Sistema Operativo',
            'message' => 'Todos los sistemas funcionando correctamente',
            'time' => $now->format('H:i')
        ];

        // Si no hay otras alertas, agregar estado del sistema
        if (empty($alerts)) {
            $alerts[] = $systemStatus;
        } else {
            // Agregar estado del sistema al final
            $alerts[] = $systemStatus;
        }

        return response()->json([
            'success' => true,
            'data' => $alerts
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

function getDailySales() {
    try {
        $period = request()->get('period', 'week');
        $dateRange = getCashReportDateRange($period);

        // Obtener ventas reales por día desde la base de datos
        $dailyData = DB::table('invoices')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('status', 'paid')
            ->select([
                DB::raw('DATE(CONVERT_TZ(created_at, "+00:00", "-05:00")) as sale_date'),
                DB::raw('COUNT(*) as transactions'),
                DB::raw('SUM(total) as sales')
            ])
            ->groupBy(DB::raw('DATE(CONVERT_TZ(created_at, "+00:00", "-05:00"))'))
            ->orderBy('sale_date')
            ->get();

        // Para período semanal, asegurar que tenemos los últimos 7 días
        if ($period === 'week') {
            $today = Carbon::now();
            $weekData = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = $today->copy()->subDays($i)->format('Y-m-d');
                $dayData = $dailyData->firstWhere('sale_date', $date);

                $weekData[] = [
                    'date' => $date,
                    'formatted_date' => $today->copy()->subDays($i)->format('d/m/Y'),
                    'day_name' => $today->copy()->subDays($i)->locale('es')->dayName,
                    'transactions' => $dayData ? (int) $dayData->transactions : 0,
                    'sales' => $dayData ? (float) $dayData->sales : 0
                ];
            }

            $dailyData = collect($weekData);
        }

        // Calcular totales y estadísticas
        $totalSales = $dailyData->sum('sales');
        $totalTransactions = $dailyData->sum('transactions');
        $averageDaily = $dailyData->count() > 0 ? $totalSales / $dailyData->count() : 0;
        $bestDay = $dailyData->sortByDesc('sales')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'daily_sales' => $dailyData->values(),
                'daily_sales_array' => $dailyData->pluck('sales')->values(), // Para gráficos
                'daily_labels' => $dailyData->pluck('formatted_date')->values(), // Etiquetas para gráficos
                'summary' => [
                    'total_sales' => $totalSales,
                    'total_transactions' => $totalTransactions,
                    'average_daily' => $averageDaily,
                    'best_day' => $bestDay,
                    'period' => $period,
                    'date_range' => $dateRange
                ]
            ]
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

function getLowStockProducts() {
    try {
        $products = DB::table('products')
            ->where('active', 1)
            ->where('manage_stock', 1)
            ->whereRaw('current_stock <= min_stock')
            ->orderBy('current_stock', 'ASC')
            ->limit(8)
            ->select('id', 'name', 'current_stock', 'min_stock', 'sale_price')
            ->get();

        // Si no hay productos críticos, obtener los 5 con menor stock
        if ($products->isEmpty()) {
            $products = DB::table('products')
                ->where('active', 1)
                ->where('manage_stock', 1)
                ->orderBy('current_stock', 'ASC')
                ->limit(5)
                ->select('id', 'name', 'current_stock', 'min_stock', 'sale_price')
                ->get();
        }

        $formattedProducts = $products->map(function($product) {
            // Asignar color basado en criticidad del stock
            $ratio = $product->min_stock > 0 ? ($product->current_stock / $product->min_stock) : 1;

            if ($ratio <= 0.2) $color = '#dc2626'; // rojo crítico
            elseif ($ratio <= 0.5) $color = '#ef4444'; // rojo
            elseif ($ratio <= 0.8) $color = '#f59e0b'; // amarillo
            else $color = '#f97316'; // naranja

            return [
                'id' => $product->id,
                'name' => $product->name,
                'quantity' => $product->current_stock,
                'min_stock' => $product->min_stock,
                'price' => $product->sale_price,
                'color' => $color,
                'critical' => $product->current_stock <= $product->min_stock
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedProducts
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

// NUEVA FUNCIÓN: Obtener datos estructurados para gráficos de tendencia
function getTrendChartData() {
    try {
        $period = request()->get('period', 'week');

        switch ($period) {
            case 'week':
                return getWeeklySalesData();
            case 'month':
                return getMonthlySalesData();
            case 'year':
                return getYearlySalesData();
            default:
                return response()->json([
                    'success' => false,
                    'error' => 'Período no válido. Use: week, month, year'
                ], 400);
        }

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

// Obtener ventas de los últimos 7 días
function getWeeklySalesData() {
    $nowColombia = Carbon::now()->setTimezone('America/Bogota');

    // Últimos 7 días completos (incluyendo hoy)
    $salesData = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = $nowColombia->copy()->subDays($i);
        $dateString = $date->format('Y-m-d');

        // CORREGIDO: Usar CONVERT_TZ para convertir UTC a hora Colombia
        $dayData = DB::table('invoices')
            ->whereRaw('DATE(CONVERT_TZ(created_at, "+00:00", "-05:00")) = ?', [$dateString])
            ->where('status', 'paid')
            ->where('type', 'invoice')
            ->select([
                DB::raw('COUNT(*) as transactions'),
                DB::raw('SUM(total) as sales')
            ])
            ->first();

        $dayName = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'][$date->dayOfWeek];

        // CORREGIDO: Asegurar tipos correctos y manejar valores null
        $transactions = $dayData && $dayData->transactions ? (int) $dayData->transactions : 0;
        $sales = $dayData && $dayData->sales ? (float) $dayData->sales : 0.0;

        $salesData[] = [
            'label' => $dayName,
            'date' => $date->format('Y-m-d'),
            'transactions' => $transactions,
            'sales' => $sales
        ];
    }

    return response()->json([
        'success' => true,
        'period' => 'week',
        'data' => [
            'chart_data' => $salesData,
            'total_sales' => array_sum(array_column($salesData, 'sales')),
            'total_transactions' => array_sum(array_column($salesData, 'transactions')),
            'peak_day' => $salesData[array_search(max(array_column($salesData, 'sales')), array_column($salesData, 'sales'))]['label'] ?? 'N/A'
        ]
    ]);
}

// Obtener ventas de los últimos 30 días
function getMonthlySalesData() {
    $nowColombia = Carbon::now()->setTimezone('America/Bogota');

    // Últimos 30 días completos
    $salesData = [];
    for ($i = 29; $i >= 0; $i--) {
        $date = $nowColombia->copy()->subDays($i);
        $dateString = $date->format('Y-m-d');

        // CORREGIDO: Usar CONVERT_TZ para convertir UTC a hora Colombia
        $dayData = DB::table('invoices')
            ->whereRaw('DATE(CONVERT_TZ(created_at, "+00:00", "-05:00")) = ?', [$dateString])
            ->where('status', 'paid')
            ->where('type', 'invoice')
            ->select([
                DB::raw('COUNT(*) as transactions'),
                DB::raw('SUM(total) as sales')
            ])
            ->first();

        // Solo incluir días con ventas para evitar el gráfico lleno de ceros
        if ($dayData && $dayData->sales > 0) {
            $salesData[] = [
                'label' => $date->format('d'),
                'date' => $date->format('Y-m-d'),
                'transactions' => (int) $dayData->transactions,
                'sales' => (float) $dayData->sales
            ];
        }
    }

    return response()->json([
        'success' => true,
        'period' => 'month',
        'data' => [
            'chart_data' => $salesData,
            'total_sales' => array_sum(array_column($salesData, 'sales')),
            'total_transactions' => array_sum(array_column($salesData, 'transactions')),
            'peak_day' => count($salesData) > 0 ? $salesData[array_search(max(array_column($salesData, 'sales')), array_column($salesData, 'sales'))]['label'] : 'N/A'
        ]
    ]);
}

// Obtener ventas de los últimos 12 meses
function getYearlySalesData() {
    $nowColombia = Carbon::now()->setTimezone('America/Bogota');

    $salesData = [];
    for ($i = 11; $i >= 0; $i--) {
        $monthDate = $nowColombia->copy()->subMonths($i);
        $monthStart = $monthDate->copy()->startOfMonth()->toDateTimeString();
        $monthEnd = $monthDate->copy()->endOfMonth()->toDateTimeString();

        $monthData = DB::table('invoices')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 'paid')
            ->select([
                DB::raw('COUNT(*) as transactions'),
                DB::raw('SUM(total) as sales')
            ])
            ->first();

        $monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        $salesData[] = [
            'label' => $monthNames[$monthDate->month - 1],
            'date' => $monthDate->format('Y-m'),
            'transactions' => $monthData ? (int) $monthData->transactions : 0,
            'sales' => $monthData ? (float) $monthData->sales : 0
        ];
    }

    return response()->json([
        'success' => true,
        'period' => 'year',
        'data' => [
            'chart_data' => $salesData,
            'total_sales' => array_sum(array_column($salesData, 'sales')),
            'total_transactions' => array_sum(array_column($salesData, 'transactions')),
            'peak_month' => count($salesData) > 0 ? $salesData[array_search(max(array_column($salesData, 'sales')), array_column($salesData, 'sales'))]['label'] : 'N/A'
        ]
    ]);
}
