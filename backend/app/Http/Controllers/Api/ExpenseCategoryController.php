<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseCategoryController extends Controller
{
    /**
     * Listar todas las categorías de gastos
     */
    public function index(Request $request)
    {
        try {
            $query = ExpenseCategory::query();

            // Filtro de búsqueda
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filtro de estado
            if ($request->filled('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            // Ordenamiento
            $sortField = $request->get('sort_by', 'name');
            $sortDirection = $request->get('sort_direction', 'asc');
            $query->orderBy($sortField, $sortDirection);

            // Paginación
            $perPage = $request->get('per_page', 15);
            $categories = $query->paginate($perPage);

            // Agregar conteo de gastos por categoría
            $categories->getCollection()->transform(function($category) {
                $category->expenses_count = $category->expenses()->count();
                $category->total_amount = $category->expenses()->sum('amount');
                return $category;
            });

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar categorías: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nueva categoría
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:expense_categories,name',
            'color' => 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.unique' => 'Ya existe una categoría con este nombre',
            'color.required' => 'El color es obligatorio',
            'color.regex' => 'El color debe ser un código hexadecimal válido (ej: #3B82F6)'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $category = ExpenseCategory::create([
                'name' => $request->name,
                'color' => $request->color,
                'description' => $request->description,
                'is_active' => $request->get('is_active', true)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Categoría creada exitosamente',
                'data' => $category
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener categoría específica
     */
    public function show($id)
    {
        try {
            $category = ExpenseCategory::findOrFail($id);

            // Agregar estadísticas
            $category->expenses_count = $category->expenses()->count();
            $category->total_amount = $category->expenses()->sum('amount');
            $category->recent_expenses = $category->expenses()
                ->with('user:id,name')
                ->orderBy('date', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $category
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        }
    }

    /**
     * Actualizar categoría
     */
    public function update(Request $request, $id)
    {
        $category = ExpenseCategory::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:expense_categories,name,' . $id,
            'color' => 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.unique' => 'Ya existe una categoría con este nombre',
            'color.required' => 'El color es obligatorio',
            'color.regex' => 'El color debe ser un código hexadecimal válido (ej: #3B82F6)'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $category->update([
                'name' => $request->name,
                'color' => $request->color,
                'description' => $request->description,
                'is_active' => $request->get('is_active', $category->is_active)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Categoría actualizada exitosamente',
                'data' => $category
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar categoría
     */
    public function destroy($id)
    {
        try {
            $category = ExpenseCategory::findOrFail($id);

            // Verificar si tiene gastos asociados
            $expensesCount = $category->expenses()->count();
            if ($expensesCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "No se puede eliminar la categoría porque tiene {$expensesCount} gastos asociados. Desactívala en su lugar."
                ], 422);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado activo/inactivo
     */
    public function toggleActive($id)
    {
        try {
            $category = ExpenseCategory::findOrFail($id);
            $category->is_active = !$category->is_active;
            $category->save();

            return response()->json([
                'success' => true,
                'message' => $category->is_active ? 'Categoría activada' : 'Categoría desactivada',
                'data' => $category
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de categorías
     */
    public function statistics()
    {
        try {
            $categories = ExpenseCategory::withCount('expenses')
                ->with(['expenses' => function($query) {
                    $query->selectRaw('category_id, SUM(amount) as total')
                          ->groupBy('category_id');
                }])
                ->get();

            $stats = [
                'total_categories' => $categories->count(),
                'active_categories' => $categories->where('is_active', true)->count(),
                'categories_with_expenses' => $categories->where('expenses_count', '>', 0)->count(),
                'top_categories' => $categories->sortByDesc('expenses_count')->take(5)->values(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }
}
