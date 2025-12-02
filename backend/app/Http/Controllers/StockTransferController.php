<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    /**
     * Listar todos los traslados
     */
    public function index(Request $request)
    {
        $query = StockTransfer::with([
            'sourceWarehouse',
            'destinationWarehouse',
            'user',
            'items.product'
        ]);

        // Filtros
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('source_warehouse_id')) {
            $query->where('source_warehouse_id', $request->source_warehouse_id);
        }

        if ($request->has('destination_warehouse_id')) {
            $query->where('destination_warehouse_id', $request->destination_warehouse_id);
        }

        $transfers = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($transfers);
    }

    /**
     * Ver un traslado específico
     */
    public function show($id)
    {
        $transfer = StockTransfer::with([
            'sourceWarehouse',
            'destinationWarehouse',
            'user',
            'items.product'
        ])->findOrFail($id);

        return response()->json($transfer);
    }

    /**
     * Crear un nuevo traslado
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_warehouse_id' => 'required|exists:warehouses,id',
            'destination_warehouse_id' => 'required|exists:warehouses,id|different:source_warehouse_id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $sourceWarehouse = Warehouse::findOrFail($validated['source_warehouse_id']);

            // Validar que haya suficiente stock en origen
            foreach ($validated['items'] as $item) {
                $availableStock = $sourceWarehouse->getProductStock($item['product_id']);
                if ($availableStock < $item['quantity']) {
                    $product = Product::find($item['product_id']);
                    throw new \Exception("Stock insuficiente para {$product->name}. Disponible: {$availableStock}, Solicitado: {$item['quantity']}");
                }
            }

            // Crear el traslado
            $transfer = StockTransfer::create([
                'source_warehouse_id' => $validated['source_warehouse_id'],
                'destination_warehouse_id' => $validated['destination_warehouse_id'],
                'user_id' => auth()->id(),
                'reference_number' => StockTransfer::generateReferenceNumber(),
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);

            // Agregar los items
            foreach ($validated['items'] as $item) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Traslado creado exitosamente',
                'transfer' => $transfer->load(['sourceWarehouse', 'destinationWarehouse', 'items.product'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear el traslado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Completar un traslado (ejecutar el movimiento de stock)
     */
    public function complete($id)
    {
        DB::beginTransaction();
        try {
            $transfer = StockTransfer::with('items')->findOrFail($id);

            if ($transfer->status !== 'pending') {
                return response()->json([
                    'message' => 'Solo se pueden completar traslados pendientes'
                ], 422);
            }

            $transfer->complete();

            // Actualizar el current_stock de cada producto afectado
            foreach ($transfer->items as $item) {
                $product = Product::find($item->product_id);
                $totalStock = $product->warehouses()->sum('product_warehouse.stock');
                $product->update(['current_stock' => $totalStock]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Traslado completado exitosamente',
                'transfer' => $transfer->fresh(['sourceWarehouse', 'destinationWarehouse', 'items.product'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al completar el traslado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancelar un traslado
     */
    public function cancel($id)
    {
        DB::beginTransaction();
        try {
            $transfer = StockTransfer::findOrFail($id);

            if ($transfer->status !== 'pending') {
                return response()->json([
                    'message' => 'Solo se pueden cancelar traslados pendientes'
                ], 422);
            }

            $transfer->update(['status' => 'cancelled']);

            DB::commit();

            return response()->json([
                'message' => 'Traslado cancelado exitosamente',
                'transfer' => $transfer
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al cancelar el traslado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un traslado (solo si está cancelado o pendiente)
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $transfer = StockTransfer::findOrFail($id);

            if ($transfer->status === 'completed') {
                return response()->json([
                    'message' => 'No se pueden eliminar traslados completados'
                ], 422);
            }

            $transfer->delete();

            DB::commit();

            return response()->json([
                'message' => 'Traslado eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al eliminar el traslado',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
