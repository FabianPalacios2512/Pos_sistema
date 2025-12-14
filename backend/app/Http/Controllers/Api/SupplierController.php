<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $suppliers = Supplier::orderBy('name', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $suppliers,
                'message' => 'Proveedores obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener proveedores: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener proveedores con métricas analíticas
     */
    public function getAnalytics(): JsonResponse
    {
        try {
            // Obtener todos los proveedores
            $suppliers = Supplier::orderBy('name', 'asc')->get();

            // Para cada proveedor, obtener el conteo de productos
            $suppliersWithData = $suppliers->map(function ($supplier) {
                return [
                    'id' => $supplier->id,
                    'name' => $supplier->name,
                    'document' => $supplier->document,
                    'contact_person' => $supplier->contact_person,
                    'phone' => $supplier->phone,
                    'email' => $supplier->email,
                    'address' => $supplier->address,
                    'payment_terms' => $supplier->payment_terms,
                    'credit_limit' => $supplier->credit_limit,
                    'current_debt' => $supplier->current_debt,
                    'active' => $supplier->active,
                    'products_count' => $supplier->products()->count(),
                    'last_purchase_date' => $supplier->last_order_date,
                    'total_purchases_amount' => $supplier->total_purchased,
                    'purchase_orders_count' => $supplier->total_orders
                ];
            });

            // Calcular métricas generales
            $totalSuppliers = $suppliers->count();
            $activeSuppliers = $suppliers->where('active', true)->count();
            $totalDebt = $suppliers->sum('current_debt');
            $bestSupplier = $suppliers->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'suppliers' => $suppliersWithData,
                    'summary' => [
                        'total_suppliers' => $totalSuppliers,
                        'active_suppliers' => $activeSuppliers,
                        'total_debt' => $totalDebt,
                        'best_supplier' => $bestSupplier ? [
                            'id' => $bestSupplier->id,
                            'name' => $bestSupplier->name,
                            'total_purchases' => 0
                        ] : null
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener analytics de proveedores',
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'document' => 'required|string|max:255|unique:suppliers,document',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'contact_person' => 'nullable|string|max:255',
                'payment_terms' => 'nullable|in:immediate,15_days,30_days,45_days,60_days,90_days',
                'credit_limit' => 'nullable|numeric|min:0',
                'current_debt' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'active' => 'boolean'
            ]);

            $supplier = Supplier::create($validated);

            return response()->json([
                'success' => true,
                'data' => $supplier,
                'message' => 'Proveedor creado exitosamente'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear proveedor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $supplier = Supplier::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $supplier,
                'message' => 'Proveedor obtenido exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proveedor no encontrado'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $supplier = Supplier::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'document' => 'required|string|max:255|unique:suppliers,document,' . $id,
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'contact_person' => 'nullable|string|max:255',
                'payment_terms' => 'nullable|in:immediate,15_days,30_days,45_days,60_days,90_days',
                'credit_limit' => 'nullable|numeric|min:0',
                'current_debt' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'active' => 'boolean'
            ]);

            $supplier->update($validated);

            return response()->json([
                'success' => true,
                'data' => $supplier,
                'message' => 'Proveedor actualizado exitosamente'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar proveedor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Proveedor eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar proveedor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle supplier active status.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->active = !$supplier->active;
            $supplier->save();

            $status = $supplier->active ? 'habilitado' : 'inhabilitado';

            return response()->json([
                'success' => true,
                'data' => $supplier,
                'message' => "Proveedor {$status} exitosamente"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado del proveedor: ' . $e->getMessage()
            ], 500);
        }
    }
}
