<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CashSession;
use App\Models\Invoice;
use App\Models\ProductReturn;
use App\Models\CreditPayment;
use App\Models\Warehouse;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    /**
     * Plan limits configuration
     */
    private function getPlanLimits(string $plan): array
    {
        $defaults = [
            'free_trial' => [
                'max_users' => 2,
                'max_warehouses' => 1,
                'multi_sede' => false,
                'audit_timeline' => false,
                'performance_metrics' => false,
            ],
            'basic' => [
                'max_users' => 3,
                'max_warehouses' => 1,
                'multi_sede' => false,
                'audit_timeline' => true,
                'performance_metrics' => true,
            ],
            'premium' => [
                'max_users' => 10,
                'max_warehouses' => 5,
                'multi_sede' => true,
                'audit_timeline' => true,
                'performance_metrics' => true,
            ],
            'enterprise' => [
                'max_users' => 999,
                'max_warehouses' => 999,
                'multi_sede' => true,
                'audit_timeline' => true,
                'performance_metrics' => true,
            ],
        ];

        $limits = $defaults[$plan] ?? $defaults['free_trial'];

        // Override con valores configurados por el admin
        $adminMaxUsers = tenant('max_users');
        if ($adminMaxUsers !== null && (int)$adminMaxUsers > 0) {
            $limits['max_users'] = (int)$adminMaxUsers;
        }
        $adminMaxWarehouses = tenant('max_warehouses');
        if ($adminMaxWarehouses !== null && (int)$adminMaxWarehouses > 0) {
            $limits['max_warehouses'] = (int)$adminMaxWarehouses;
            $limits['multi_sede'] = (int)$adminMaxWarehouses > 1;
        }

        return $limits;
    }

    /**
     * Dashboard KPIs - operational metrics for today
     */
    public function dashboardKpis(Request $request)
    {
        try {
            $dateFrom = null;
            $dateTo = null;

            if ($request->has('date')) {
                $dateFrom = Carbon::parse($request->date)->startOfDay();
                $dateTo = Carbon::parse($request->date)->endOfDay();
            } elseif ($request->has('date_from') && $request->has('date_to')) {
                $dateFrom = Carbon::parse($request->date_from)->startOfDay();
                $dateTo = Carbon::parse($request->date_to)->endOfDay();
            } else {
                $dateFrom = Carbon::today()->startOfDay();
                $dateTo = Carbon::today()->endOfDay();
            }

            $isToday = $dateFrom->isToday();
            $tenant = tenant();
            $plan = $tenant?->plan ?? 'free_trial';
            $planLimits = $this->getPlanLimits($plan);

            // Active now: users with open cash sessions
            $activeNow = CashSession::where('status', 'open')
                ->with(['user:id,name', 'warehouse:id,name'])
                ->get()
                ->map(fn($s) => [
                    'user_id' => $s->user_id,
                    'user_name' => $s->user?->name ?? 'Desconocido',
                    'warehouse' => $s->warehouse?->name ?? 'Principal',
                    'opened_at' => $s->opened_at,
                ]);

            // Total sales for the period
            $salesToday = Invoice::whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('status', '!=', 'cancelled')
                ->where('type', 'invoice')
                ->sum('total');

            // Sales count for the period
            $salesCountToday = Invoice::whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('status', '!=', 'cancelled')
                ->where('type', 'invoice')
                ->count();

            // Return alerts for the period
            $returnsToday = ProductReturn::whereBetween('created_at', [$dateFrom, $dateTo])
                ->with(['user:id,name', 'originalInvoice:id,number'])
                ->get();

            $totalReturnsAmount = $returnsToday->sum('total');
            $totalReturnsCount = $returnsToday->count();

            // Detailed return info for alert panel
            $returnDetails = $returnsToday->map(fn($r) => [
                'id' => $r->id,
                'number' => $r->number ?? 'DEV-' . $r->id,
                'total' => (float) $r->total,
                'reason' => $r->reason,
                'user_name' => $r->user?->name ?? 'Desconocido',
                'user_id' => $r->user_id,
                'invoice_number' => $r->originalInvoice?->number ?? null,
                'created_at' => $r->created_at,
            ])->values();

            // Per-user return counts for alerts (flag if > 3 returns in a day)
            $returnsByUser = $returnsToday->groupBy('user_id')->map(function ($returns) {
                return [
                    'user_name' => $returns->first()->user?->name ?? 'Desconocido',
                    'count' => $returns->count(),
                    'total' => $returns->sum('total'),
                    'alert' => $returns->count() >= 3,
                ];
            })->values();

            // Cash discrepancies for the period (closed sessions with difference)
            $discrepancySessions = CashSession::whereBetween('closed_at', [$dateFrom, $dateTo])
                ->where('status', 'closed')
                ->whereNotNull('difference_amount')
                ->whereRaw('ABS(difference_amount) > 0')
                ->with(['user:id,name', 'warehouse:id,name'])
                ->get();

            $discrepanciesToday = $discrepancySessions->count();

            // Detailed discrepancy info for alert panel
            $discrepancyDetails = $discrepancySessions->map(fn($s) => [
                'user_name' => $s->user?->name ?? 'Desconocido',
                'user_id' => $s->user_id,
                'warehouse' => $s->warehouse?->name ?? 'Principal',
                'difference' => (float) $s->difference_amount,
                'opening_amount' => (float) $s->opening_amount,
                'closing_amount' => (float) $s->closing_amount,
                'closed_at' => $s->closed_at,
            ])->values();

            // Total users and limits
            $totalUsers = User::count();
            $activeUsersCount = User::where('active', true)->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'active_now' => $activeNow,
                    'active_now_count' => $activeNow->count(),
                    'sales_today' => (float) $salesToday,
                    'sales_count_today' => $salesCountToday,
                    'returns_today_amount' => (float) $totalReturnsAmount,
                    'returns_today_count' => $totalReturnsCount,
                    'returns_today_details' => $returnDetails,
                    'return_alerts' => $returnsByUser->filter(fn($r) => $r['alert'])->values(),
                    'discrepancies_today' => $discrepanciesToday,
                    'discrepancies_today_details' => $discrepancyDetails,
                    'total_users' => $totalUsers,
                    'active_users' => $activeUsersCount,
                    'plan' => $plan,
                    'plan_limits' => $planLimits,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener KPIs del dashboard',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Enriched user list with performance data for a given date range
     */
    public function usersWithPerformance(Request $request)
    {
        try {
            // Support date range filters: date, date_from, date_to
            $dateFrom = null;
            $dateTo = null;

            if ($request->has('date')) {
                $dateFrom = Carbon::parse($request->date)->startOfDay();
                $dateTo = Carbon::parse($request->date)->endOfDay();
            } elseif ($request->has('date_from') && $request->has('date_to')) {
                $dateFrom = Carbon::parse($request->date_from)->startOfDay();
                $dateTo = Carbon::parse($request->date_to)->endOfDay();
            } else {
                $dateFrom = Carbon::today()->startOfDay();
                $dateTo = Carbon::today()->endOfDay();
            }

            $users = User::with(['role', 'warehouse:id,name'])->get()->map(function ($user) use ($dateFrom, $dateTo) {
                // Current open cash session (always real-time)
                $openSession = CashSession::where('user_id', $user->id)
                    ->where('status', 'open')
                    ->with('warehouse:id,name')
                    ->first();

                // Sales for the date range (via cash sessions)
                $userSessionIds = CashSession::where('user_id', $user->id)->pluck('id');

                $salesPeriod = 0;
                $salesCountPeriod = 0;
                if ($userSessionIds->isNotEmpty()) {
                    $salesPeriod = Invoice::whereIn('cash_session_id', $userSessionIds)
                        ->whereBetween('created_at', [$dateFrom, $dateTo])
                        ->where('status', '!=', 'cancelled')
                        ->where('type', 'invoice')
                        ->sum('total');

                    $salesCountPeriod = Invoice::whereIn('cash_session_id', $userSessionIds)
                        ->whereBetween('created_at', [$dateFrom, $dateTo])
                        ->where('status', '!=', 'cancelled')
                        ->where('type', 'invoice')
                        ->count();
                }

                // Returns for the period
                $returnsPeriod = ProductReturn::where('user_id', $user->id)
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->count();

                // Attendance: entry/exit for the date range
                $attendanceLogs = AttendanceLog::where('user_id', $user->id)
                    ->whereBetween('event_at', [$dateFrom, $dateTo])
                    ->orderBy('event_at', 'asc')
                    ->get();

                $firstEntry = $attendanceLogs->where('event_type', 'entry')->first();
                $lastExit = $attendanceLogs->where('event_type', 'exit')->last();

                // Check if exit was auto-closed by system
                $exitIsAutoClose = $lastExit && $lastExit->is_auto_closed;
                $exitClosedBy = $lastExit ? $lastExit->closed_by : null;

                // Cash discrepancy for the period (closed sessions)
                $cashDiscrepancy = null;
                $closedSession = CashSession::where('user_id', $user->id)
                    ->where('status', 'closed')
                    ->whereBetween('closed_at', [$dateFrom, $dateTo])
                    ->whereNotNull('difference_amount')
                    ->orderBy('closed_at', 'desc')
                    ->first();

                if ($closedSession) {
                    $cashDiscrepancy = (float) $closedSession->difference_amount;
                }

                // Check if user has any forced-closed session (pending audit)
                $hasForcedClosed = CashSession::where('user_id', $user->id)
                    ->where('status', 'forced_closed')
                    ->exists();

                // Determine cash_status: open > forced_closed > closed
                $cashStatus = 'closed';
                if ($openSession) {
                    $cashStatus = 'open';
                } elseif ($hasForcedClosed) {
                    $cashStatus = 'forced_closed';
                }

                // Determine last ingress
                $lastIngress = null;
                if ($firstEntry) {
                    $lastIngress = $firstEntry->event_at;
                } elseif ($openSession?->opened_at) {
                    $lastIngress = $openSession->opened_at;
                } elseif ($user->last_login && Carbon::parse($user->last_login)->between($dateFrom, $dateTo)) {
                    $lastIngress = $user->last_login;
                }

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'cc' => $user->cc,
                    'phone' => $user->phone,
                    'active' => $user->active,
                    'last_login' => $user->last_login,
                    'role' => $user->role,
                    'role_id' => $user->role_id,
                    // Performance data
                    'current_warehouse' => $openSession?->warehouse?->name ?? $user->warehouse?->name ?? null,
                    'warehouse_id' => $openSession?->warehouse_id ?? $user->warehouse_id,
                    'cash_status' => $cashStatus,
                    'cash_session_opened_at' => $openSession?->opened_at,
                    'last_ingress' => $lastIngress,
                    'sales_today' => (float) $salesPeriod,
                    'sales_count_today' => $salesCountPeriod,
                    'returns_today' => $returnsPeriod,
                    // New: attendance entry/exit times
                    'entry_time' => $firstEntry?->event_at,
                    'exit_time' => $lastExit?->event_at,
                    'exit_is_auto_closed' => $exitIsAutoClose,
                    'exit_closed_by' => $exitClosedBy,
                    // New: cash discrepancy
                    'cash_discrepancy' => $cashDiscrepancy,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $users,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuarios con rendimiento',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Full user profile with performance metrics
     */
    public function userProfile(string $id)
    {
        try {
            $user = User::with('role')->findOrFail($id);
            $now = Carbon::now();
            $startOfMonth = $now->copy()->startOfMonth();

            // Monthly performance
            $monthSessionIds = CashSession::where('user_id', $user->id)
                ->where('opened_at', '>=', $startOfMonth)
                ->pluck('id');

            $monthlyInvoices = Invoice::whereIn('cash_session_id', $monthSessionIds)
                ->where('status', '!=', 'cancelled')
                ->where('type', 'invoice');

            $totalSoldMonth = (float) $monthlyInvoices->sum('total');
            $invoicesCount = $monthlyInvoices->count();
            $avgTicket = $invoicesCount > 0 ? round($totalSoldMonth / $invoicesCount, 2) : 0;

            // Monthly returns
            $returnsMonth = ProductReturn::where('user_id', $user->id)
                ->where('created_at', '>=', $startOfMonth)
                ->get();

            $totalReturnsMonth = (float) $returnsMonth->sum('total');
            $returnsCountMonth = $returnsMonth->count();

            // Cash sessions this month
            $cashSessionsMonth = CashSession::where('user_id', $user->id)
                ->where('opened_at', '>=', $startOfMonth)
                ->with('warehouse:id,name')
                ->orderBy('opened_at', 'desc')
                ->get();

            // Cash discrepancies (sessions with non-zero difference)
            $discrepancies = $cashSessionsMonth->filter(fn($s) => 
                $s->difference_amount && abs((float) $s->difference_amount) > 0
            )->count();

            // Current session
            $currentSession = CashSession::where('user_id', $user->id)
                ->where('status', 'open')
                ->with('warehouse:id,name')
                ->first();

            // Warehouses this user has worked in
            $warehousesWorked = CashSession::where('user_id', $user->id)
                ->whereNotNull('warehouse_id')
                ->distinct('warehouse_id')
                ->pluck('warehouse_id');

            $warehouses = Warehouse::whereIn('id', $warehousesWorked)
                ->where('active', true)
                ->get(['id', 'name', 'address']);

            // Available warehouses for transfer
            $allWarehouses = Warehouse::where('active', true)->get(['id', 'name', 'address']);

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'performance' => [
                        'total_sold_month' => $totalSoldMonth,
                        'invoices_count' => $invoicesCount,
                        'avg_ticket' => $avgTicket,
                        'returns_count' => $returnsCountMonth,
                        'returns_amount' => $totalReturnsMonth,
                        'cash_sessions_count' => $cashSessionsMonth->count(),
                        'discrepancies' => $discrepancies,
                    ],
                    'current_session' => $currentSession ? [
                        'id' => $currentSession->id,
                        'warehouse' => $currentSession->warehouse?->name ?? 'Principal',
                        'warehouse_id' => $currentSession->warehouse_id,
                        'opened_at' => $currentSession->opened_at,
                        'opening_amount' => (float) $currentSession->opening_amount,
                    ] : null,
                    'warehouses_worked' => $warehouses,
                    'all_warehouses' => $allWarehouses,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener perfil del usuario',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Activity timeline for a user on a given date
     * Reconstructed from existing tables: CashSession, Invoice, ProductReturn
     */
    public function userTimeline(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $date = $request->input('date', Carbon::today()->toDateString());
            $targetDate = Carbon::parse($date);

            $events = collect();

            // 1. Login event (always first if exists)
            if ($user->last_login && Carbon::parse($user->last_login)->isSameDay($targetDate)) {
                $events->push([
                    'type' => 'login',
                    'timestamp' => $user->last_login,
                    'icon' => 'login',
                    'color' => 'slate',
                    'title' => 'Inició sesión',
                    'description' => 'Acceso al sistema',
                    '_order' => 0, // Login siempre primero
                ]);
            }

            // 2. Cash sessions with their invoices/returns grouped inside
            $sessions = CashSession::where('user_id', $user->id)
                ->where(function ($q) use ($targetDate) {
                    $q->whereDate('opened_at', $targetDate)
                      ->orWhereDate('closed_at', $targetDate);
                })
                ->with('warehouse:id,name')
                ->orderBy('opened_at')
                ->get();

            // Get all invoices and returns for this user today, indexed by cash_session_id
            $allSessionIds = $sessions->pluck('id');

            $invoicesBySession = Invoice::whereIn('cash_session_id', $allSessionIds)
                ->whereDate('created_at', $targetDate)
                ->where('status', '!=', 'cancelled')
                ->where('type', 'invoice')
                ->orderBy('created_at')
                ->get(['id', 'number', 'total', 'payment_method', 'created_at', 'cash_session_id'])
                ->groupBy('cash_session_id');

            $returnsBySession = ProductReturn::where('user_id', $user->id)
                ->whereDate('created_at', $targetDate)
                ->orderBy('created_at')
                ->get(['id', 'number', 'total', 'reason', 'created_at']);

            // Get all credit payments (abonos) for this user today
            $abonosBySession = CreditPayment::where('user_id', $user->id)
                ->whereDate('created_at', $targetDate)
                ->with('customer:id,name')
                ->orderBy('created_at')
                ->get();

            foreach ($sessions as $sessionIdx => $session) {
                $sessionOrder = ($sessionIdx + 1) * 1000; // Base order per session

                // Open event
                if ($session->opened_at && Carbon::parse($session->opened_at)->isSameDay($targetDate)) {
                    $events->push([
                        'type' => 'cash_open',
                        'timestamp' => $session->opened_at,
                        'icon' => 'cash-open',
                        'color' => 'emerald',
                        'title' => 'Abrió caja',
                        'description' => 'Sede: ' . ($session->warehouse?->name ?? 'Principal') . ' con $' . number_format((float) $session->opening_amount, 0, ',', '.'),
                        '_order' => $sessionOrder,
                    ]);
                }

                // Invoices belonging to this session (sandwiched between open and close)
                $sessionInvoices = $invoicesBySession->get($session->id, collect());
                foreach ($sessionInvoices as $invoiceIdx => $invoice) {
                    $events->push([
                        'type' => 'sale',
                        'timestamp' => $invoice->created_at,
                        'icon' => 'sale',
                        'color' => 'blue',
                        'title' => 'Venta procesada',
                        'description' => $invoice->number . ' - $' . number_format((float) $invoice->total, 0, ',', '.') . ' (' . ($invoice->payment_method ?? 'Efectivo') . ')',
                        '_order' => $sessionOrder + 1 + $invoiceIdx,
                    ]);
                }

                // Returns that fall within this session's time range
                $sessionStart = Carbon::parse($session->opened_at);
                $sessionEnd = $session->closed_at ? Carbon::parse($session->closed_at) : Carbon::now();
                $sessionReturns = $returnsBySession->filter(function ($r) use ($sessionStart, $sessionEnd) {
                    $t = Carbon::parse($r->created_at);
                    return $t->between($sessionStart, $sessionEnd);
                });
                foreach ($sessionReturns as $return) {
                    $events->push([
                        'type' => 'return',
                        'timestamp' => $return->created_at,
                        'icon' => 'return',
                        'color' => 'amber',
                        'title' => 'Procesó devolución',
                        'description' => ($return->number ?? 'DEV') . ' - $' . number_format((float) $return->total, 0, ',', '.') . ($return->reason ? ' (' . $return->reason . ')' : ''),
                        '_order' => $sessionOrder + 500,
                    ]);
                }

                // Abonos (Credit payments) that fall within this session's time range
                $sessionAbonos = $abonosBySession->filter(function ($a) use ($session) {
                    return $a->cash_session_id === $session->id || 
                           (!$a->cash_session_id && Carbon::parse($a->created_at)->between(
                               Carbon::parse($session->opened_at),
                               $session->closed_at ? Carbon::parse($session->closed_at) : Carbon::now()
                           ));
                });
                $methodLabels = ['cash' => 'Efectivo', 'card' => 'Tarjeta', 'transfer' => 'Transferencia'];
                foreach ($sessionAbonos as $abono) {
                    $events->push([
                        'type' => 'abono',
                        'timestamp' => $abono->created_at,
                        'icon' => 'credit-payment',
                        'color' => 'emerald',
                        'title' => 'Registró abono',
                        'description' => ($abono->customer->name ?? 'Cliente') . ' - $' . number_format((float) $abono->amount, 0, ',', '.') . ' (' . ($methodLabels[$abono->method] ?? $abono->method) . ')',
                        '_order' => $sessionOrder + 600,
                    ]);
                }

                // Close event
                if ($session->closed_at && Carbon::parse($session->closed_at)->isSameDay($targetDate)) {
                    $diff = (float) ($session->difference_amount ?? 0);
                    $color = abs($diff) > 0 ? 'rose' : 'emerald';
                    $diffText = $diff != 0 ? ' (Descuadre: $' . number_format($diff, 0, ',', '.') . ')' : ' (Cuadrada)';

                    $events->push([
                        'type' => 'cash_close',
                        'timestamp' => $session->closed_at,
                        'icon' => 'cash-close',
                        'color' => $color,
                        'title' => 'Cerró caja',
                        'description' => 'Total: $' . number_format((float) $session->closing_amount, 0, ',', '.') . $diffText,
                        '_order' => $sessionOrder + 999,
                    ]);
                }
            }

            // Sort by _order to maintain logical grouping (login → open → sales → returns → close)
            $sortedEvents = $events->sortBy('_order')->values()->map(function ($e) {
                unset($e['_order']);
                return $e;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'date' => $date,
                    'user_name' => $user->name,
                    'events' => $sortedEvents,
                    'total_events' => $sortedEvents->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener timeline del usuario',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Assign user to a different warehouse (sede transfer)
     * Updates the user's current open cash session's warehouse
     */
    public function assignWarehouse(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $warehouseId = $request->input('warehouse_id');

            if (!$warehouseId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Se requiere warehouse_id',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $warehouse = Warehouse::where('active', true)->findOrFail($warehouseId);

            // Check tenant plan
            $tenant = tenant();
            $plan = $tenant?->plan ?? 'free_trial';
            $limits = $this->getPlanLimits($plan);

            if (!$limits['multi_sede']) {
                return response()->json([
                    'success' => false,
                    'message' => 'La función Multi-Sede requiere un plan Premium o superior',
                    'upgrade_required' => true,
                ], Response::HTTP_FORBIDDEN);
            }

            // Check if user has an open cash session
            $session = CashSession::where('user_id', $user->id)
                ->where('status', 'open')
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no tiene una caja abierta. Debe abrir caja en la nueva sede.',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Update session warehouse
            $oldWarehouse = $session->warehouse?->name ?? 'Sin sede';
            $session->warehouse_id = $warehouse->id;
            $session->save();

            return response()->json([
                'success' => true,
                'message' => "Usuario transferido de '{$oldWarehouse}' a '{$warehouse->name}'",
                'data' => [
                    'user_id' => $user->id,
                    'old_warehouse' => $oldWarehouse,
                    'new_warehouse' => $warehouse->name,
                    'new_warehouse_id' => $warehouse->id,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar sede',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get plan limits for the current tenant
     */
    public function planInfo()
    {
        try {
            $tenant = tenant();
            $plan = $tenant?->plan ?? 'free_trial';
            $limits = $this->getPlanLimits($plan);
            $totalUsers = User::count();
            $totalWarehouses = Warehouse::where('active', true)->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'plan' => $plan,
                    'limits' => $limits,
                    'usage' => [
                        'users' => $totalUsers,
                        'warehouses' => $totalWarehouses,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener info del plan',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
