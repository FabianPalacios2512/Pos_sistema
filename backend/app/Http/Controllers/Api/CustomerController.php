<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     * 🎯 Optimizado: Solo carga loyalty_points si el sistema está habilitado
     */
    public function index(): JsonResponse
    {
        try {
            // Verificar si el sistema de fidelización está habilitado
            $loyaltyEnabled = \App\Models\SystemSetting::getSettings()->enable_loyalty_system ?? false;

            // Seleccionar campos dinámicamente para evitar cargas innecesarias
            $selectFields = [
                'id', 'name', 'email', 'phone', 'address', 'city',
                'document_type', 'document_number', 'birth_date', 'gender',
                'credit_limit', 'current_debt', 'credit_active', 'active',
                'total_purchases', 'total_orders', 'created_at', 'updated_at'
            ];

            // Solo agregar loyalty_points si el sistema está habilitado
            if ($loyaltyEnabled) {
                $selectFields[] = 'loyalty_points';
            }

            $customers = Customer::select($selectFields)
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $customers,
                'loyalty_enabled' => $loyaltyEnabled, // Indicar al frontend si está habilitado
                'message' => 'Clientes obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener clientes: ' . $e->getMessage()
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
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:255',
                'document_type' => 'nullable|string|max:50',
                'document_number' => 'nullable|string|max:50',
                'birth_date' => 'nullable|date',
                'credit_limit' => 'nullable|numeric|min:0',
                'current_debt' => 'nullable|numeric|min:0',
                'credit_active' => 'boolean',
                'active' => 'boolean'
            ]);

            $customer = Customer::create($validated);

            return response()->json([
                'success' => true,
                'data' => $customer,
                'message' => 'Cliente creado exitosamente'
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
                'message' => 'Error al crear cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * 🎯 Optimizado: Solo carga loyalty_points si el sistema está habilitado
     */
    public function show(string $id): JsonResponse
    {
        try {
            // Verificar si el sistema de fidelización está habilitado
            $loyaltyEnabled = \App\Models\SystemSetting::getSettings()->enable_loyalty_system ?? false;

            // Seleccionar campos dinámicamente
            $selectFields = [
                'id', 'name', 'email', 'phone', 'address', 'city',
                'document_type', 'document_number', 'birth_date', 'gender',
                'credit_limit', 'current_debt', 'credit_active', 'active',
                'total_purchases', 'total_orders', 'created_at', 'updated_at'
            ];

            if ($loyaltyEnabled) {
                $selectFields[] = 'loyalty_points';
            }

            $customer = Customer::select($selectFields)->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $customer,
                'loyalty_enabled' => $loyaltyEnabled,
                'message' => 'Cliente obtenido exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $customer = Customer::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:255',
                'document_type' => 'nullable|string|max:50',
                'document_number' => 'nullable|string|max:50',
                'birth_date' => 'nullable|date',
                'credit_limit' => 'nullable|numeric|min:0',
                'current_debt' => 'nullable|numeric|min:0',
                'credit_active' => 'boolean',
                'active' => 'boolean'
            ]);

            $customer->update($validated);

            return response()->json([
                'success' => true,
                'data' => $customer,
                'message' => 'Cliente actualizado exitosamente'
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
                'message' => 'Error al actualizar cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cliente eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar cliente: ' . $e->getMessage()
            ], 500);
        }
    }
}
