<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashSession;
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
            $query = CashSession::with('user')
                ->orderBy('created_at', 'desc');

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
            $expectedAmount = $session->opening_amount + $session->cash_sales;
            $actualAmount = $request->actual_amount;
            $difference = $actualAmount - $expectedAmount;

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
                        'customer' => $invoice->customer->name ?? 'Cliente General',
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
                        'customer' => $return->customer->name ?? 'Cliente General',
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
                'invoices' => $invoices
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
                    'total_expenses' => $session->total_expenses
                ],
                'cash_flow' => [
                    'opening_amount' => $session->opening_amount,
                    'expected_amount' => $session->expected_amount,
                    'net_cash_flow' => $session->cash_sales - $session->total_expenses
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
}
