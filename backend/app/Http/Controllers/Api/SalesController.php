<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Sale::with(['customer', 'user', 'saleItems.product']);

            // Filtrar por búsqueda
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('invoice_number', 'like', "%{$search}%");
            }

            // Filtrar por estado
            if ($request->has('status')) {
                $query->where('status', $request->get('status'));
            }

            // Filtrar por cliente
            if ($request->has('customer_id')) {
                $query->where('customer_id', $request->get('customer_id'));
            }

            // Ordenar por fecha más reciente
            $query->orderBy('sale_date', 'desc');

            // Paginar resultados
            $sales = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $sales,
                'message' => 'Ventas obtenidas exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las ventas: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log de datos recibidos para debugging
        \Log::info('Datos recibidos para crear venta/cotización:', $request->all());

        $validator = Validator::make($request->all(), [
            'invoice_number' => 'required|string|unique:sales,invoice_number',
            'customer_id' => 'nullable|exists:customers,id',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|in:cash,card,transfer,credit,mixed',
            'cash_received' => 'nullable|numeric|min:0',
            'change_given' => 'nullable|numeric|min:0',
            'status' => 'required|in:completed,pending,cancelled,refunded,quotation',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total_price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();

            // Crear la venta/cotización
            $sale = Sale::create([
                'invoice_number' => $request->invoice_number,
                'customer_id' => $request->customer_id,
                'user_id' => auth()->id() ?? 1,
                'subtotal' => $request->subtotal,
                'tax_amount' => $request->tax_amount ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'total_amount' => $request->total_amount,
                'payment_method' => $request->payment_method,
                'cash_received' => $request->cash_received,
                'change_given' => $request->change_given,
                'status' => $request->status,
                'notes' => $request->notes,
                'sale_date' => now()
            ]);

            // Crear los items de la venta
            foreach ($request->items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price']
                ]);
            }

            DB::commit();

            // Cargar la venta con sus relaciones
            $sale->load(['customer', 'user', 'saleItems.product']);

            return response()->json([
                'success' => true,
                'data' => $sale,
                'message' => $request->status === 'quotation' ? 'Cotización creada exitosamente' : 'Venta creada exitosamente'
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la venta: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $sale = Sale::with(['customer', 'user', 'saleItems.product'])->find($id);

            if (!$sale) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'data' => $sale,
                'message' => 'Venta obtenida exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la venta: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $sale = Sale::find($id);

            if (!$sale) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], Response::HTTP_NOT_FOUND);
            }

            $validator = Validator::make($request->all(), [
                'customer_id' => 'nullable|exists:customers,id',
                'subtotal' => 'sometimes|numeric|min:0',
                'tax_amount' => 'sometimes|numeric|min:0',
                'discount_amount' => 'sometimes|numeric|min:0',
                'total_amount' => 'sometimes|numeric|min:0',
                'payment_method' => 'sometimes|in:cash,card,transfer,credit,mixed',
                'cash_received' => 'sometimes|numeric|min:0',
                'change_given' => 'sometimes|numeric|min:0',
                'status' => 'sometimes|in:completed,pending,cancelled,refunded,quotation',
                'notes' => 'nullable|string',
                // Validación para items (opcional, solo si se envían)
                'items' => 'sometimes|array|min:1',
                'items.*.product_id' => 'required_with:items|exists:products,id',
                'items.*.quantity' => 'required_with:items|integer|min:1',
                'items.*.unit_price' => 'required_with:items|numeric|min:0',
                'items.*.total_price' => 'required_with:items|numeric|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $sale->update($request->only([
                'customer_id',
                'subtotal',
                'tax_amount',
                'discount_amount',
                'total_amount',
                'payment_method',
                'cash_received',
                'change_given',
                'status',
                'notes'
            ]));

            // Actualizar items si se proporcionaron
            if ($request->has('items')) {
                // Eliminar items existentes
                $sale->saleItems()->delete();

                // Crear nuevos items
                foreach ($request->items as $item) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['total_price'],
                        'discount_amount' => $item['discount_amount'] ?? 0
                    ]);
                }
            }

            $sale->load(['customer', 'user', 'saleItems.product']);

            return response()->json([
                'success' => true,
                'data' => $sale,
                'message' => 'Venta actualizada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la venta: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $sale = Sale::find($id);

            if (!$sale) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], Response::HTTP_NOT_FOUND);
            }

            // Verificar si la venta puede ser eliminada
            if ($sale->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar una venta completada'
                ], Response::HTTP_FORBIDDEN);
            }

            DB::beginTransaction();

            // Eliminar los items primero
            $sale->saleItems()->delete();

            // Eliminar la venta
            $sale->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la venta: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get items for a specific sale
     */
    public function items(string $id)
    {
        try {
            $sale = Sale::with(['saleItems.product'])->find($id);

            if (!$sale) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'data' => $sale->saleItems,
                'message' => 'Items de venta obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los items: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Process a refund for a sale
     */
    public function refund(Request $request, string $id)
    {
        try {
            $sale = Sale::find($id);

            if (!$sale) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], Response::HTTP_NOT_FOUND);
            }

            if ($sale->status === 'refunded') {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta venta ya ha sido reembolsada'
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($sale->status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden reembolsar ventas completadas'
                ], Response::HTTP_BAD_REQUEST);
            }

            $validator = Validator::make($request->all(), [
                'reason' => 'required|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            DB::beginTransaction();

            // Actualizar el estado de la venta
            $sale->update([
                'status' => 'refunded',
                'notes' => $sale->notes . "\n\nREEMBOLSO: " . $request->reason
            ]);

            DB::commit();

            $sale->load(['customer', 'user', 'saleItems.product']);

            return response()->json([
                'success' => true,
                'data' => $sale,
                'message' => 'Reembolso procesado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el reembolso: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Búsqueda pública de cotización por código (para QR sin autenticación)
     */
    public function searchQuotePublic($code)
    {
        try {
            // Buscar primero en la tabla sales (sistema antiguo con códigos largos)
            $quote = Sale::with(['customer', 'user', 'saleItems.product'])
                ->where('invoice_number', $code)
                ->where('status', 'quotation')
                ->first();

            if ($quote) {
                return response()->json([
                    'success' => true,
                    'data' => $quote,
                    'message' => 'Cotización encontrada exitosamente'
                ]);
            }

            // Si no se encuentra en sales, buscar en la tabla invoices (sistema nuevo con códigos COT-000XXX)
            $invoiceQuote = \App\Models\Invoice::with(['customer', 'invoiceItems.product'])
                ->where('number', $code)
                ->where('status', 'quotation')
                ->first();

            if ($invoiceQuote) {
                // Transformar el formato de invoices para que coincida con el esperado por el frontend
                $transformedQuote = (object) [
                    'id' => $invoiceQuote->id,
                    'invoice_number' => $invoiceQuote->number,
                    'customer_name' => $invoiceQuote->customer->name ?? 'Cliente General',
                    'customer_id' => $invoiceQuote->customer_id,
                    'total_amount' => $invoiceQuote->total,
                    'status' => $invoiceQuote->status,
                    'created_at' => $invoiceQuote->created_at,
                    'updated_at' => $invoiceQuote->updated_at,
                    'customer' => $invoiceQuote->customer,
                    'items' => $invoiceQuote->invoiceItems->map(function($item) {
                        return [
                            'id' => $item->id,
                            'product_name' => $item->product->name ?? 'Producto',
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'total_price' => $item->total_price,
                            'product' => $item->product
                        ];
                    })
                ];

                return response()->json([
                    'success' => true,
                    'data' => $transformedQuote,
                    'message' => 'Cotización encontrada exitosamente'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Cotización no encontrada'
            ], Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar cotización: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
