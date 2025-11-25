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
            $suppliers = \DB::table('suppliers as s')
                ->select([
                    's.id',
                    's.name',
                    's.contact_name',
                    's.phone',
                    's.email',
                    's.city',
                    's.credit_limit',
                    's.current_debt',
                    's.active',
                    \DB::raw('COUNT(DISTINCT p.id) as products_count'),
                    \DB::raw('MAX(i.date) as last_purchase_date'),
                    \DB::raw('COALESCE(SUM(ii.quantity * ii.unit_price), 0) as total_purchases_amount'),
                    \DB::raw('COUNT(DISTINCT i.id) as purchase_orders_count')
                ])
                ->leftJoin('products as p', 's.id', '=', 'p.supplier_id')
                ->leftJoin('invoice_items as ii', 'p.id', '=', 'ii.product_id')
                ->leftJoin('invoices as i', function($join) {
                    $join->on('ii.invoice_id', '=', 'i.id')
                         ->where('i.type', '=', 'invoice')
                         ->where('i.status', '=', 'paid');
                })
                ->groupBy([
                    's.id', 's.name', 's.contact_name', 's.phone', 's.email',
                    's.city', 's.credit_limit', 's.current_debt', 's.active'
                ])
                ->orderBy('total_purchases_amount', 'desc')
                ->get();

            // Calcular métricas generales
            $totalSuppliers = $suppliers->count();
            $activeSuppliers = $suppliers->where('active', 1)->count();
            $totalDebt = $suppliers->sum('current_debt');
            $bestSupplier = $suppliers->sortByDesc('total_purchases_amount')->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'suppliers' => $suppliers,
                    'summary' => [
                        'total_suppliers' => $totalSuppliers,
                        'active_suppliers' => $activeSuppliers,
                        'total_debt' => $totalDebt,
                        'best_supplier' => $bestSupplier ? [
                            'id' => $bestSupplier->id,
                            'name' => $bestSupplier->name,
                            'total_purchases' => $bestSupplier->total_purchases_amount
                        ] : null
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener analytics de proveedores',
                'error' => $e->getMessage()
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
                'contact_name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'tax_id' => 'nullable|string|max:50',
                'credit_limit' => 'nullable|numeric|min:0',
                'current_debt' => 'nullable|numeric|min:0',
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
                'contact_name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'tax_id' => 'nullable|string|max:50',
                'credit_limit' => 'nullable|numeric|min:0',
                'current_debt' => 'nullable|numeric|min:0',
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
