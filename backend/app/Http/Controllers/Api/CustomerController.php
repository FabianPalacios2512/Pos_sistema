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
     */
    public function index(): JsonResponse
    {
        try {
            $customers = Customer::orderBy('name', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $customers,
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
                'country' => 'nullable|string|max:255',
                'document_type' => 'nullable|string|max:50',
                'document_number' => 'nullable|string|max:50',
                'birth_date' => 'nullable|date',
                'credit_limit' => 'nullable|numeric|min:0',
                'current_debt' => 'nullable|numeric|min:0',
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
     */
    public function show(string $id): JsonResponse
    {
        try {
            $customer = Customer::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $customer,
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
                'country' => 'nullable|string|max:255',
                'document_type' => 'nullable|string|max:50',
                'document_number' => 'nullable|string|max:50',
                'birth_date' => 'nullable|date',
                'credit_limit' => 'nullable|numeric|min:0',
                'current_debt' => 'nullable|numeric|min:0',
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
