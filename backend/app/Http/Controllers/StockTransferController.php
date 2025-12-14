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
        \Log::info('=== INDEX TRANSFERS ===');
        \Log::info('Request params:', $request->all());

        $query = StockTransfer::with([
            'sourceWarehouse',
            'destinationWarehouse',
            'user',
            'items.product'
        ]);

        // Filtros
        if ($request->has('status')) {
            $query->where('status', $request->status);
            \Log::info('Filtering by status:', $request->status);
        }

        if ($request->has('source_warehouse_id')) {
            $query->where('source_warehouse_id', $request->source_warehouse_id);
            \Log::info('Filtering by source:', $request->source_warehouse_id);
        }

        if ($request->has('destination_warehouse_id')) {
            $query->where('destination_warehouse_id', $request->destination_warehouse_id);
            \Log::info('Filtering by destination:', $request->destination_warehouse_id);
        }

        \Log::info('Query SQL:', ['sql' => $query->toSql()]);

        $transfers = $query->orderBy('created_at', 'desc')->paginate(20);

        \Log::info('Query results:', [
            'total' => $transfers->total(),
            'count' => $transfers->count(),
            'per_page' => $transfers->perPage()
        ]);

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
        \Log::info('=== INICIO STORE TRANSFER ===');
        \Log::info('Request data:', $request->all());

        $validated = $request->validate([
            'source_warehouse_id' => 'required|exists:warehouses,id',
            'destination_warehouse_id' => 'required|exists:warehouses,id|different:source_warehouse_id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        \Log::info('Validation passed:', $validated);

        DB::beginTransaction();
        \Log::info('Transaction started');

        try {
            $sourceWarehouse = Warehouse::findOrFail($validated['source_warehouse_id']);
            \Log::info('Source warehouse found:', ['id' => $sourceWarehouse->id, 'name' => $sourceWarehouse->name]);

            // Validar que haya suficiente stock en origen
            foreach ($validated['items'] as $item) {
                $availableStock = $sourceWarehouse->getProductStock($item['product_id']);
                \Log::info('Stock validation:', [
                    'product_id' => $item['product_id'],
                    'available' => $availableStock,
                    'requested' => $item['quantity']
                ]);

                if ($availableStock < $item['quantity']) {
                    $product = Product::find($item['product_id']);
                    \Log::error('Insufficient stock:', [
                        'product' => $product->name,
                        'available' => $availableStock,
                        'requested' => $item['quantity']
                    ]);
                    throw new \Exception("Stock insuficiente para {$product->name}. Disponible: {$availableStock}, Solicitado: {$item['quantity']}");
                }
            }

            \Log::info('Stock validation passed, creating transfer...');

            // Crear el traslado
            $transfer = StockTransfer::create([
                'source_warehouse_id' => $validated['source_warehouse_id'],
                'destination_warehouse_id' => $validated['destination_warehouse_id'],
                'user_id' => auth()->id(),
                'reference_number' => StockTransfer::generateReferenceNumber(),
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);

            \Log::info('Transfer created:', [
                'id' => $transfer->id,
                'reference' => $transfer->reference_number,
                'status' => $transfer->status
            ]);

            // Agregar los items
            foreach ($validated['items'] as $item) {
                $transferItem = StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);

                \Log::info('Transfer item created:', [
                    'id' => $transferItem->id,
                    'product_id' => $transferItem->product_id,
                    'quantity' => $transferItem->quantity
                ]);
            }

            \Log::info('All items created, committing transaction...');
            DB::commit();
            \Log::info('Transaction committed successfully');

            $transferWithRelations = $transfer->load(['sourceWarehouse', 'destinationWarehouse', 'items.product']);
            \Log::info('Transfer loaded with relations:', [
                'transfer_id' => $transferWithRelations->id,
                'items_count' => $transferWithRelations->items->count()
            ]);

            return response()->json([
                'message' => 'Traslado creado exitosamente',
                'transfer' => $transferWithRelations
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Exception caught, rolling back transaction:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            DB::rollBack();
            \Log::info('Transaction rolled back');

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
        \Log::info('=== COMPLETE TRANSFER ===');
        \Log::info('Transfer ID:', ['id' => $id]);

        DB::beginTransaction();
        try {
            $transfer = StockTransfer::with('items')->findOrFail($id);
            \Log::info('Transfer found:', [
                'id' => $transfer->id,
                'status' => $transfer->status,
                'items_count' => $transfer->items->count()
            ]);

            if ($transfer->status !== 'pending') {
                \Log::warning('Transfer is not pending:', ['status' => $transfer->status]);
                return response()->json([
                    'message' => 'Solo se pueden completar traslados pendientes'
                ], 422);
            }

            \Log::info('Calling transfer->complete()...');
            $transfer->complete();
            \Log::info('Transfer completed successfully');

            // Actualizar el current_stock de cada producto afectado
            \Log::info('Updating current_stock for products...');
            foreach ($transfer->items as $item) {
                $product = Product::find($item->product_id);
                $totalStock = $product->warehouses()->sum('product_warehouse.stock');
                \Log::info('Updating product stock:', [
                    'product_id' => $product->id,
                    'old_stock' => $product->current_stock,
                    'new_stock' => $totalStock
                ]);
                $product->update(['current_stock' => $totalStock]);
            }

            DB::commit();
            \Log::info('Transaction committed');

            return response()->json([
                'message' => 'Traslado completado exitosamente',
                'transfer' => $transfer->fresh(['sourceWarehouse', 'destinationWarehouse', 'items.product'])
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception in complete transfer:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
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
