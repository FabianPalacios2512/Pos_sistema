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
            // Obtener todos los proveedores con sus órdenes de compra
            $suppliers = Supplier::with(['products'])->orderBy('name', 'asc')->get();

            // Para cada proveedor, obtener datos reales de órdenes de compra
            $suppliersWithData = $suppliers->map(function ($supplier) {
                // Obtener última orden de compra
                $lastOrder = \DB::table('purchase_orders')
                    ->where('supplier_id', $supplier->id)
                    ->whereNull('deleted_at')
                    ->orderBy('order_date', 'desc')
                    ->first();

                // Contar órdenes totales
                $ordersCount = \DB::table('purchase_orders')
                    ->where('supplier_id', $supplier->id)
                    ->whereNull('deleted_at')
                    ->count();

                // Total comprado
                $totalPurchased = \DB::table('purchase_orders')
                    ->where('supplier_id', $supplier->id)
                    ->whereNull('deleted_at')
                    ->where('status', '!=', 'cancelled')
                    ->sum('total');

                // Órdenes pendientes
                $pendingOrders = \DB::table('purchase_orders')
                    ->where('supplier_id', $supplier->id)
                    ->whereNull('deleted_at')
                    ->whereIn('status', ['draft', 'pending', 'ordered'])
                    ->count();

                return [
                    'id' => $supplier->id,
                    'name' => $supplier->name,
                    'document' => $supplier->document,
                    'contact_person' => $supplier->contact_person,
                    'phone' => $supplier->phone,
                    'email' => $supplier->email,
                    'address' => $supplier->address,
                    'city' => $supplier->city ?? null,
                    'payment_terms' => $supplier->payment_terms,
                    'credit_limit' => floatval($supplier->credit_limit ?? 0),
                    'current_debt' => floatval($supplier->current_debt ?? 0),
                    'active' => $supplier->active,
                    'products_count' => $supplier->products()->count(),
                    // Datos reales de órdenes de compra
                    'last_order_date' => $lastOrder ? $lastOrder->order_date : null,
                    'last_order_number' => $lastOrder ? $lastOrder->order_number : null,
                    'last_order_total' => $lastOrder ? floatval($lastOrder->total) : null,
                    'total_orders' => $ordersCount,
                    'total_purchased' => floatval($totalPurchased),
                    'pending_orders' => $pendingOrders,
                    'notes' => $supplier->notes
                ];
            });

            // Calcular métricas generales
            $totalSuppliers = $suppliers->count();
            $activeSuppliers = $suppliers->where('active', true)->count();
            $totalDebt = $suppliers->sum('current_debt');

            // Mejor proveedor (el que más compras tiene)
            $bestSupplierData = $suppliersWithData->sortByDesc('total_purchased')->first();

            // Total de órdenes pendientes
            $totalPendingOrders = \DB::table('purchase_orders')
                ->whereNull('deleted_at')
                ->whereIn('status', ['draft', 'pending', 'ordered'])
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'suppliers' => $suppliersWithData,
                    'summary' => [
                        'total_suppliers' => $totalSuppliers,
                        'active_suppliers' => $activeSuppliers,
                        'total_debt' => floatval($totalDebt),
                        'total_pending_orders' => $totalPendingOrders,
                        'best_supplier' => $bestSupplierData ? [
                            'id' => $bestSupplierData['id'],
                            'name' => $bestSupplierData['name'],
                            'total_purchases' => $bestSupplierData['total_purchased']
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
