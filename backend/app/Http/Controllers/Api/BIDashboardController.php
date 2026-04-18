<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class BIDashboardController extends Controller
{
    /**
     * Single endpoint: GET /bi/dashboard?warehouse_id=&date_from=&date_to=
     * Returns ALL BI dashboard data in one call.
     */
    public function index(Request $request)
    {
        $warehouseId = $request->input('warehouse_id'); // null = todas
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::now()->format('Y-m-d'));

        // Previous period for growth calculations (same duration, shifted back)
        $periodDays = Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)) + 1;
        $prevFrom = Carbon::parse($dateFrom)->subDays($periodDays)->format('Y-m-d');
        $prevTo = Carbon::parse($dateFrom)->subDay()->format('Y-m-d');

        try {
            $data = [
                'kpis'               => $this->getKPIs($warehouseId, $dateFrom, $dateTo, $prevFrom, $prevTo),
                'sales_vs_expenses'  => $this->getSalesVsExpenses($warehouseId, $dateFrom, $dateTo),
                'warehouse_ranking'  => $this->getWarehouseRanking($dateFrom, $dateTo),
                'dead_stock'         => $this->getDeadStock($warehouseId, 30),
                'critical_stock'     => $this->getCriticalStock($warehouseId),
                'top_products'       => $this->getTopProducts($warehouseId, $dateFrom, $dateTo),
                'payment_breakdown'  => $this->getPaymentBreakdown($warehouseId, $dateFrom, $dateTo),
                'recent_transactions'=> $this->getRecentTransactions($warehouseId, 8),
                'filters' => [
                    'warehouses' => $this->getWarehouses(),
                    'date_from'  => $dateFrom,
                    'date_to'    => $dateTo,
                    'warehouse_id' => $warehouseId,
                ],
            ];

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading BI dashboard',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ──────────────────────────────────────────────
    // KPIs: margin, ticket, inventory value, revenue, expenses
    // ──────────────────────────────────────────────
    private function getKPIs($warehouseId, $dateFrom, $dateTo, $prevFrom, $prevTo)
    {
        // Current period revenue & cost
        $current = $this->periodFinancials($warehouseId, $dateFrom, $dateTo);
        $previous = $this->periodFinancials($warehouseId, $prevFrom, $prevTo);

        // Inventory value
        $inventoryValue = $this->inventoryValue($warehouseId);

        // Margin
        $grossRevenue = (float) $current->total_revenue;
        $grossCost    = (float) $current->total_cost;
        $margin = $grossRevenue > 0
            ? round((($grossRevenue - $grossCost) / $grossRevenue) * 100, 1)
            : 0;

        // Ticket
        $avgTicket = $current->tx_count > 0
            ? round($grossRevenue / $current->tx_count, 0)
            : 0;
        $prevAvgTicket = $previous->tx_count > 0
            ? round((float) $previous->total_revenue / $previous->tx_count, 0)
            : 0;
        $ticketGrowth = $prevAvgTicket > 0
            ? round((($avgTicket - $prevAvgTicket) / $prevAvgTicket) * 100, 1)
            : 0;

        // Revenue growth
        $revenueGrowth = (float) $previous->total_revenue > 0
            ? round((($grossRevenue - (float) $previous->total_revenue) / (float) $previous->total_revenue) * 100, 1)
            : 0;

        // Expenses
        $totalExpenses = $this->periodExpenses($warehouseId, $dateFrom, $dateTo);
        $prevExpenses  = $this->periodExpenses($warehouseId, $prevFrom, $prevTo);
        $expenseGrowth = $prevExpenses > 0
            ? round((($totalExpenses - $prevExpenses) / $prevExpenses) * 100, 1)
            : 0;

        // Net profit
        $netProfit = $grossRevenue - $grossCost - $totalExpenses;

        return [
            'total_revenue'    => $grossRevenue,
            'revenue_growth'   => $revenueGrowth,
            'total_cost'       => $grossCost,
            'gross_margin'     => $margin,
            'avg_ticket'       => $avgTicket,
            'ticket_growth'    => $ticketGrowth,
            'tx_count'         => (int) $current->tx_count,
            'inventory_value'  => $inventoryValue,
            'total_expenses'   => $totalExpenses,
            'expense_growth'   => $expenseGrowth,
            'net_profit'       => $netProfit,
        ];
    }

    private function periodFinancials($warehouseId, $from, $to)
    {
        $q = DB::table('invoices as i')
            ->join('invoice_items as ii', 'i.id', '=', 'ii.invoice_id')
            ->whereBetween('i.date', [$from, $to])
            ->whereIn('i.status', ['paid', 'completed']);

        if ($warehouseId) {
            $q->whereIn('i.cash_session_id', function ($sub) use ($warehouseId) {
                $sub->select('id')->from('cash_sessions')->where('warehouse_id', $warehouseId);
            });
        }

        return $q->selectRaw('
            COALESCE(SUM(ii.quantity * ii.unit_price), 0) as total_revenue,
            COALESCE(SUM(ii.quantity * COALESCE(ii.cost_price, 0)), 0) as total_cost,
            COUNT(DISTINCT i.id) as tx_count
        ')->first();
    }

    private function periodExpenses($warehouseId, $from, $to)
    {
        // expenses table (egresos manuales)
        $q = DB::table('expenses')
            ->whereBetween(DB::raw('DATE(date)'), [$from, $to]);

        if ($warehouseId) {
            $q->where(function ($sub) use ($warehouseId) {
                $sub->whereNull('cash_session_id')
                    ->orWhereIn('cash_session_id', function ($s) use ($warehouseId) {
                        $s->select('id')->from('cash_sessions')->where('warehouse_id', $warehouseId);
                    });
            });
        }

        $expensesTotal = (float) $q->sum('amount');

        // cash_movements (egresos desde Movimiento de Caja)
        $movQ = DB::table('cash_movements')
            ->where('type', 'egreso')
            ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);

        if ($warehouseId) {
            $movQ->whereIn('cash_session_id', function ($sub) use ($warehouseId) {
                $sub->select('id')->from('cash_sessions')->where('warehouse_id', $warehouseId);
            });
        }

        $movementsTotal = (float) $movQ->sum('amount');

        return $expensesTotal + $movementsTotal;
    }

    private function inventoryValue($warehouseId)
    {
        if ($warehouseId) {
            // Stock from product_warehouse for that specific warehouse
            return (float) DB::table('product_warehouse as pw')
                ->join('products as p', 'pw.product_id', '=', 'p.id')
                ->where('pw.warehouse_id', $warehouseId)
                ->where('p.active', true)
                ->selectRaw('COALESCE(SUM(pw.stock * p.cost_price), 0) as val')
                ->value('val');
        }

        // Simple products
        $simple = (float) DB::table('products')
            ->where('active', true)
            ->where(function ($q) {
                $q->whereNull('product_type')->orWhere('product_type', 'simple');
            })
            ->selectRaw('COALESCE(SUM(current_stock * cost_price), 0) as val')
            ->value('val');

        // Variable products: use variant cost_price * variant stock
        $variable = (float) DB::table('product_variants as pv')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->where('p.active', true)
            ->where('p.product_type', 'variable')
            ->where('pv.active', true)
            ->selectRaw('COALESCE(SUM(pv.stock * COALESCE(pv.cost_price, p.cost_price, 0)), 0) as val')
            ->value('val');

        return $simple + $variable;
    }

    // ──────────────────────────────────────────────
    // Sales vs Expenses by day (chart)
    // ──────────────────────────────────────────────
    private function getSalesVsExpenses($warehouseId, $dateFrom, $dateTo)
    {
        // Daily sales
        $salesQ = DB::table('invoices as i')
            ->whereBetween('i.date', [$dateFrom, $dateTo])
            ->whereIn('i.status', ['paid', 'completed']);

        if ($warehouseId) {
            $salesQ->whereIn('i.cash_session_id', function ($sub) use ($warehouseId) {
                $sub->select('id')->from('cash_sessions')->where('warehouse_id', $warehouseId);
            });
        }

        $dailySales = $salesQ->selectRaw('DATE(i.date) as day, COALESCE(SUM(i.total), 0) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->toArray();

        // Daily expenses from expenses table
        $expQ = DB::table('expenses')
            ->whereBetween(DB::raw('DATE(date)'), [$dateFrom, $dateTo]);

        if ($warehouseId) {
            $expQ->where(function ($sub) use ($warehouseId) {
                $sub->whereNull('cash_session_id')
                    ->orWhereIn('cash_session_id', function ($s) use ($warehouseId) {
                        $s->select('id')->from('cash_sessions')->where('warehouse_id', $warehouseId);
                    });
            });
        }

        $dailyExpenses = $expQ->selectRaw('DATE(date) as day, COALESCE(SUM(amount), 0) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->toArray();

        // Daily egresos from cash_movements (Movimiento de Caja)
        $movQ = DB::table('cash_movements')
            ->where('type', 'egreso')
            ->whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo]);

        if ($warehouseId) {
            $movQ->whereIn('cash_session_id', function ($sub) use ($warehouseId) {
                $sub->select('id')->from('cash_sessions')->where('warehouse_id', $warehouseId);
            });
        }

        $dailyMovements = $movQ->selectRaw('DATE(created_at) as day, COALESCE(SUM(amount), 0) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->toArray();

        // Build full range
        $result = [];
        $current = Carbon::parse($dateFrom);
        $end = Carbon::parse($dateTo);
        while ($current->lte($end)) {
            $d = $current->format('Y-m-d');
            $result[] = [
                'date'     => $d,
                'label'    => $current->format('d M'),
                'sales'    => (float) ($dailySales[$d] ?? 0),
                'expenses' => (float) ($dailyExpenses[$d] ?? 0) + (float) ($dailyMovements[$d] ?? 0),
            ];
            $current->addDay();
        }

        return $result;
    }

    // ──────────────────────────────────────────────
    // Warehouse ranking
    // ──────────────────────────────────────────────
    private function getWarehouseRanking($dateFrom, $dateTo)
    {
        return DB::table('warehouses as w')
            ->leftJoin('cash_sessions as cs', function ($join) {
                $join->on('w.id', '=', 'cs.warehouse_id');
            })
            ->leftJoin('invoices as i', function ($join) use ($dateFrom, $dateTo) {
                $join->on('cs.id', '=', 'i.cash_session_id')
                     ->whereBetween('i.date', [$dateFrom, $dateTo])
                     ->whereIn('i.status', ['paid', 'completed']);
            })
            ->where('w.active', true)
            ->groupBy('w.id', 'w.name')
            ->selectRaw('
                w.id,
                w.name,
                COALESCE(SUM(i.total), 0) as revenue,
                COUNT(DISTINCT i.id) as tx_count
            ')
            ->orderByDesc('revenue')
            ->get()
            ->toArray();
    }

    // ──────────────────────────────────────────────
    // Dead stock: products with no movement in X days
    // ──────────────────────────────────────────────
    private function getDeadStock($warehouseId, $days = 30)
    {
        $cutoff = Carbon::now()->subDays($days)->format('Y-m-d');

        // Products that have stock > 0 but NO sales since $cutoff
        $q = DB::table('products as p')
            ->leftJoin('invoice_items as ii', function ($join) use ($cutoff) {
                $join->on('p.id', '=', 'ii.product_id')
                     ->whereExists(function ($sub) use ($cutoff) {
                         $sub->select(DB::raw(1))
                              ->from('invoices as inv')
                              ->whereColumn('inv.id', 'ii.invoice_id')
                              ->where('inv.date', '>=', $cutoff)
                              ->whereIn('inv.status', ['paid', 'completed']);
                     });
            })
            ->where('p.active', true)
            ->where('p.current_stock', '>', 0)
            ->whereNull('ii.id') // No recent sales
            ->select('p.id', 'p.name', 'p.sku', 'p.current_stock', 'p.cost_price', 'p.sale_price', 'p.image_url')
            ->selectRaw('p.current_stock * p.cost_price as capital_locked')
            ->orderByDesc('capital_locked')
            ->limit(10)
            ->get();

        // Also get last sale date for context
        return $q->map(function ($p) {
            $lastSale = DB::table('invoice_items as ii')
                ->join('invoices as inv', 'ii.invoice_id', '=', 'inv.id')
                ->where('ii.product_id', $p->id)
                ->whereIn('inv.status', ['paid', 'completed'])
                ->max('inv.date');

            $p->last_sale_date = $lastSale;
            $p->days_without_sale = $lastSale
                ? Carbon::parse($lastSale)->diffInDays(Carbon::now())
                : null;
            return $p;
        })->toArray();
    }

    // ──────────────────────────────────────────────
    // Critical stock (variant-aware)
    // ──────────────────────────────────────────────
    private function getCriticalStock($warehouseId)
    {
        // Simple products with low stock
        $simpleQ = DB::table('products as p')
            ->where('p.active', true)
            ->where('p.manage_stock', true)
            ->where(function ($q) {
                $q->whereNull('p.product_type')->orWhere('p.product_type', 'simple');
            })
            ->whereRaw('p.current_stock <= p.min_stock')
            ->select(
                'p.id', 'p.name', 'p.sku', 'p.image_url',
                'p.current_stock as stock', 'p.min_stock',
                DB::raw("NULL as variant_label"),
                DB::raw("'simple' as type")
            )
            ->orderBy('p.current_stock')
            ->limit(10);

        // Variant products with low stock
        $variants = DB::table('product_variants as pv')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->where('p.active', true)
            ->where('p.manage_stock', true)
            ->where('p.product_type', 'variable')
            ->where('pv.active', true)
            ->whereRaw('pv.stock <= p.min_stock')
            ->select(
                'p.id', 'p.name', 'pv.sku', 'p.image_url',
                'pv.stock as stock', 'p.min_stock',
                'pv.options_summary as variant_label',
                DB::raw("'variant' as type")
            )
            ->orderBy('pv.stock')
            ->limit(10);

        return $simpleQ->unionAll($variants)
            ->orderBy('stock')
            ->limit(15)
            ->get()
            ->map(function ($item) {
                if ($item->type === 'variant' && $item->variant_label) {
                    try {
                        $opts = json_decode($item->variant_label, true);
                        if (is_array($opts)) {
                            $item->variant_label = implode(' / ', array_map(function ($o) {
                                $val = $o['value'] ?? '';
                                // Replace hex colors with a readable symbol
                                if (preg_match('/^#[0-9a-fA-F]{3,6}$/', $val)) {
                                    $val = 'Color';
                                }
                                return ($o['name'] ?? '') . ': ' . $val;
                            }, $opts));
                        }
                    } catch (\Throwable $e) {
                        $item->variant_label = null;
                    }
                }
                return $item;
            })
            ->toArray();
    }

    // ──────────────────────────────────────────────
    // Top products
    // ──────────────────────────────────────────────
    private function getTopProducts($warehouseId, $dateFrom, $dateTo)
    {
        $q = DB::table('invoice_items as ii')
            ->join('invoices as i', 'ii.invoice_id', '=', 'i.id')
            ->join('products as p', 'ii.product_id', '=', 'p.id')
            ->whereBetween('i.date', [$dateFrom, $dateTo])
            ->whereIn('i.status', ['paid', 'completed']);

        if ($warehouseId) {
            $q->whereIn('i.cash_session_id', function ($sub) use ($warehouseId) {
                $sub->select('id')->from('cash_sessions')->where('warehouse_id', $warehouseId);
            });
        }

        return $q->groupBy('p.id', 'p.name')
            ->selectRaw('
                p.id, p.name,
                SUM(ii.quantity) as units_sold,
                SUM(ii.quantity * ii.unit_price) as revenue,
                SUM(ii.quantity * COALESCE(ii.cost_price, 0)) as cost
            ')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get()
            ->map(function ($p) {
                $p->margin = $p->revenue > 0
                    ? round((($p->revenue - $p->cost) / $p->revenue) * 100, 1)
                    : 0;
                return $p;
            })
            ->toArray();
    }

    // ──────────────────────────────────────────────
    // Payment method breakdown
    // ──────────────────────────────────────────────
    private function getPaymentBreakdown($warehouseId, $dateFrom, $dateTo)
    {
        $q = DB::table('invoices')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->whereIn('status', ['paid', 'completed']);

        if ($warehouseId) {
            $q->whereIn('cash_session_id', function ($sub) use ($warehouseId) {
                $sub->select('id')->from('cash_sessions')->where('warehouse_id', $warehouseId);
            });
        }

        return $q->groupBy('payment_method')
            ->selectRaw('
                payment_method as method,
                COUNT(*) as tx_count,
                COALESCE(SUM(total), 0) as total
            ')
            ->orderByDesc('total')
            ->get()
            ->toArray();
    }

    // ──────────────────────────────────────────────
    // Recent transactions
    // ──────────────────────────────────────────────
    private function getRecentTransactions($warehouseId, $limit = 8)
    {
        $q = DB::table('invoices as i')
            ->leftJoin('customers as c', 'i.customer_id', '=', 'c.id')
            ->whereIn('i.status', ['paid', 'completed'])
            ->select(
                'i.id', 'i.number', 'i.total', 'i.date',
                'i.payment_method', 'i.status',
                'c.name as customer_name'
            );

        if ($warehouseId) {
            $q->whereIn('i.cash_session_id', function ($sub) use ($warehouseId) {
                $sub->select('id')->from('cash_sessions')->where('warehouse_id', $warehouseId);
            });
        }

        return $q->orderByDesc('i.date')
            ->orderByDesc('i.id')
            ->limit($limit)
            ->get()
            ->map(function ($tx) {
                $tx->customer_name = $tx->customer_name ?: 'Cliente Final';
                return $tx;
            })
            ->toArray();
    }

    // ──────────────────────────────────────────────
    // Warehouse list for filter dropdown
    // ──────────────────────────────────────────────
    private function getWarehouses()
    {
        return DB::table('warehouses')
            ->where('active', true)
            ->select('id', 'name', 'is_default')
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get()
            ->toArray();
    }
}
