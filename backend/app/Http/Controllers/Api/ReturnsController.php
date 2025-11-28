<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductReturn;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\InventoryMovement;
use App\Models\CashSession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReturnsController extends Controller
{
    /**
     * Obtener lista de devoluciones
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // No incluir returnItems.product ya que no existe la tabla return_items
            $query = ProductReturn::with(['originalInvoice', 'customer', 'user', 'cashSession'])
                ->orderBy('created_at', 'desc');

            // Filtros
            if ($request->has('date_from') && $request->has('date_to')) {
                $query->byDateRange($request->date_from, $request->date_to);
            }

            if ($request->has('status')) {
                $query->byStatus($request->status);
            }

            if ($request->has('cash_session_id')) {
                $query->byCashSession($request->cash_session_id);
            }

            $returns = $query->paginate($request->get('per_page', 15));

            // Procesar cada devolución para incluir información de productos
            $returns->getCollection()->transform(function ($return) {
                // Asegurar que items es un array
                $items = is_string($return->items) ? json_decode($return->items, true) : $return->items;
                if (!is_array($items)) {
                    $items = [];
                }

                // Agregar información de productos a cada item
                $return->return_items = collect($items)->map(function ($item) {
                    if (isset($item['product_id'])) {
                        $product = Product::find($item['product_id']);
                        $item['product'] = $product;
                    }
                    return $item;
                })->toArray();

                return $return;
            });

            return response()->json([
                'success' => true,
                'data' => $returns
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener devoluciones',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar factura para devolución
     */
    public function searchInvoice(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'invoice_number' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Número de factura requerido',
                    'errors' => $validator->errors()
                ], 422);
            }

            $invoice = Invoice::with(['customer', 'invoiceItems.product'])
                ->where('number', $request->invoice_number)
                ->where('status', 'paid')
                ->first();

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Factura no encontrada o no está pagada'
                ], 404);
            }

            // Obtener devoluciones previas para esta factura (solo para información)
            $previousReturns = ProductReturn::where('original_invoice_id', $invoice->id)
                ->where('status', '!=', 'cancelled')
                ->get();

            // La cantidad disponible para devolución es simplemente la cantidad ACTUAL del item
            // porque la factura ya fue actualizada en devoluciones previas
            foreach ($invoice->invoiceItems as $item) {
                // La cantidad actual del item ES lo disponible para devolver
                $item->returned_quantity = 0; // No necesitamos este dato porque la factura ya está actualizada
                $item->available_for_return = $item->quantity; // Toda la cantidad actual es devolvible
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'invoice' => $invoice,
                    'previous_returns' => $previousReturns
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar factura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nueva devolución
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Log para debugging
            \Log::info('Return Request Received:', [
                'data' => $request->all(),
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            $validator = Validator::make($request->all(), [
                'original_invoice_id' => 'required|exists:invoices,id',
                'reason' => 'required|string|max:500',
                'refund_method' => 'required|in:cash,card,transfer,store_credit',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.reason' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de devolución inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // IMPORTANTE: Verificar que el usuario esté autenticado
            $userId = Auth::id();

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado',
                    'requires_auth' => true
                ], 401);
            }

            // Buscar SOLO la sesión de caja abierta del usuario autenticado
            $currentSession = CashSession::where('status', 'open')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$currentSession) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes una sesión de caja abierta. Por favor, abre una caja antes de procesar devoluciones.',
                    'requires_cash_session' => true,
                    'user_id' => $userId
                ], 400);
            }

            \Log::info('Devolución procesada en sesión de caja', [
                'session_id' => $currentSession->id,
                'user_id' => $userId,
                'user_name' => Auth::user()->name ?? 'Unknown'
            ]);

            DB::beginTransaction();

            // Obtener la factura original
            $originalInvoice = Invoice::with('invoiceItems')->findOrFail($request->original_invoice_id);

            // Validar que los productos y cantidades están disponibles para devolución
            $this->validateReturnItems($request->items, $originalInvoice);

            // Calcular totales
            $subtotal = 0;
            $taxAmount = 0;
            $returnItems = [];

            foreach ($request->items as $itemData) {
                $originalItem = $originalInvoice->invoiceItems->where('product_id', $itemData['product_id'])->first();

                if (!$originalItem) {
                    throw new \Exception("Producto no encontrado en la factura original");
                }

                $itemSubtotal = $itemData['quantity'] * $originalItem->unit_price;

                // Calcular IVA proporcional real del item original
                $itemTaxAmount = $originalItem->quantity > 0
                    ? ($originalItem->tax_amount / $originalItem->quantity) * $itemData['quantity']
                    : 0;

                $subtotal += $itemSubtotal;
                $taxAmount += $itemTaxAmount;

                $returnItems[] = [
                    'product_id' => $itemData['product_id'],
                    'original_invoice_item_id' => $originalItem->id,
                    'quantity' => $itemData['quantity'],
                    'original_quantity' => $originalItem->quantity,
                    'unit_price' => $originalItem->unit_price,
                    'discount_amount' => $originalItem->discount_amount ?? 0,
                    'tax_amount' => $itemTaxAmount, // IVA real del item original
                    'subtotal' => $itemSubtotal,
                    'reason' => $itemData['reason'] ?? null
                ];
            }

            $total = $subtotal + $taxAmount;

            // Crear la devolución
            $return = ProductReturn::create([
                'number' => ProductReturn::generateReturnNumber(),
                'original_invoice_id' => $request->original_invoice_id,
                'customer_id' => $originalInvoice->customer_id,
                'cash_session_id' => $currentSession->id,
                'user_id' => Auth::id() ?? 1, // Fallback a admin si no hay usuario autenticado
                'return_date' => now()->toDateString(),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'refund_method' => $request->refund_method,
                'status' => 'completed',
                'reason' => $request->reason,
                'notes' => $request->notes,
                'items' => json_encode($returnItems) // Convertir manualmente a JSON
            ]);

            // Actualizar inventario (devolver stock) y procesar los items
            foreach ($returnItems as $itemData) {
                // Actualizar inventario (devolver stock)
                $product = Product::findOrFail($itemData['product_id']);
                $previousStock = $product->current_stock;
                $newStock = $previousStock + $itemData['quantity'];

                $product->update(['current_stock' => $newStock]);

                // Registrar movimiento de inventario
                InventoryMovement::create([
                    'product_id' => $itemData['product_id'],
                    'type' => 'return',
                    'quantity' => $itemData['quantity'],
                    'previous_stock' => $previousStock,
                    'new_stock' => $newStock,
                    'unit_cost' => $itemData['unit_price'],
                    'reference' => "Devolución {$return->number}",
                    'notes' => "Devolución de producto - Factura: {$originalInvoice->number}",
                    'user_id' => Auth::id() ?? 1, // Fallback a admin si no hay usuario autenticado
                    'movement_date' => now()
                ]);
            }

            // Actualizar totales de la sesión de caja (restar la devolución)
            $currentSession->update([
                'total_sales' => $currentSession->total_sales - $total,
                'cash_sales' => $request->refund_method === 'cash'
                    ? $currentSession->cash_sales - $total
                    : $currentSession->cash_sales,
                'card_sales' => $request->refund_method === 'card'
                    ? $currentSession->card_sales - $total
                    : $currentSession->card_sales,
                'transfer_sales' => $request->refund_method === 'transfer'
                    ? $currentSession->transfer_sales - $total
                    : $currentSession->transfer_sales
            ]);

            // 🔧 ACTUALIZAR LA FACTURA ORIGINAL
            // Recargar la factura original para asegurar datos actualizados
            $originalInvoice = Invoice::find($request->original_invoice_id);

            if ($originalInvoice) {
                // 💳 CRÍTICO: Si la venta era a crédito, restar la deuda del cliente
                if ($originalInvoice->payment_method === 'credit') {
                    $customer = \App\Models\Customer::find($originalInvoice->customer_id);
                    if ($customer) {
                        $previousDebt = $customer->current_debt ?? 0;

                        // Calcular recargo proporcional a los items devueltos
                        $surchargeToRefund = 0;
                        if ($originalInvoice->surcharge_amount > 0 && $originalInvoice->subtotal > 0) {
                            // Proporción del recargo basado en el subtotal devuelto
                            $surchargePercentage = $originalInvoice->surcharge_amount / $originalInvoice->subtotal;
                            $surchargeToRefund = $subtotal * $surchargePercentage;
                        }

                        $totalToRefund = $total + $surchargeToRefund; // Total + recargo proporcional
                        $newDebt = max(0, $previousDebt - $totalToRefund); // No puede quedar negativo

                        $customer->current_debt = $newDebt;
                        $customer->save();

                        \Log::info('💳 Deuda del cliente actualizada por devolución de venta a crédito', [
                            'customer_id' => $customer->id,
                            'customer_name' => $customer->name,
                            'previous_debt' => $previousDebt,
                            'subtotal_returned' => $subtotal,
                            'tax_returned' => $taxAmount,
                            'surcharge_returned' => $surchargeToRefund,
                            'total_amount_refunded' => $totalToRefund,
                            'new_debt' => $newDebt,
                            'return_number' => $return->number,
                            'original_invoice' => $originalInvoice->number
                        ]);
                    }
                }

                // Calcular nuevos totales de la factura
                $newSubtotal = $originalInvoice->subtotal - $subtotal;
                $newTaxAmount = $originalInvoice->tax_amount - $taxAmount;
                $newTotal = $originalInvoice->total - $total;

                // Si la devolución es completa (total llega a 0), marcar como devuelta
                if ($newTotal <= 0) {
                    \Log::info("Marcando factura #{$originalInvoice->number} como DEVUELTA - Total devuelto completamente - Referencia: {$return->number}");

                    try {
                        // Primero eliminar los invoice_items
                        \Log::info("Eliminando invoice_items de la factura #{$originalInvoice->number}");
                        $deletedItems = InvoiceItem::where('invoice_id', $originalInvoice->id)->delete();
                        \Log::info("Items eliminados: {$deletedItems}");

                        // Luego actualizar la factura - usar array vacío, no json_encode
                        \Log::info("Actualizando factura a estado 'returned'");
                        $originalInvoice->update([
                            'subtotal' => 0,
                            'tax_amount' => 0,
                            'total' => 0,
                            'surcharge_amount' => 0, // Resetear recargo en devolución completa
                            'status' => 'returned',
                            'return_reference' => $return->number,
                            'items' => [] // Array vacío (Laravel lo convertirá a JSON automáticamente)
                        ]);

                        \Log::info("Factura actualizada exitosamente a estado 'returned'");
                    } catch (\Exception $e) {
                        \Log::error("Error al actualizar factura a returned: " . $e->getMessage());
                        \Log::error("Stack trace: " . $e->getTraceAsString());
                        throw $e;
                    }
                } else {
                    \Log::info("Actualizando factura #{$originalInvoice->number} - Nuevo total: {$newTotal}");

                    // Calcular nuevo surcharge_amount proporcional si es venta a crédito
                    $newSurchargeAmount = 0;
                    if ($originalInvoice->payment_method === 'credit' && $originalInvoice->surcharge_amount > 0) {
                        // Calcular proporción del recargo basado en el nuevo subtotal
                        $originalSubtotalWithSurcharge = $originalInvoice->subtotal + $originalInvoice->surcharge_amount;
                        $surchargePercentage = $originalSubtotalWithSurcharge > 0
                            ? ($originalInvoice->surcharge_amount / $originalInvoice->subtotal)
                            : 0;
                        $newSurchargeAmount = $newSubtotal * $surchargePercentage;

                        \Log::info("Recargo proporcional calculado", [
                            'original_surcharge' => $originalInvoice->surcharge_amount,
                            'new_surcharge' => $newSurchargeAmount,
                            'percentage' => $surchargePercentage * 100 . '%'
                        ]);
                    }

                    // Devolución parcial: actualizar totales de la factura
                    $originalInvoice->update([
                        'subtotal' => max(0, $newSubtotal),
                        'tax_amount' => max(0, $newTaxAmount),
                        'total' => max(0, $newTotal),
                        'surcharge_amount' => max(0, $newSurchargeAmount)
                    ]);

                    // Actualizar o eliminar invoice_items devueltos
                    foreach ($request->items as $itemData) {
                        $originalItem = InvoiceItem::where('invoice_id', $originalInvoice->id)
                            ->where('product_id', $itemData['product_id'])
                            ->first();

                        if ($originalItem) {
                            $newQuantity = $originalItem->quantity - $itemData['quantity'];

                            if ($newQuantity <= 0) {
                                \Log::info("Eliminando item completo - Producto ID: {$itemData['product_id']}");
                                // Si se devolvió toda la cantidad, eliminar el item
                                $originalItem->delete();
                            } else {
                                // Actualizar cantidad y totales del item
                                $newItemSubtotal = $newQuantity * $originalItem->unit_price;
                                $newItemTaxAmount = ($originalItem->tax_amount / $originalItem->quantity) * $newQuantity;

                                \Log::info("Actualizando item - Producto ID: {$itemData['product_id']} - Nueva cantidad: {$newQuantity}");

                                $originalItem->update([
                                    'quantity' => $newQuantity,
                                    'subtotal' => $newItemSubtotal,
                                    'tax_amount' => $newItemTaxAmount
                                ]);
                            }
                        }
                    }

                    // También actualizar el campo items JSON de la factura
                    $updatedItems = [];
                    $remainingItems = InvoiceItem::where('invoice_id', $originalInvoice->id)->get();

                    foreach ($remainingItems as $item) {
                        $product = Product::find($item->product_id);
                        $updatedItems[] = [
                            'product_id' => $item->product_id,
                            'product_name' => $product ? $product->name : 'Producto eliminado',
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price
                        ];
                    }

                    $originalInvoice->update([
                        'items' => json_encode($updatedItems)
                    ]);
                }
            }

            DB::commit();

            // IMPORTANTE: Recargar la devolución desde la BD después del commit
            $savedReturn = ProductReturn::with(['originalInvoice', 'customer'])
                ->find($return->id);

            // Usar la devolución recargada desde la BD
            $return = $savedReturn;

            if (!$return) {
                // En lugar de lanzar excepción, crear respuesta de error específica
                return response()->json([
                    'success' => false,
                    'message' => 'Error crítico: La devolución no pudo ser procesada correctamente'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Devolución procesada exitosamente',
                'data' => $return
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la devolución',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar que los items pueden ser devueltos
     *
     * IMPORTANTE: Los invoice_items ya tienen la cantidad actualizada después de cada devolución.
     * Por lo tanto, NO debemos restar las devoluciones previas nuevamente.
     * Solo validamos contra la cantidad actual del invoice_item.
     */
    private function validateReturnItems($items, $originalInvoice)
    {
        foreach ($items as $itemData) {
            $originalItem = $originalInvoice->invoiceItems->where('product_id', $itemData['product_id'])->first();

            if (!$originalItem) {
                throw new \Exception("El producto ID {$itemData['product_id']} no existe en la factura original");
            }

            // La cantidad del invoice_item YA refleja las devoluciones previas
            // Solo validamos que no se devuelva más de lo que hay actualmente
            $availableForReturn = $originalItem->quantity;

            if ($itemData['quantity'] > $availableForReturn) {
                $product = Product::find($itemData['product_id']);
                throw new \Exception("No se puede devolver {$itemData['quantity']} unidades de {$product->name}. Solo hay {$availableForReturn} disponibles para devolución.");
            }
        }
    }

    /**
     * Obtener detalles de una devolución específica
     */
    public function show($id): JsonResponse
    {
        try {
            $return = ProductReturn::with([
                'originalInvoice',
                'customer',
                'user',
                'cashSession'
            ])->findOrFail($id);

            // Asegurar que items es un array
            $items = is_string($return->items) ? json_decode($return->items, true) : $return->items;
            if (!is_array($items)) {
                $items = [];
            }

            // Agregar información de productos a cada item
            $return->return_items = collect($items)->map(function ($item) {
                if (isset($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    $item['product'] = $product;
                }
                return $item;
            })->toArray();

            return response()->json([
                'success' => true,
                'data' => $return
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Devolución no encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Cancelar una devolución (solo si está pendiente)
     */
    public function cancel($id): JsonResponse
    {
        try {
            $return = ProductReturn::findOrFail($id);

            if ($return->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden cancelar devoluciones pendientes'
                ], 400);
            }

            DB::beginTransaction();

            // Marcar como cancelada
            $return->update(['status' => 'cancelled']);

            // Revertir movimientos de inventario si ya se aplicaron
            $returnItemsArray = is_string($return->items) ? json_decode($return->items, true) : $return->items;
            foreach ($returnItemsArray ?? [] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $previousStock = $product->current_stock;
                $newStock = $previousStock - $item['quantity'];

                $product->update(['current_stock' => $newStock]);

                // Registrar movimiento de reversa
                InventoryMovement::create([
                    'product_id' => $item['product_id'],
                    'type' => 'adjustment',
                    'quantity' => -$item['quantity'],
                    'previous_stock' => $previousStock,
                    'new_stock' => $newStock,
                    'reference' => "Cancelación devolución {$return->number}",
                    'notes' => "Cancelación de devolución",
                    'user_id' => Auth::id(),
                    'movement_date' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Devolución cancelada exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar la devolución',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener métricas agregadas de devoluciones por período
     */
    public function getMetrics(Request $request, $period = 'today'): JsonResponse
    {
        try {
            // Determinar rango de fechas según el período
            $now = now();

            switch ($period) {
                case 'today':
                    $startDate = $now->copy()->startOfDay();
                    $endDate = $now->copy()->endOfDay();
                    break;
                case 'week':
                    $startDate = $now->copy()->subDays(7)->startOfDay();
                    $endDate = $now->copy()->endOfDay();
                    break;
                case 'month':
                    $startDate = $now->copy()->subDays(30)->startOfDay();
                    $endDate = $now->copy()->endOfDay();
                    break;
                case 'quarter':
                    $startDate = $now->copy()->subDays(90)->startOfDay();
                    $endDate = $now->copy()->endOfDay();
                    break;
                case 'year':
                    $startDate = $now->copy()->subDays(365)->startOfDay();
                    $endDate = $now->copy()->endOfDay();
                    break;
                default:
                    $startDate = $now->copy()->subDays(30)->startOfDay();
                    $endDate = $now->copy()->endOfDay();
            }

            // Calcular métricas de devoluciones
            $returns = ProductReturn::where('status', 'completed')
                ->whereBetween('return_date', [$startDate, $endDate]);

            $totalReturns = $returns->sum('total');
            $returnsCount = $returns->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'totalReturns' => (float) $totalReturns,
                    'returnsCount' => $returnsCount,
                    'period' => $period,
                    'startDate' => $startDate->toDateString(),
                    'endDate' => $endDate->toDateString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener métricas de devoluciones',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
