<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CashSession;
use App\Models\Invoice;
use App\Models\ProductReturn;
use App\Models\Warehouse;
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
        $limits = [
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

        return $limits[$plan] ?? $limits['free_trial'];
    }

    /**
     * Dashboard KPIs - operational metrics for today
     */
    public function dashboardKpis(Request $request)
    {
        try {
            $today = Carbon::today();
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

            // Total sales today (from invoices linked to cash sessions)
            $salesToday = Invoice::whereDate('created_at', $today)
                ->where('status', '!=', 'cancelled')
                ->where('type', 'invoice')
                ->sum('total');

            // Sales count today
            $salesCountToday = Invoice::whereDate('created_at', $today)
                ->where('status', '!=', 'cancelled')
                ->where('type', 'invoice')
                ->count();

            // Return alerts: users with returns today
            $returnsToday = ProductReturn::whereDate('created_at', $today)
                ->with('user:id,name')
                ->get();

            $totalReturnsAmount = $returnsToday->sum('total');
            $totalReturnsCount = $returnsToday->count();

            // Per-user return counts for alerts (flag if > 3 returns in a day)
            $returnsByUser = $returnsToday->groupBy('user_id')->map(function ($returns) {
                return [
                    'user_name' => $returns->first()->user?->name ?? 'Desconocido',
                    'count' => $returns->count(),
                    'total' => $returns->sum('total'),
                    'alert' => $returns->count() >= 3,
                ];
            })->values();

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
                    'return_alerts' => $returnsByUser->filter(fn($r) => $r['alert'])->values(),
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
     * Enriched user list with today's performance data
     */
    public function usersWithPerformance(Request $request)
    {
        try {
            $today = Carbon::today();

            $users = User::with('role')->get()->map(function ($user) use ($today) {
                // Current open cash session
                $openSession = CashSession::where('user_id', $user->id)
                    ->where('status', 'open')
                    ->with('warehouse:id,name')
                    ->first();

                // Sales today for this user (via cash sessions)
                $userSessionIds = CashSession::where('user_id', $user->id)
                    ->whereDate('opened_at', $today)
                    ->pluck('id');

                $salesToday = 0;
                $salesCountToday = 0;
                if ($userSessionIds->isNotEmpty()) {
                    $salesToday = Invoice::whereIn('cash_session_id', $userSessionIds)
                        ->where('status', '!=', 'cancelled')
                        ->where('type', 'invoice')
                        ->sum('total');

                    $salesCountToday = Invoice::whereIn('cash_session_id', $userSessionIds)
                        ->where('status', '!=', 'cancelled')
                        ->where('type', 'invoice')
                        ->count();
                }

                // Returns today
                $returnsToday = ProductReturn::where('user_id', $user->id)
                    ->whereDate('created_at', $today)
                    ->count();

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
                    'current_warehouse' => $openSession?->warehouse?->name ?? null,
                    'cash_status' => $openSession ? 'open' : 'closed',
                    'cash_session_opened_at' => $openSession?->opened_at,
                    'sales_today' => (float) $salesToday,
                    'sales_count_today' => $salesCountToday,
                    'returns_today' => $returnsToday,
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

            // 1. Cash session events (open/close)
            $sessions = CashSession::where('user_id', $user->id)
                ->whereDate('opened_at', $targetDate)
                ->orWhere(function ($q) use ($user, $targetDate) {
                    $q->where('user_id', $user->id)
                        ->whereDate('closed_at', $targetDate);
                })
                ->with('warehouse:id,name')
                ->get();

            foreach ($sessions as $session) {
                // Open event
                if ($session->opened_at && Carbon::parse($session->opened_at)->isSameDay($targetDate)) {
                    $events->push([
                        'type' => 'cash_open',
                        'timestamp' => $session->opened_at,
                        'icon' => 'cash-open',
                        'color' => 'emerald',
                        'title' => 'Abrió caja',
                        'description' => 'Sede: ' . ($session->warehouse?->name ?? 'Principal') . ' con $' . number_format((float) $session->opening_amount, 0, ',', '.'),
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
                    ]);
                }
            }

            // 2. Invoice events (sales)
            $sessionIds = CashSession::where('user_id', $user->id)->pluck('id');

            $invoices = Invoice::whereIn('cash_session_id', $sessionIds)
                ->whereDate('created_at', $targetDate)
                ->where('status', '!=', 'cancelled')
                ->where('type', 'invoice')
                ->orderBy('created_at')
                ->get(['id', 'number', 'total', 'payment_method', 'created_at']);

            foreach ($invoices as $invoice) {
                $events->push([
                    'type' => 'sale',
                    'timestamp' => $invoice->created_at,
                    'icon' => 'sale',
                    'color' => 'blue',
                    'title' => 'Venta procesada',
                    'description' => $invoice->number . ' - $' . number_format((float) $invoice->total, 0, ',', '.') . ' (' . ($invoice->payment_method ?? 'Efectivo') . ')',
                ]);
            }

            // 3. Return events
            $returns = ProductReturn::where('user_id', $user->id)
                ->whereDate('created_at', $targetDate)
                ->orderBy('created_at')
                ->get(['id', 'number', 'total', 'reason', 'created_at']);

            foreach ($returns as $return) {
                $events->push([
                    'type' => 'return',
                    'timestamp' => $return->created_at,
                    'icon' => 'return',
                    'color' => 'amber',
                    'title' => 'Procesó devolución',
                    'description' => ($return->number ?? 'DEV') . ' - $' . number_format((float) $return->total, 0, ',', '.') . ($return->reason ? ' (' . $return->reason . ')' : ''),
                ]);
            }

            // 4. Login event (from last_login if it matches target date)
            if ($user->last_login && Carbon::parse($user->last_login)->isSameDay($targetDate)) {
                $events->push([
                    'type' => 'login',
                    'timestamp' => $user->last_login,
                    'icon' => 'login',
                    'color' => 'slate',
                    'title' => 'Inició sesión',
                    'description' => 'Acceso al sistema',
                ]);
            }

            // Sort by timestamp
            $sortedEvents = $events->sortBy('timestamp')->values();

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
