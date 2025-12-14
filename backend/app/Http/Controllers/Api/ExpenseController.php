<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\CashSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Listar gastos con filtros
     */
    public function index(Request $request)
    {
        try {
            $query = Expense::with(['user:id,name', 'category:id,name,color', 'cashSession:id,opened_at']);

            // Filtros
            if ($request->has('category_id') && $request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('payment_method') && $request->payment_method) {
                $query->where('payment_method', $request->payment_method);
            }

            if ($request->has('start_date') && $request->start_date) {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date) {
                $query->whereDate('date', '<=', $request->end_date);
            }

            if ($request->has('user_id') && $request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            // Búsqueda por descripción o proveedor
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('supplier', 'like', "%{$search}%")
                      ->orWhere('receipt_number', 'like', "%{$search}%");
                });
            }

            // Ordenamiento
            $sortBy = $request->get('sort_by', 'date');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginación
            $perPage = $request->get('per_page', 15);
            $expenses = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $expenses,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener gastos',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Crear un nuevo gasto
     */
    public function store(Request $request)
    {
        try {
            // Validación
            $validator = Validator::make($request->all(), [
                'category_id' => 'required|exists:expense_categories,id',
                'amount' => 'required|numeric|min:0.01|max:9999999999999',
                'description' => 'required|string|max:1000',
                'payment_method' => 'required|in:efectivo,transferencia,tarjeta',
                'expense_source' => 'nullable|in:caja,general', // Nueva validación
                'date' => 'nullable|date',
                'receipt_number' => 'nullable|string|max:50',
                'supplier' => 'nullable|string|max:100',
                'notes' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                // Construir mensaje de error más descriptivo para el usuario
                $errorMessages = [];
                foreach ($validator->errors()->messages() as $field => $messages) {
                    $fieldName = match($field) {
                        'category_id' => 'Categoría',
                        'amount' => 'Monto',
                        'description' => 'Descripción',
                        'payment_method' => 'Método de pago',
                        'date' => 'Fecha',
                        'receipt_number' => 'Número de recibo',
                        'supplier' => 'Proveedor',
                        'notes' => 'Notas',
                        default => ucfirst($field)
                    };
                    foreach ($messages as $message) {
                        $errorMessages[] = str_replace($field, $fieldName, $message);
                    }
                }

                return response()->json([
                    'success' => false,
                    'message' => implode('. ', $errorMessages),
                    'errors' => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();

            $data = $validator->validated();
            $data['user_id'] = auth()->id();
            $data['date'] = $data['date'] ?? now();

            // LÓGICA CON FUENTE: Respetar la elección del usuario
            if ($data['payment_method'] === 'efectivo') {
                $expenseSource = $request->input('expense_source', 'general'); // Por defecto: general

                if ($expenseSource === 'caja') {
                    // Usuario quiere descontar de la caja actual
                    $cashSession = CashSession::where('user_id', auth()->id())
                        ->where('status', CashSession::STATUS_OPEN)
                        ->first();

                    if (!$cashSession) {
                        return response()->json([
                            'success' => false,
                            'message' => "No tienes una caja abierta. Abre una caja primero o registra el gasto como 'Gasto general'.",
                        ], 400);
                    }

                    // Verificar fondos disponibles
                    $availableCash = $cashSession->opening_amount + $cashSession->cash_sales;

                    if ($data['amount'] > $availableCash) {
                        return response()->json([
                            'success' => false,
                            'message' => "No hay suficiente efectivo en caja. Disponible: $" . number_format($availableCash, 2) . ". Selecciona 'Gasto general' o usa otro método de pago.",
                        ], 400);
                    }

                    // Vincular gasto a la caja abierta
                    $data['cash_session_id'] = $cashSession->id;
                } else {
                    // Gasto general: no vincular a caja
                    $data['cash_session_id'] = null;
                    // Nota: Estos gastos se registran pero no se descuentan de ninguna caja
                }
            } else {
                // Otros métodos de pago (transferencia, tarjeta) nunca se vinculan a caja
                $data['cash_session_id'] = null;
            }

            $expense = Expense::create($data);

            // Cargar relaciones
            $expense->load(['user:id,name', 'category:id,name,color', 'cashSession:id,opened_at']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Gasto registrado exitosamente',
                'data' => $expense,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el gasto',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mostrar un gasto específico
     */
    public function show($id)
    {
        try {
            $expense = Expense::with(['user:id,name,email', 'category', 'cashSession'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $expense,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gasto no encontrado',
            ], 404);
        }
    }

    /**
     * Actualizar un gasto
     */
    public function update(Request $request, $id)
    {
        try {
            $expense = Expense::findOrFail($id);

            // Validación
            $validator = Validator::make($request->all(), [
                'category_id' => 'sometimes|exists:expense_categories,id',
                'amount' => 'sometimes|numeric|min:0.01|max:9999999999999',
                'description' => 'sometimes|string|max:1000',
                'payment_method' => 'sometimes|in:efectivo,transferencia,tarjeta',
                'date' => 'sometimes|date',
                'receipt_number' => 'nullable|string|max:50',
                'supplier' => 'nullable|string|max:100',
                'notes' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                // Construir mensaje de error más descriptivo
                $errorMessages = [];
                foreach ($validator->errors()->messages() as $field => $messages) {
                    $fieldName = match($field) {
                        'category_id' => 'Categoría',
                        'amount' => 'Monto',
                        'description' => 'Descripción',
                        'payment_method' => 'Método de pago',
                        'date' => 'Fecha',
                        'receipt_number' => 'Número de recibo',
                        'supplier' => 'Proveedor',
                        'notes' => 'Notas',
                        default => ucfirst($field)
                    };
                    foreach ($messages as $message) {
                        $errorMessages[] = str_replace($field, $fieldName, $message);
                    }
                }

                return response()->json([
                    'success' => false,
                    'message' => implode('. ', $errorMessages),
                    'errors' => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();

            $data = $validator->validated();

            // Si se cambia el método de pago a efectivo
            if (isset($data['payment_method']) && $data['payment_method'] === 'efectivo' && $expense->payment_method !== 'efectivo') {
                $cashSession = CashSession::where('user_id', auth()->id())
                    ->where('status', CashSession::STATUS_OPEN)
                    ->first();

                if (!$cashSession) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes una caja abierta',
                    ], 400);
                }

                $data['cash_session_id'] = $cashSession->id;
            } elseif (isset($data['payment_method']) && $data['payment_method'] !== 'efectivo') {
                $data['cash_session_id'] = null;
            }

            $expense->update($data);
            $expense->load(['user:id,name', 'category:id,name,color', 'cashSession:id,opened_at']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Gasto actualizado exitosamente',
                'data' => $expense,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el gasto',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eliminar un gasto
     */
    public function destroy($id)
    {
        try {
            $expense = Expense::findOrFail($id);
            $expense->delete();

            return response()->json([
                'success' => true,
                'message' => 'Gasto eliminado exitosamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el gasto',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de gastos
     */
    public function statistics(Request $request)
    {
        try {
            $startDate = $request->get('start_date', now()->startOfMonth());
            $endDate = $request->get('end_date', now()->endOfMonth());

            // Total de gastos
            $totalExpenses = Expense::betweenDates($startDate, $endDate)->sum('amount');

            // Gastos por categoría
            $expensesByCategory = Expense::betweenDates($startDate, $endDate)
                ->select('category_id', DB::raw('SUM(amount) as total'))
                ->with('category:id,name,color')
                ->groupBy('category_id')
                ->get();

            // Gastos por método de pago
            $expensesByPaymentMethod = Expense::betweenDates($startDate, $endDate)
                ->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
                ->groupBy('payment_method')
                ->get();

            // Gastos del mes actual vs mes anterior
            $currentMonthTotal = Expense::currentMonth()->sum('amount');
            $previousMonthTotal = Expense::whereBetween('date', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ])->sum('amount');

            $percentageChange = $previousMonthTotal > 0
                ? (($currentMonthTotal - $previousMonthTotal) / $previousMonthTotal) * 100
                : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'total_expenses' => $totalExpenses,
                    'by_category' => $expensesByCategory,
                    'by_payment_method' => $expensesByPaymentMethod,
                    'current_month' => $currentMonthTotal,
                    'previous_month' => $previousMonthTotal,
                    'percentage_change' => round($percentageChange, 2),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener todas las categorías activas
     */
    public function getCategories()
    {
        try {
            $categories = ExpenseCategory::active()->get();

            return response()->json([
                'success' => true,
                'data' => $categories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener categorías',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verificar si hay caja abierta para el usuario actual
     */
    public function checkCashSession()
    {
        try {
            $cashSession = CashSession::where('user_id', auth()->id())
                ->where('status', CashSession::STATUS_OPEN)
                ->first();

            return response()->json([
                'success' => true,
                'has_open_session' => $cashSession !== null,
                'session' => $cashSession,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar sesión de caja',
            ], 500);
        }
    }
}
