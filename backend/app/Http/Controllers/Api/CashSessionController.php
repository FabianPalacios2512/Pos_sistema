<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashSession;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CashSessionController extends Controller
{
    /**
     * Listar todas las sesiones de caja (para administradores)
     */
    public function index(Request $request)
    {
        try {
            $query = CashSession::with(['user', 'warehouse'])
                ->orderBy('created_at', 'desc');

            // Restricción por sede para usuarios no-admin
            $currentUser = Auth::user();
            $totalWarehouses = Warehouse::where('active', true)->count();
            if ($totalWarehouses > 1 && $currentUser) {
                // Administrador ve todo, Admin POS y vendedores ven solo su sede
                if (!$currentUser->isFullAdmin() && $currentUser->warehouse_id) {
                    $query->where('warehouse_id', $currentUser->warehouse_id);
                }
            }

            // Filtros opcionales
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('opened_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('opened_at', '<=', $request->date_to);
            }

            $sessions = $query->get();

            // Refresh totals for open sessions so admin sees current data
            foreach ($sessions as $session) {
                if ($session->status === 'open') {
                    $session->updateSalesTotals();
                    $session->calculateExpectedAmount();
                    $session->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Sesiones obtenidas correctamente',
                'sessions' => $sessions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener sesiones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener la sesión de caja activa del usuario
     */
    public function getCurrentSession()
    {
        try {
            $userId = Auth::id(); // Requerir autenticación real

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $session = CashSession::getOpenSessionForUser($userId);

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay sesión de caja abierta',
                    'session' => null
                ]);
            }

            // Actualizar totales antes de devolver
            $session->updateSalesTotals();
            $session->calculateExpectedAmount();
            $session->save();

            return response()->json([
                'success' => true,
                'message' => 'Sesión de caja obtenida correctamente',
                'session' => $session->load(['user', 'warehouse'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la sesión: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar si el usuario tiene sesión abierta
     */
    public function checkSession()
    {
        try {
            $userId = Auth::id(); // Requerir autenticación real

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $hasSession = CashSession::hasOpenSession($userId);

            return response()->json([
                'success' => true,
                'hasOpenSession' => $hasSession,
                'message' => $hasSession ? 'Usuario tiene sesión abierta' : 'Usuario no tiene sesión abierta'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar sesión: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Abrir nueva sesión de caja
     */
    public function openSession(Request $request)
    {
        try {
            $request->validate([
                'warehouse_id' => 'required|exists:warehouses,id',
                'opening_amount' => 'required|numeric|min:0',
                'opening_notes' => 'nullable|string|max:500'
            ]);

            $userId = Auth::id(); // Requerir autenticación real

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Verificar que no hay sesión abierta para ESTE usuario específico
            if (CashSession::hasOpenSession($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya tienes una sesión de caja abierta. Debes cerrarla antes de abrir una nueva.'
                ], 400);
            }

            // Validar sede asignada (solo si el negocio tiene más de 1 sede)
            // Administradores (full y POS) pueden abrir caja en su sede, full admin en cualquiera
            $totalWarehouses = Warehouse::where('active', true)->count();
            if ($totalWarehouses > 1) {
                $user = User::with('role')->find($userId);

                if (!$user->isFullAdmin()) {
                    if ($user && !$user->warehouse_id) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No puedes abrir caja porque no tienes una sede asignada. Contacta a tu administrador para que te asigne una sede antes de abrir caja.'
                        ], 403);
                    }

                    if ($user && $user->warehouse_id && $user->warehouse_id != $request->warehouse_id) {
                        $sedeAsignada = Warehouse::find($user->warehouse_id)?->name ?? 'otra sede';
                        return response()->json([
                            'success' => false,
                            'message' => "No puedes abrir caja en esta sede porque estás asignado(a) a \"{$sedeAsignada}\". Si esto es un error, contacta a tu administrador para que actualice tu sede."
                        ], 403);
                    }
                }
            }

            $session = CashSession::openSession(
                $userId,
                $request->opening_amount,
                $request->opening_notes,
                $request->warehouse_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Sesión de caja abierta correctamente',
                'session' => $session->load(['user', 'warehouse'])
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de apertura inválidos',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al abrir sesión: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cerrar sesión de caja actual
     */
    public function closeSession(Request $request)
    {
        try {
            $request->validate([
                'actual_amount' => 'required|numeric|min:0',
                'closing_notes' => 'nullable|string|max:500'
            ]);

            $userId = Auth::id();

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $session = CashSession::getOpenSessionForUser($userId);

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes una sesión de caja abierta para cerrar'
                ], 400);
            }

            $session->closeSession(
                $request->actual_amount,
                $request->closing_notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Sesión de caja cerrada correctamente',
                'session' => $session->load(['user', 'warehouse'])
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de cierre inválidos',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar sesión: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cerrar sesión específica por ID (para administradores)
     */
    public function closeSessionById(Request $request, $sessionId)
    {
        try {
            $request->validate([
                'actual_amount' => 'required|numeric|min:0',
                'closing_notes' => 'nullable|string|max:500',
                'expenses_detail' => 'nullable|string|max:1000',
                'cash_counted' => 'nullable|numeric|min:0',
                'card_counted' => 'nullable|numeric|min:0',
                'transfer_counted' => 'nullable|numeric|min:0'
            ]);

            $session = CashSession::with('user')->findOrFail($sessionId);

            if ($session->status !== 'open') {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta sesión ya está cerrada'
                ], 400);
            }

            // Actualizar totales antes de cerrar
            $session->updateSalesTotals();
            $session->save();

            // Calcular montos esperados
            $expectedAmount = $session->calculateExpectedAmount();
            $actualAmount = $request->actual_amount;
            $difference = $actualAmount - $expectedAmount;

            $manualCashIncomes = $session->cashMovements()->where('type', 'ingreso')->sum('amount');
            $manualCashEgresos = $session->cashMovements()->where('type', 'egreso')->sum('amount');

            // Determinar estado de cierre
            $closingStatus = 'exact';
            if ($difference > 0) {
                $closingStatus = 'surplus';
            } elseif ($difference < 0) {
                $closingStatus = 'deficit';
            }
            if ($request->filled('expenses_detail') && !empty($request->expenses_detail)) {
                $closingStatus = 'with_expenses';
            }

            // Crear desglose detallado
            $closingBreakdown = [
                'opening_amount' => $session->opening_amount,
                'cash_sales' => $session->cash_sales,
                'card_sales' => $session->card_sales,
                'transfer_sales' => $session->transfer_sales,
                'total_sales' => $session->total_sales,
                'manual_cash_incomes' => $manualCashIncomes,
                'manual_cash_egresos' => $manualCashEgresos,
                'total_expenses' => $session->total_expenses,
                'expected_cash' => $expectedAmount,
                'actual_cash' => $actualAmount,
                'difference' => $difference,
                'cash_counted' => $request->get('cash_counted', $actualAmount),
                'card_counted' => $request->get('card_counted', $session->card_sales),
                'transfer_counted' => $request->get('transfer_counted', $session->transfer_sales),
                'closed_by' => auth()->user()->name ?? 'Sistema',
                'closing_timestamp' => now()->toISOString()
            ];

            // Actualizar sesión con detalles de cierre
            $session->update([
                'closed_at' => now(),
                'expected_amount' => $expectedAmount,
                'actual_amount' => $actualAmount,
                'difference_amount' => $difference,
                'closing_notes' => $request->closing_notes,
                'expenses_detail' => $request->expenses_detail,
                'closing_status' => $closingStatus,
                'closing_breakdown' => $closingBreakdown,
                'status' => CashSession::STATUS_CLOSED
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sesión de caja cerrada correctamente',
                'session' => $session->load(['user', 'warehouse']),
                'closing_details' => [
                    'status' => $closingStatus,
                    'difference' => $difference,
                    'breakdown' => $closingBreakdown
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de cierre inválidos',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar sesión: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener historial detallado de una sesión para auditoría
     */
    public function getSessionAudit($sessionId)
    {
        try {
            $session = CashSession::with(['user', 'invoices' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])->findOrFail($sessionId);

            // Obtener todas las facturas de esta sesión con detalles
            $invoices = $session->invoices()->with(['customer'])->get();

            // Obtener todas las devoluciones de esta sesión
            $returns = \App\Models\ProductReturn::where('cash_session_id', $sessionId)
                ->with(['customer', 'originalInvoice'])
                ->get();

            // Obtener todos los gastos de esta sesión
            $expenses = \App\Models\Expense::where('cash_session_id', $sessionId)
                ->with(['category', 'user'])
                ->get();

            // Obtener movimientos manuales de caja (ingresos / egresos)
            $cashMovements = \App\Models\CashMovement::where('cash_session_id', $sessionId)
                ->with(['user'])
                ->get();

            // Crear timeline de eventos
            $timeline = [];

            // Evento de apertura
            $timeline[] = [
                'type' => 'opening',
                'timestamp' => $session->created_at,
                'description' => 'Apertura de caja',
                'amount' => $session->opening_amount,
                'details' => [
                    'user' => $session->user->name ?? 'Usuario',
                    'initial_amount' => $session->opening_amount,
                    'notes' => $session->opening_notes
                ]
            ];

            // Eventos de ventas
            foreach ($invoices as $invoice) {
                // Los items están almacenados como JSON en el campo 'items'
                $items = $invoice->items ?? [];
                $itemsCount = is_array($items) ? count($items) : 0;

                $timeline[] = [
                    'type' => 'sale',
                    'timestamp' => $invoice->created_at,
                    'description' => "Venta #{$invoice->number}",
                    'amount' => $invoice->total,
                    'details' => [
                        'invoice_id' => $invoice->id,
                        'invoice_number' => $invoice->number,
                        'customer' => $invoice->customer->name ?? 'Cliente Final',
                        'payment_method' => $invoice->payment_method,
                        'items_count' => $itemsCount,
                        'items' => is_array($items) ? array_map(function($item) {
                            return [
                                'product' => $item['product_name'] ?? 'Producto',
                                'quantity' => $item['quantity'] ?? 0,
                                'unit_price' => $item['unit_price'] ?? 0,
                                'subtotal' => ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0)
                            ];
                        }, $items) : []
                    ]
                ];
            }

            // Eventos de devoluciones
            foreach ($returns as $return) {
                $items = is_string($return->items) ? json_decode($return->items, true) : $return->items;
                $itemsCount = is_array($items) ? count($items) : 0;

                $timeline[] = [
                    'type' => 'return',
                    'timestamp' => $return->created_at,
                    'description' => "Devolución #{$return->number}",
                    'amount' => -$return->total, // Negativo porque es una devolución
                    'details' => [
                        'return_id' => $return->id,
                        'return_number' => $return->number,
                        'original_invoice' => $return->originalInvoice->number ?? 'N/A',
                        'customer' => $return->customer->name ?? 'Cliente Final',
                        'refund_method' => $return->refund_method,
                        'reason' => $return->reason,
                        'items_count' => $itemsCount,
                        'items' => is_array($items) ? array_map(function($item) {
                            return [
                                'product' => $item['product_name'] ?? 'Producto',
                                'quantity' => $item['quantity'] ?? 0,
                                'unit_price' => $item['unit_price'] ?? 0,
                                'subtotal' => ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0)
                            ];
                        }, $items) : []
                    ]
                ];
            }

            // Eventos de gastos
            foreach ($expenses as $expense) {
                $timeline[] = [
                    'type' => 'expense',
                    'timestamp' => $expense->created_at,
                    'description' => "Gasto: {$expense->description}",
                    'amount' => -$expense->amount, // Negativo porque es un egreso
                    'details' => [
                        'expense_id' => $expense->id,
                        'category' => $expense->category->name ?? 'Sin categoría',
                        'category_color' => $expense->category->color ?? '#6B7280',
                        'payment_method' => $expense->payment_method,
                        'supplier' => $expense->supplier,
                        'receipt_number' => $expense->receipt_number,
                        'user' => $expense->user->name ?? 'Usuario'
                    ]
                ];
            }

            // Eventos de movimientos manuales de caja
            foreach ($cashMovements as $movement) {
                $timeline[] = [
                    'type' => $movement->type === 'ingreso' ? 'cash-income' : 'cash-expense',
                    'timestamp' => $movement->created_at,
                    'description' => ($movement->type === 'ingreso' ? 'Ingreso: ' : 'Egreso: ') . $movement->concept,
                    'amount' => $movement->type === 'ingreso' ? $movement->amount : -$movement->amount,
                    'details' => [
                        'movement_id' => $movement->id,
                        'concept' => $movement->concept,
                        'reference' => $movement->reference,
                        'notes' => $movement->notes,
                        'user' => $movement->user->name ?? 'Usuario',
                        'movement_type' => $movement->type,
                    ]
                ];
            }

            // Eventos de abonos (pagos de crédito)
            $creditPayments = \App\Models\CreditPayment::where('cash_session_id', $sessionId)
                ->with(['customer', 'user'])
                ->get();

            // Fallback: si no hay por cash_session_id, buscar por user_id y rango de tiempo
            if ($creditPayments->isEmpty() && $session->user_id) {
                $creditPayments = \App\Models\CreditPayment::where('user_id', $session->user_id)
                    ->whereBetween('created_at', [
                        $session->created_at,
                        $session->closed_at ?? now()
                    ])
                    ->with(['customer', 'user'])
                    ->get();
            }

            foreach ($creditPayments as $payment) {
                $methodLabels = ['cash' => 'Efectivo', 'card' => 'Tarjeta', 'transfer' => 'Transferencia'];
                $timeline[] = [
                    'type' => 'abono',
                    'timestamp' => $payment->created_at,
                    'description' => "Abono de " . ($payment->customer->name ?? 'Cliente'),
                    'amount' => $payment->amount,
                    'details' => [
                        'payment_id' => $payment->id,
                        'customer' => $payment->customer->name ?? 'Cliente',
                        'customer_document' => $payment->customer->document_number ?? '',
                        'method' => $methodLabels[$payment->method] ?? $payment->method,
                        'reference' => $payment->reference,
                        'notes' => $payment->notes,
                        'user' => $payment->user->name ?? 'Usuario'
                    ]
                ];
            }

            // Ordenar timeline cronológicamente (excluyendo apertura que debe estar primera)
            $opening = array_shift($timeline); // Remover apertura temporalmente
            usort($timeline, function($a, $b) {
                return strtotime($a['timestamp']) - strtotime($b['timestamp']);
            });
            array_unshift($timeline, $opening); // Volver a poner apertura al inicio

            // Evento de cierre (si existe)
            if ($session->status === 'closed') {
                $timeline[] = [
                    'type' => 'closing',
                    'timestamp' => $session->closed_at,
                    'description' => 'Cierre de caja',
                    'amount' => $session->actual_amount,
                    'details' => [
                        'expected_amount' => $session->expected_amount,
                        'actual_amount' => $session->actual_amount,
                        'difference' => $session->difference_amount,
                        'status' => $session->closing_status,
                        'notes' => $session->closing_notes,
                        'expenses' => $session->expenses_detail,
                        'breakdown' => $session->closing_breakdown
                    ]
                ];
            }

            // Ordenar timeline por timestamp
            usort($timeline, function($a, $b) {
                return strtotime($a['timestamp']) - strtotime($b['timestamp']);
            });

            // Estadísticas de la sesión
            $stats = [
                'total_transactions' => $invoices->count(),
                'total_returns' => $returns->count(),
                'total_returns_amount' => $returns->sum('total'),
                'total_expenses' => $expenses->count(),
                'total_expenses_amount' => $expenses->sum('amount'),
                'total_cash_incomes' => $cashMovements->where('type', 'ingreso')->count(),
                'total_cash_incomes_amount' => $cashMovements->where('type', 'ingreso')->sum('amount'),
                'total_cash_egresos' => $cashMovements->where('type', 'egreso')->count(),
                'total_cash_egresos_amount' => $cashMovements->where('type', 'egreso')->sum('amount'),
                'total_abonos' => $creditPayments->count(),
                'total_abonos_amount' => $creditPayments->sum('amount'),
                'payment_methods_breakdown' => $invoices->groupBy('payment_method')->map(function($group) {
                    return [
                        'count' => $group->count(),
                        'total' => $group->sum('total')
                    ];
                }),
                'session_duration' => $session->created_at->diffInMinutes($session->updated_at),
                'average_sale' => $invoices->count() > 0 ? $invoices->avg('total') : 0,
                'largest_sale' => $invoices->max('total') ?? 0,
                'smallest_sale' => $invoices->min('total') ?? 0
            ];

            return response()->json([
                'success' => true,
                'session' => $session,
                'timeline' => $timeline,
                'statistics' => $stats,
                'invoices' => $invoices,
                'returns' => $returns,
                'expenses' => $expenses,
                'cashMovements' => $cashMovements,
                'creditPayments' => $creditPayments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener auditoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener historial de sesiones del usuario
     */
    public function getHistory(Request $request)
    {
        try {
            $userId = Auth::id();
            $limit = $request->get('limit', 10);
            $page = $request->get('page', 1);

            $sessions = CashSession::forUser($userId)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'message' => 'Historial obtenido correctamente',
                'sessions' => $sessions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener historial: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de la sesión actual
     */
    public function getSessionStats()
    {
        try {
            $userId = Auth::id();
            $session = CashSession::getOpenSessionForUser($userId);

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay sesión de caja abierta'
                ], 400);
            }

            // Actualizar totales
            $session->updateSalesTotals();
            $session->calculateExpectedAmount();
            $session->save();

            // Estadísticas adicionales
            $manualCashIncomes = $session->cashMovements()->where('type', 'ingreso')->sum('amount');
            $manualCashEgresos = $session->cashMovements()->where('type', 'egreso')->sum('amount');

            $stats = [
                'session_info' => [
                    'id' => $session->id,
                    'opening_amount' => $session->opening_amount,
                    'opening_time' => $session->opened_at ? $session->opened_at->toTimeString() : null,
                    'opening_date' => $session->opened_at ? $session->opened_at->toDateString() : null,
                    'status' => $session->status
                ],
                'sales_summary' => [
                    'total_sales' => $session->total_sales,
                    'cash_sales' => $session->cash_sales,
                    'card_sales' => $session->card_sales,
                    'transfer_sales' => $session->transfer_sales,
                    'total_expenses' => $session->total_expenses,
                    'manual_cash_incomes' => $manualCashIncomes,
                    'manual_cash_egresos' => $manualCashEgresos,
                ],
                'cash_flow' => [
                    'opening_amount' => $session->opening_amount,
                    'expected_amount' => $session->expected_amount,
                    'net_cash_flow' => $session->cash_sales + $manualCashIncomes - $session->total_expenses - $manualCashEgresos
                ],
                'transaction_count' => [
                    'invoices_count' => $session->invoices()->count(),
                    'sales_count' => $session->sales()->count()
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas obtenidas correctamente',
                'stats' => $stats,
                'session' => $session
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Forzar actualización de totales de la sesión
     */
    public function updateTotals()
    {
        try {
            $userId = Auth::id();
            $session = CashSession::getOpenSessionForUser($userId);

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay sesión de caja abierta'
                ], 400);
            }

            $session->updateSalesTotals();
            $session->calculateExpectedAmount();
            $session->save();

            return response()->json([
                'success' => true,
                'message' => 'Totales actualizados correctamente',
                'session' => $session
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar totales: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener resumen del día actual
     */
    public function getDailySummary()
    {
        try {
            $userId = Auth::id();
            $today = now()->toDateString();

            $sessions = CashSession::forUser($userId)
                ->whereDate('opened_at', $today)
                ->get();

            $summary = [
                'date' => $today,
                'total_sessions' => $sessions->count(),
                'open_sessions' => $sessions->where('status', CashSession::STATUS_OPEN)->count(),
                'closed_sessions' => $sessions->where('status', CashSession::STATUS_CLOSED)->count(),
                'total_sales' => $sessions->sum('total_sales'),
                'total_cash_sales' => $sessions->sum('cash_sales'),
                'total_card_sales' => $sessions->sum('card_sales'),
                'total_transfer_sales' => $sessions->sum('transfer_sales'),
                'sessions' => $sessions
            ];

            return response()->json([
                'success' => true,
                'message' => 'Resumen diario obtenido correctamente',
                'summary' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener resumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener la sesión de caja actual de un usuario específico
     */
    public function getUserSession($userId)
    {
        try {
            $session = CashSession::getOpenSessionForUser($userId);

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay sesión de caja abierta para este usuario',
                    'session' => null
                ]);
            }

            // Actualizar totales antes de devolver
            $session->updateSalesTotals();
            $session->save();

            return response()->json([
                'success' => true,
                'message' => 'Sesión de caja obtenida correctamente',
                'session' => $session->load(['user', 'warehouse'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la sesión: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user has any forced-closed sessions pending audit
     */
    public function checkForcedClosed()
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $forcedSession = CashSession::where('user_id', $userId)
                ->where('status', CashSession::STATUS_FORCED_CLOSED)
                ->with('warehouse:id,name', 'user:id,name')
                ->orderBy('closed_at', 'asc')
                ->first();

            if (!$forcedSession) {
                return response()->json([
                    'success' => true,
                    'has_forced_closed' => false,
                ]);
            }

            return response()->json([
                'success' => true,
                'has_forced_closed' => true,
                'session' => [
                    'id' => $forcedSession->id,
                    'user_name' => $forcedSession->user?->name ?? 'Usuario',
                    'created_at' => $forcedSession->created_at,
                    'opened_at' => $forcedSession->opened_at,
                    'closed_at' => $forcedSession->closed_at,
                    'opening_amount' => (float) $forcedSession->opening_amount,
                    'expected_amount' => (float) ($forcedSession->expected_amount ?? 0),
                    'warehouse_name' => $forcedSession->warehouse?->name ?? 'Sin sede',
                    'total_sales' => (float) $forcedSession->total_sales,
                    'cash_sales' => (float) $forcedSession->cash_sales,
                    'card_sales' => (float) ($forcedSession->closing_breakdown['sales']['card'] ?? 0),
                    'transfer_sales' => (float) ($forcedSession->closing_breakdown['sales']['transfer'] ?? 0),
                    'total_expenses' => (float) ($forcedSession->total_expenses ?? 0),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar cierres forzados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resolve a forced-closed session with the cashier's actual count (audit)
     */
    public function resolveForcedClose(Request $request)
    {
        try {
            $request->validate([
                'session_id' => 'required|integer|exists:cash_sessions,id',
                'actual_amount' => 'required|numeric|min:0',
                'closing_notes' => 'nullable|string|max:500',
            ]);

            $userId = Auth::id();

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $session = CashSession::where('id', $request->session_id)
                ->where('user_id', $userId)
                ->where('status', CashSession::STATUS_FORCED_CLOSED)
                ->firstOrFail();

            $expectedAmount = (float) ($session->expected_amount ?? 0);
            $actualAmount = (float) $request->actual_amount;
            $difference = $actualAmount - $expectedAmount;

            $closingStatus = 'exact';
            if ($difference > 0.01) {
                $closingStatus = 'surplus';
            } elseif ($difference < -0.01) {
                $closingStatus = 'deficit';
            }

            $session->update([
                'status' => CashSession::STATUS_CLOSED,
                'actual_amount' => $actualAmount,
                'difference_amount' => $difference,
                'closing_status' => $closingStatus,
                'closing_notes' => ($session->closing_notes ? $session->closing_notes . ' | ' : '')
                    . 'Arqueo del cajero: ' . ($request->closing_notes ?? 'Sin observaciones'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Arqueo registrado correctamente. Ya puedes operar.',
                'session' => $session->fresh(),
                'difference' => $difference,
                'closing_status' => $closingStatus,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al resolver cierre forzado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Devuelve info de acceso del usuario actual para la vista de cajas.
     * El frontend usa esto para mostrar el empty state correcto.
     */
    public function warehouseAccess()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'No autenticado'], 401);
        }

        $user->loadMissing('role');
        $roleName = strtolower($user->role->name ?? '');
        $isAdmin = in_array($roleName, ['administrador', 'admin', 'superadmin']);
        $totalWarehouses = Warehouse::where('active', true)->count();
        $needsRestriction = $totalWarehouses > 1 && !$isAdmin;

        $userWarehouse = $user->warehouse_id ? Warehouse::find($user->warehouse_id) : null;

        return response()->json([
            'success' => true,
            'is_admin' => $isAdmin,
            'needs_restriction' => $needsRestriction,
            'has_warehouse' => $user->warehouse_id !== null,
            'warehouse' => $userWarehouse ? [
                'id' => $userWarehouse->id,
                'name' => $userWarehouse->name,
            ] : null,
            'total_warehouses' => $totalWarehouses,
        ]);
    }
}
