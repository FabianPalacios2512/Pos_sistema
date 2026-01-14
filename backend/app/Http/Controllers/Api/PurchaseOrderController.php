<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class PurchaseOrderController extends Controller
{
    /**
     * Listar todas las órdenes de compra
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = PurchaseOrder::with(['supplier', 'warehouse', 'items.product', 'items.variant', 'creator'])
                ->orderBy('order_date', 'desc');

            // Filtros
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            if ($request->has('supplier_id') && $request->supplier_id !== '') {
                $query->where('supplier_id', $request->supplier_id);
            }

            if ($request->has('date_from')) {
                $query->whereDate('order_date', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('order_date', '<=', $request->date_to);
            }

            $orders = $query->get();

            // Calcular métricas
            $metrics = [
                'total_orders' => $orders->count(),
                'pending_orders' => $orders->where('status', 'pending')->count(),
                'received_orders' => $orders->where('status', 'received')->count(),
                'draft_orders' => $orders->where('status', 'draft')->count(),
                'total_amount' => $orders->sum('total'),
                'pending_amount' => $orders->where('payment_status', '!=', 'paid')->sum(function($order) {
                    return $order->total - $order->paid_amount;
                })
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'orders' => $orders,
                    'metrics' => $metrics
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar órdenes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nueva orden de compra
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'supplier_id' => 'required|exists:suppliers,id',
                'warehouse_id' => 'nullable|exists:warehouses,id',
                'order_date' => 'required|date',
                'expected_date' => 'nullable|date|after_or_equal:order_date',
                'notes' => 'nullable|string',
                'reference' => 'nullable|string|max:255',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.variant_id' => 'nullable|exists:product_variants,id',  // 👗 NUEVO: para moda
                'items.*.variant_options' => 'nullable|array',                   // 👗 NUEVO: opciones de variante
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.unit_cost' => 'required|numeric|min:0'
            ]);

            DB::beginTransaction();

            // Crear orden
            $order = PurchaseOrder::create([
                'order_number' => PurchaseOrder::generateOrderNumber(),
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'] ?? null,
                'status' => 'draft',
                'order_date' => $validated['order_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'reference' => $validated['reference'] ?? null,
                'created_by' => Auth::id()
            ]);

            // Crear items
            $subtotal = 0;
            $tax = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $itemSubtotal = $item['quantity'] * $item['unit_cost'];
                $itemTax = 0; // Puedes calcular IVA aquí si es necesario

                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,           // 👗 NUEVO
                    'variant_options' => $item['variant_options'] ?? null, // 👗 NUEVO
                    'quantity_ordered' => $item['quantity'],
                    'unit' => $product->measurement_unit ?? 'unit',
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $itemSubtotal,
                    'tax_amount' => $itemTax,
                    'total' => $itemSubtotal + $itemTax,
                    'notes' => $item['notes'] ?? null
                ]);

                $subtotal += $itemSubtotal;
                $tax += $itemTax;
            }

            // Actualizar totales de la orden
            $order->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $subtotal + $tax
            ]);

            DB::commit();

            // Recargar con relaciones (incluir variante)
            $order->load(['supplier', 'items.product', 'items.variant', 'creator']);

            return response()->json([
                'success' => true,
                'message' => 'Orden de compra creada exitosamente',
                'data' => $order
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear orden: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver detalles de una orden
     */
    public function show($id): JsonResponse
    {
        try {
            $order = PurchaseOrder::with(['supplier', 'warehouse', 'items.product', 'items.variant', 'creator', 'receiver'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Orden no encontrada'
            ], 404);
        }
    }

    /**
     * Actualizar orden (solo si está en draft)
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $order = PurchaseOrder::findOrFail($id);

            if ($order->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden editar órdenes en borrador'
                ], 400);
            }

            $validated = $request->validate([
                'supplier_id' => 'required|exists:suppliers,id',
                'warehouse_id' => 'nullable|exists:warehouses,id',
                'expected_date' => 'nullable|date',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.unit_cost' => 'required|numeric|min:0'
            ]);

            DB::beginTransaction();

            // Actualizar orden
            $order->update([
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'] ?? null,
                'expected_date' => $validated['expected_date'] ?? null,
                'notes' => $validated['notes'] ?? null
            ]);

            // Eliminar items antiguos
            $order->items()->delete();

            // Crear nuevos items
            $subtotal = 0;
            $tax = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $itemSubtotal = $item['quantity'] * $item['unit_cost'];
                $itemTax = 0;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity'],
                    'unit' => $product->measurement_unit ?? 'unit',
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $itemSubtotal,
                    'tax_amount' => $itemTax,
                    'total' => $itemSubtotal + $itemTax
                ]);

                $subtotal += $itemSubtotal;
                $tax += $itemTax;
            }

            $order->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $subtotal + $tax
            ]);

            DB::commit();

            $order->load(['supplier', 'items.product']);

            return response()->json([
                'success' => true,
                'message' => 'Orden actualizada exitosamente',
                'data' => $order
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar orden: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado de la orden
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $order = PurchaseOrder::findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|in:draft,pending,received,cancelled'
            ]);

            $order->update(['status' => $validated['status']]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'data' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recibir mercancía (actualizar inventario)
     * 👗 Actualizado para soportar variantes de producto (moda)
     */
    public function receive(Request $request, $id): JsonResponse
    {
        try {
            $order = PurchaseOrder::with(['items.product', 'items.variant'])->findOrFail($id);

            if (!in_array($order->status, ['pending', 'partial'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden recibir órdenes pendientes'
                ], 400);
            }

            // Si no tiene bodega asignada, usar la principal (id: 1)
            if (!$order->warehouse_id) {
                $order->warehouse_id = 1;
                $order->save();
            }

            $validated = $request->validate([
                'received_items' => 'required|array',
                'received_items.*.item_id' => 'required|exists:purchase_order_items,id',
                'received_items.*.quantity' => 'required|numeric|min:0'
            ]);

            DB::beginTransaction();

            foreach ($validated['received_items'] as $receivedItem) {
                $item = PurchaseOrderItem::with(['product', 'variant'])->findOrFail($receivedItem['item_id']);
                $quantityToReceive = $receivedItem['quantity'];

                if ($quantityToReceive <= 0) continue;

                // Actualizar cantidad recibida
                $item->quantity_received += $quantityToReceive;
                $item->received = $item->quantity_received >= $item->quantity_ordered;
                $item->save();

                $product = $item->product;
                $previousStock = $product->current_stock;

                // 👗 MODA: Si tiene variant_id, actualizar stock de la variante
                if ($item->variant_id && $item->variant) {
                    $variant = $item->variant;
                    $previousVariantStock = $variant->stock ?? 0;

                    // Actualizar stock en product_variants
                    $variant->stock = ($variant->stock ?? 0) + $quantityToReceive;
                    $variant->save();

                    // Actualizar stock en product_warehouse para la variante
                    if ($order->warehouse_id && Schema::hasTable('product_warehouse')) {
                        try {
                            $warehouseVariant = DB::table('product_warehouse')
                                ->where('product_id', $product->id)
                                ->where('product_variant_id', $variant->id)
                                ->where('warehouse_id', $order->warehouse_id)
                                ->first();

                            if ($warehouseVariant) {
                                DB::table('product_warehouse')
                                    ->where('product_id', $product->id)
                                    ->where('product_variant_id', $variant->id)
                                    ->where('warehouse_id', $order->warehouse_id)
                                    ->increment('stock', $quantityToReceive);
                            } else {
                                DB::table('product_warehouse')->insert([
                                    'product_id' => $product->id,
                                    'product_variant_id' => $variant->id,
                                    'warehouse_id' => $order->warehouse_id,
                                    'stock' => $quantityToReceive,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                            }
                        } catch (\Exception $e) {
                            \Log::warning('No se pudo actualizar product_warehouse para variante: ' . $e->getMessage());
                        }
                    }

                    // Recalcular stock total del producto (suma de todas las variantes)
                    $totalStock = DB::table('product_variants')
                        ->where('product_id', $product->id)
                        ->sum('stock');
                    $product->current_stock = $totalStock;
                    $product->save();

                    // Crear movimiento de inventario con referencia a variante
                    $variantInfo = $item->variant_options
                        ? collect($item->variant_options)->map(fn($o) => "{$o['name']}: {$o['value']}")->join(', ')
                        : "Variante #{$variant->id}";

                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'supplier_id' => $order->supplier_id,
                        'type' => 'in',
                        'reason' => 'purchase',
                        'quantity' => $quantityToReceive,
                        'previous_stock' => $previousVariantStock,
                        'new_stock' => $variant->stock,
                        'unit_cost' => $item->unit_cost,
                        'total_cost' => $quantityToReceive * $item->unit_cost,
                        'unit_price' => $item->unit_cost,
                        'total_value' => $quantityToReceive * $item->unit_cost,
                        'reference' => 'PO-' . $order->order_number,
                        'notes' => "Recepción OC #{$order->order_number} - {$product->name} ({$variantInfo})",
                        'movement_date' => now(),
                        'user_id' => Auth::id()
                    ]);

                } else {
                    // Producto simple (sin variantes)
                    $product->current_stock += $quantityToReceive;
                    $product->save();

                    // Actualizar stock en product_warehouse
                    if ($order->warehouse_id && Schema::hasTable('product_warehouse')) {
                        try {
                            $warehouseProduct = DB::table('product_warehouse')
                                ->where('product_id', $product->id)
                                ->where('warehouse_id', $order->warehouse_id)
                                ->whereNull('product_variant_id')
                                ->first();

                            if ($warehouseProduct) {
                                DB::table('product_warehouse')
                                    ->where('product_id', $product->id)
                                    ->where('warehouse_id', $order->warehouse_id)
                                    ->whereNull('product_variant_id')
                                    ->increment('stock', $quantityToReceive);
                            } else {
                                DB::table('product_warehouse')->insert([
                                    'product_id' => $product->id,
                                    'warehouse_id' => $order->warehouse_id,
                                    'stock' => $quantityToReceive,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                            }
                        } catch (\Exception $e) {
                            \Log::warning('No se pudo actualizar product_warehouse: ' . $e->getMessage());
                        }
                    }

                    // Crear movimiento de inventario
                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'supplier_id' => $order->supplier_id,
                        'type' => 'in',
                        'reason' => 'purchase',
                        'quantity' => $quantityToReceive,
                        'previous_stock' => $previousStock,
                        'new_stock' => $product->current_stock,
                        'unit_cost' => $item->unit_cost,
                        'total_cost' => $quantityToReceive * $item->unit_cost,
                        'unit_price' => $item->unit_cost,
                        'total_value' => $quantityToReceive * $item->unit_cost,
                        'reference' => 'PO-' . $order->order_number,
                        'notes' => "Recepción de orden de compra #{$order->order_number}",
                        'movement_date' => now(),
                        'user_id' => Auth::id()
                    ]);
                }
            }

            // 🔄 Recargar items para asegurar que quantity_received esté actualizado
            $order->load(['items', 'items.variant']);

            // Actualizar estado de la orden
            if ($order->isFullyReceived()) {
                $order->status = 'received';
                $order->received_date = now();
            } else {
                $order->status = 'partial';
            }

            $order->received_by = Auth::id();
            $order->save();

            DB::commit();

            $order->load(['items.product', 'items.variant', 'supplier']);

            return response()->json([
                'success' => true,
                'message' => 'Mercancía recibida y inventario actualizado',
                'data' => $order
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al recibir mercancía: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar orden (solo draft)
     */
    public function destroy($id): JsonResponse
    {
        try {
            $order = PurchaseOrder::findOrFail($id);

            if ($order->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden eliminar órdenes en borrador'
                ], 400);
            }

            $order->delete();

            return response()->json([
                'success' => true,
                'message' => 'Orden eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar orden: ' . $e->getMessage()
            ], 500);
        }
    }
}
