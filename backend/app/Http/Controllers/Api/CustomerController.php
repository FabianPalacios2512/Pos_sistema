<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CreditPortalController;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Customer::query();

            // Filtro por búsqueda
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('document', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            // Filtro por estado
            if ($request->has('active')) {
                $query->where('active', $request->active);
            }

            // Ordenar
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            $customers = $query->get();

            return response()->json([
                'success' => true,
                'data' => $customers
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting customers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener clientes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'document_type' => 'nullable|string|max:10',
                'document_number' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:255',
                'credit_limit' => 'nullable|numeric|min:0',
                'credit_active' => 'nullable|boolean',
                'active' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Preparar datos para creación
            $customerData = $request->all();

            // Asegurar valores por defecto
            $customerData['active'] = $customerData['active'] ?? true;
            $customerData['credit_limit'] = $customerData['credit_limit'] ?? 0;
            $customerData['current_debt'] = 0;
            $customerData['total_purchases'] = 0;
            $customerData['total_orders'] = 0;
            $customerData['loyalty_points'] = 0;

            // 🎯 Generar ID y Token de crédito si tiene crédito activo
            if (($customerData['credit_active'] ?? false) || ($customerData['credit_limit'] ?? 0) > 0) {
                $customerData['credit_active'] = true;
                $customerData['credit_id'] = CreditPortalController::generateCreditId();
                $customerData['credit_access_token'] = CreditPortalController::generateAccessToken();
            }

            $customer = Customer::create($customerData);

            return response()->json([
                'success' => true,
                'message' => 'Cliente creado exitosamente',
                'data' => $customer
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating customer', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al crear cliente',
                'error' => $e->getMessage(),
                'details' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $customer = Customer::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting customer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $customer = Customer::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'document' => 'nullable|string|max:50|unique:customers,document,' . $id,
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
                'address' => 'nullable|string|max:500',
                'credit_limit' => 'nullable|numeric|min:0',
                'credit_days' => 'nullable|integer|min:0',
                'active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = $request->all();

            // 🎯 Generar ID y Token de crédito si se activa crédito y no tiene
            $creditActivating = ($updateData['credit_active'] ?? false) || ($updateData['credit_limit'] ?? 0) > 0;
            $noTieneCreditId = empty($customer->credit_id);

            if ($creditActivating && $noTieneCreditId) {
                $updateData['credit_active'] = true;
                $updateData['credit_id'] = CreditPortalController::generateCreditId();
                $updateData['credit_access_token'] = CreditPortalController::generateAccessToken();
            }

            $customer->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Cliente actualizado exitosamente',
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating customer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);

            // 🛡️ PROTECCIÓN: No permitir eliminar al Consumidor Final (NIT DIAN)
            if ($customer->document_number === '222222222222') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar al Consumidor Final. Este es un cliente especial del sistema requerido por la normativa DIAN.',
                    'is_system_customer' => true
                ], 403);
            }

            // Verificar si el cliente tiene facturas asociadas
            $invoicesCount = DB::table('invoices')
                ->where('customer_id', $id)
                ->count();

            if ($invoicesCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el cliente porque tiene facturas asociadas',
                    'has_invoices' => true,
                    'invoices_count' => $invoicesCount
                ], 400);
            }

            $customer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cliente eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting customer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer invoices
     */
    public function getInvoices($customer)
    {
        try {
            $invoices = DB::table('invoices')
                ->where('customer_id', $customer)
                ->select('id', 'number', 'date', 'total', 'status')
                ->orderBy('date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $invoices
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting customer invoices: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener facturas del cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if document exists
     */
    public function checkDocument(Request $request)
    {
        try {
            // 🔍 BÚSQUEDA OPCIONAL: No es obligatorio encontrar el cliente
            $validator = Validator::make($request->all(), [
                'document_number' => 'nullable|string',
                'document' => 'nullable|string',
                'exclude_id' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                // ❌ Si la validación falla, devolver éxito con exists=false (no bloquear)
                return response()->json([
                    'success' => true,
                    'exists' => false,
                    'data' => null
                ]);
            }

            // Soportar tanto 'document' como 'document_number'
            $documentValue = $request->document_number ?? $request->document;

            if (!$documentValue) {
                return response()->json([
                    'success' => true,
                    'exists' => false,
                    'data' => null
                ]);
            }

            $query = Customer::where('document_number', $documentValue);

            if ($request->has('exclude_id')) {
                $query->where('id', '!=', $request->exclude_id);
            }

            $customer = $query->first();
            $exists = $customer !== null;

            return response()->json([
                'success' => true,
                'exists' => $exists,
                'data' => $exists ? $customer : null
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking document: ' . $e->getMessage());
            // ⚠️ Incluso con error, devolver éxito con exists=false (búsqueda opcional)
            return response()->json([
                'success' => true,
                'exists' => false,
                'data' => null
            ]);
        }
    }

    /**
     * 🗑️ Eliminar crédito de un cliente
     * Solo permite eliminar si el cliente está al día (sin deuda)
     */
    public function deleteCredit($id)
    {
        try {
            $customer = Customer::findOrFail($id);

            // Verificar que no tenga deuda pendiente
            $currentDebt = $customer->balance ?? 0;

            // 🔧 FIX: Verificar en la tabla INVOICES (no sales) las facturas a crédito no pagadas
            $pendingCredit = \App\Models\Invoice::where('customer_id', $id)
                ->where('payment_method', 'credit')
                ->whereNotIn('status', ['paid', 'cancelled', 'returned'])
                ->sum('total');

            if ($currentDebt > 0 || $pendingCredit > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el crédito. El cliente tiene deuda pendiente de $' . number_format($currentDebt + $pendingCredit, 0, ',', '.')
                ], 400);
            }

            // Eliminar foto si existe
            if ($customer->credit_photo) {
                $photoPath = str_replace('/storage/', '', $customer->credit_photo);
                \Storage::disk('public')->delete($photoPath);
            }

            // 🔧 FIX: Limpiar datos de crédito del cliente (credit_active, NO has_credit)
            $customer->update([
                'credit_limit' => 0,
                'balance' => 0,
                'credit_photo' => null,
                'credit_active' => false  // ✅ Columna correcta en la BD
            ]);

            // Opcional: Eliminar historial de pagos de crédito
            // \App\Models\CreditPayment::where('customer_id', $id)->delete();

            Log::info("🗑️ Crédito eliminado para cliente ID: {$id}, Nombre: {$customer->name}");

            return response()->json([
                'success' => true,
                'message' => 'Crédito eliminado correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error eliminando crédito: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el crédito: ' . $e->getMessage()
            ], 500);
        }
    }
}
