<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\InvoiceItem;
use App\Models\AppliedDiscount;
use App\Models\Discount;
use App\Models\Product;
use App\Models\InventoryMovement;
use App\Models\CashSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Invoice::with(['customer', 'items.product'])
                ->orderBy('created_at', 'desc');

            // Filter by customer_id if provided
            if ($request->has('customer_id')) {
                $query->where('customer_id', $request->customer_id);
            }

            // Filter by payment_method if provided
            if ($request->has('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }

            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by type if provided
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            $invoices = $query->get()
                ->map(function ($invoice) {
                    return [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number ?? $invoice->number,
                        'number' => $invoice->number,
                        'type' => $invoice->type,
                        'customer_id' => $invoice->customer_id,
                        'customer_name' => $invoice->customer->name,
                        'customer_document' => $invoice->customer->document,
                        'customer_email' => $invoice->customer->email,
                        'customer_phone' => $invoice->customer->phone,
                        'date' => $invoice->date->format('Y-m-d'),
                        'due_date' => $invoice->due_date?->format('Y-m-d'),
                        'subtotal' => (float) $invoice->subtotal,
                        'tax' => (float) $invoice->tax_amount,
                        'total' => (float) $invoice->total,
                        'status' => $invoice->status,
                        'payment_method' => $invoice->payment_method,
                        'surcharge_amount' => (float) ($invoice->surcharge_amount ?? 0),
                        'notes' => $invoice->notes,
                        'items' => $this->getInvoiceItems($invoice),
                        'created_at' => $invoice->created_at,
                        'updated_at' => $invoice->updated_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $invoices
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las facturas',
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
            $validator = Validator::make($request->all(), [
                'type' => 'required|in:invoice,quote,credit_note',
                'customer_id' => 'required|exists:customers,id',
                'date' => 'required|date',
                'due_date' => 'nullable|date|after_or_equal:date',
                'items' => 'required|array',
                'items.*.product_id' => 'required|integer',
                'items.*.product_name' => 'required|string',
                'items.*.product_sku' => 'nullable|string',
                'items.*.quantity' => 'required|numeric|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.cost_price' => 'nullable|numeric|min:0',
                'items.*.discount_amount' => 'nullable|numeric|min:0',
                'items.*.tax_amount' => 'nullable|numeric|min:0',
                'items.*.notes' => 'nullable|string',
                'subtotal' => 'required|numeric|min:0',
                'tax_amount' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
                'discounts' => 'nullable|array',
                'discounts.*.type' => 'nullable|string',
                'discounts.*.value' => 'nullable|numeric|min:0',
                'discounts.*.amount' => 'nullable|numeric|min:0',
                'discounts.*.reason' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $data = $validator->validated();
            $data['number'] = Invoice::generateNextNumber($data['type']);

            // Establecer status según el tipo de documento
            if ($data['type'] === 'quote') {
                $data['status'] = 'quotation'; // Las cotizaciones deben tener status 'quotation' para activar "Mandar al POS"
            } else {
                $data['status'] = 'draft'; // Facturas y notas de crédito como borrador
            }

            // Crear la factura principal
            $invoice = Invoice::create($data);

            // Insertar items detallados en invoice_items
            foreach ($data['items'] as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $discountAmount = $item['discount_amount'] ?? 0;
                $taxAmount = $item['tax_amount'] ?? 0;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['product_sku'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'cost_price' => $item['cost_price'] ?? null,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'tax_amount' => $taxAmount,
                    'notes' => $item['notes'] ?? null
                ]);
            }

            // Insertar descuentos aplicados si existen
            if (isset($data['discounts']) && !empty($data['discounts'])) {
                foreach ($data['discounts'] as $discount) {
                    if (isset($discount['amount']) && $discount['amount'] > 0) {
                        AppliedDiscount::create([
                            'customer_id' => $data['customer_id'],
                            'invoice_id' => $invoice->id,
                            'discount_type' => $discount['type'] ?? 'manual',
                            'discount_value' => $discount['value'] ?? 0,
                            'discount_amount' => $discount['amount'],
                            'discount_reason' => $discount['reason'] ?? 'Descuento aplicado en factura',
                            'applied_by' => (string)(auth()->id() ?? 1), // Usuario actual o admin por defecto
                            'applied_at' => now()
                        ]);
                    }
                }
            }

            DB::commit();

            $invoice->load(['customer', 'invoiceItems', 'appliedDiscounts']);

            return response()->json([
                'success' => true,
                'message' => 'Factura creada exitosamente',
                'data' => $invoice
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('POS Invoice Error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la factura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $invoice = Invoice::with(['customer'])->findOrFail($id);
            // Cargar items manualmente para debug
            $invoice->load(['items']);

            // Formatear la respuesta para incluir información completa
            $formattedInvoice = [
                'id' => $invoice->id,
                'number' => $invoice->number,
                'type' => $invoice->type,
                'customer_name' => $invoice->customer->name,
                'customer_document' => $invoice->customer->document,
                'customer_email' => $invoice->customer->email,
                'customer_phone' => $invoice->customer->phone,
                'date' => $invoice->date->format('Y-m-d'),
                'due_date' => $invoice->due_date?->format('Y-m-d'),
                'subtotal' => (float) $invoice->subtotal,
                'tax' => (float) $invoice->tax_amount,
                'total' => (float) $invoice->total,
                'status' => $invoice->status,
                'notes' => $invoice->notes,
                'items' => $this->getInvoiceItems($invoice),
                'created_at' => $invoice->created_at,
                'updated_at' => $invoice->updated_at,
            ];

            return response()->json([
                'success' => true,
                'data' => $formattedInvoice
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Factura no encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $invoice = Invoice::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'type' => 'sometimes|in:invoice,quote,credit_note',
                'customer_id' => 'sometimes|exists:customers,id',
                'date' => 'sometimes|date',
                'due_date' => 'nullable|date|after_or_equal:date',
                'items' => 'sometimes|array',
                'subtotal' => 'sometimes|numeric|min:0',
                'tax_amount' => 'sometimes|numeric|min:0',
                'total' => 'sometimes|numeric|min:0',
                'status' => 'sometimes|in:draft,sent,paid,overdue,cancelled',
                'notes' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $invoice->update($validator->validated());
            $invoice->load('customer');

            return response()->json([
                'success' => true,
                'message' => 'Factura actualizada exitosamente',
                'data' => $invoice
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la factura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            // Validar credenciales de administrador si se proporcionan
            if ($request->has('admin_email') && $request->has('admin_password')) {
                $admin = User::where('email', $request->admin_email)
                    ->where('role', 'admin')
                    ->first();

                if (!$admin || !Hash::check($request->admin_password, $admin->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Credenciales de administrador inválidas'
                    ], 401);
                }
            }

            $invoice = Invoice::with(['invoiceItems', 'customer'])->findOrFail($id);

            // Log para auditoria
            \Log::info("Eliminando factura {$invoice->number}", [
                'invoice_id' => $id,
                'total' => $invoice->total,
                'items_count' => $invoice->invoiceItems->count(),
                'deleted_by' => auth()->user()->email ?? 'sistema'
            ]);

            \DB::transaction(function () use ($invoice) {
                // 1. Restaurar inventario de productos
                foreach ($invoice->invoiceItems as $item) {
                    if ($item->product_id) {
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $product->increment('stock', $item->quantity);
                            \Log::info("Stock restaurado: {$product->name} +{$item->quantity} = {$product->stock}");
                        }
                    }
                }

                // 2. Si hay items con array JSON (facturas POS)
                if ($invoice->items && is_array($invoice->items)) {
                    foreach ($invoice->items as $item) {
                        if (isset($item['product_id'])) {
                            $product = Product::find($item['product_id']);
                            if ($product) {
                                $quantity = $item['quantity'] ?? 1;
                                $product->increment('stock', $quantity);
                                \Log::info("Stock restaurado (JSON): {$product->name} +{$quantity} = {$product->stock}");
                            }
                        }
                    }
                }

                // 3. Eliminar items relacionados
                $invoice->invoiceItems()->delete();

                // 4. Eliminar descuentos aplicados si existen
                $invoice->appliedDiscounts()->delete();

                // 5. Actualizar estado de sesión de caja si aplica
                if ($invoice->cash_session_id) {
                    // Aquí podrías actualizar los totales de la sesión de caja
                    \Log::info("Factura eliminada de sesión de caja: {$invoice->cash_session_id}");
                }

                // 6. Finalmente eliminar la factura
                $invoice->delete();
            });

            return response()->json([
                'success' => true,
                'message' => "Factura {$invoice->number} eliminada exitosamente. Inventario restaurado."
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Factura no encontrada'
            ], 404);
        } catch (\Exception $e) {
            \Log::error("Error eliminando factura {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la factura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoice statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $currentMonth = now()->format('Y-m');

            $stats = [
                'monthly_invoices' => Invoice::where('type', 'invoice')
                    ->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$currentMonth])
                    ->count(),
                'total_invoiced' => Invoice::where('type', 'invoice')
                    ->where('status', '!=', 'cancelled')
                    ->sum('total'),
                'pending_invoices' => Invoice::where('status', 'sent')->count(),
                'quotations' => Invoice::where('type', 'quote')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear factura desde POS sin autenticación (solo para desarrollo)
     */
    public function createPosInvoice(Request $request): JsonResponse
    {
        try {
            // Log para debug
            \Log::info('POS Invoice Request Data:', $request->all());

            // Fix for legacy frontend sending customer_id 7 (Cliente General) which might not exist
            if ($request->customer_id == 7) {
                $customerExists = DB::table('customers')->where('id', 7)->exists();
                if (!$customerExists) {
                    $request->merge(['customer_id' => 1]);
                    \Log::info('Mapped customer_id 7 to 1 because 7 does not exist.');
                }
            }

            // Obtener códigos de métodos de pago válidos de la base de datos
            $validPaymentMethods = DB::table('payment_methods')
                ->where('active', 1)
                ->pluck('code')
                ->toArray();

            // ✅ Agregar 'credit' si Creditienda está habilitado
            $systemSettings = DB::table('system_settings')->first();
            if ($systemSettings && $systemSettings->creditienda_enabled) {
                $validPaymentMethods[] = 'credit';
            }

            // 🐛 DEBUG: Ver qué payment_method llega desde el frontend
            \Log::info('🔍 Payment Method Debug:', [
                'received_payment_method' => $request->payment_method,
                'valid_payment_methods' => $validPaymentMethods,
                'type_of_received' => gettype($request->payment_method),
                'creditienda_enabled' => $systemSettings->creditienda_enabled ?? false
            ]);

            $validator = Validator::make($request->all(), [
                'type' => 'required|in:invoice,quote,credit_note',
                'customer_id' => 'required|exists:customers,id',
                'date' => 'required|date',
                'due_date' => 'nullable|date|after_or_equal:date',
                'payment_method' => 'nullable|in:' . implode(',', $validPaymentMethods),
                'surcharge_amount' => 'nullable|numeric|min:0',
                'items' => 'required|array',
                'items.*.product_id' => 'required|integer',
                'items.*.product_name' => 'required|string',
                'items.*.quantity' => 'required|numeric|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'subtotal' => 'required|numeric|min:0',
                'tax_amount' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
                // Campos para descuentos promocionales
                'applied_discount' => 'nullable|array',
                'applied_discount.code' => 'nullable|string',
                'applied_discount.discount_id' => 'nullable|integer|exists:discounts,id',
                'applied_discount.amount' => 'nullable|numeric|min:0',
                'customer_email' => 'nullable|email',
                'customer_phone' => 'nullable|string',
                'user_identifier' => 'nullable|string',
                // Campos para loyalty points
                'loyalty_points_redeemed' => 'nullable|integer|min:1',
                'loyalty_discount_amount' => 'nullable|numeric|min:0'
            ]);

            if ($validator->fails()) {
                \Log::error('POS Invoice Validation Failed:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $data = $validator->validated();

            \Log::info('📝 Generando número de factura para tipo: ' . $data['type']);
            $data['number'] = Invoice::generateNextNumber($data['type']);
            \Log::info('✅ Número generado: ' . $data['number']);

            // Establecer status según el tipo de documento
            if ($data['type'] === 'quote') {
                $data['status'] = 'quotation'; // Las cotizaciones deben tener status 'quotation' para activar "Mandar al POS"
            } else {
                $data['status'] = 'paid'; // Las ventas del POS están pagadas
            }

            // Asignar cash_session_id SOLO para ventas de contado (no cotizaciones ni crédito)
            if ($data['type'] !== 'quote' && (!isset($data['payment_method']) || $data['payment_method'] !== 'credit')) {
                // Obtener el usuario autenticado
                $userId = Auth::id();

                \Log::info('Auth check in createPosInvoice', [
                    'user_id' => $userId,
                    'auth_check' => Auth::check(),
                    'auth_user' => Auth::user() ? Auth::user()->toArray() : null,
                    'request_user' => $request->user() ? $request->user()->toArray() : null
                ]);

                if (!$userId) {
                    \Log::error('Usuario no autenticado en createPosInvoice');
                    return response()->json([
                        'success' => false,
                        'message' => 'Usuario no autenticado'
                    ], 401);
                }

                // Buscar sesión de caja abierta para el usuario específico
                $currentCashSession = CashSession::getOpenSessionForUser($userId);

                if ($currentCashSession) {
                    $data['cash_session_id'] = $currentCashSession->id;
                    \Log::info("✅ Venta de contado asignada a sesión de caja {$currentCashSession->id}");
                } else {
                    // Log warning pero no bloquear la venta
                    \Log::warning("Factura {$data['number']} creada sin sesión de caja abierta para usuario {$userId}");
                }
            } elseif (isset($data['payment_method']) && $data['payment_method'] === 'credit') {
                // Las ventas a crédito NO se asignan a caja porque no hay dinero físico
                $data['cash_session_id'] = null;
                \Log::info("💳 Venta a crédito NO asignada a caja - El dinero entrará cuando se registre el abono");
            }

            // VALIDACIÓN CRÍTICA: VENTAS A CRÉDITO (antes de crear la factura)
            if (isset($data['payment_method']) && $data['payment_method'] === 'credit') {
                \Log::info('💳 Validando venta a crédito', [
                    'customer_id' => $data['customer_id'],
                    'total' => $data['total'],
                    'surcharge_amount' => $data['surcharge_amount'] ?? 0
                ]);

                // 1. Obtener cliente
                $customer = \App\Models\Customer::find($data['customer_id']);
                if (!$customer) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cliente no encontrado'
                    ], 404);
                }

                // 2. Validar que el cliente tenga crédito activo
                if (!$customer->credit_active) {
                    \Log::warning('❌ Cliente sin crédito activo', [
                        'customer_id' => $customer->id,
                        'customer_name' => $customer->name
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'El cliente no tiene crédito habilitado'
                    ], 400);
                }

                // 3. Calcular total con recargo
                $totalWithSurcharge = $data['total'] + ($data['surcharge_amount'] ?? 0);

                // 4. Validar cupo disponible
                $currentDebt = floatval($customer->current_debt ?? 0);
                $creditLimit = floatval($customer->credit_limit ?? 0);
                $availableCredit = $creditLimit - $currentDebt;
                $newDebt = $currentDebt + $totalWithSurcharge;

                \Log::info('🔍 Validación de cupo', [
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'credit_limit' => $creditLimit,
                    'current_debt' => $currentDebt,
                    'available_credit' => $availableCredit,
                    'required_credit' => $totalWithSurcharge,
                    'new_debt_if_approved' => $newDebt
                ]);

                if ($totalWithSurcharge > $availableCredit) {
                    \Log::error('❌ Crédito insuficiente', [
                        'customer_id' => $customer->id,
                        'available_credit' => $availableCredit,
                        'required' => $totalWithSurcharge,
                        'deficit' => $totalWithSurcharge - $availableCredit
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => sprintf(
                            'Crédito insuficiente. Disponible: $%s - Requerido: $%s',
                            number_format($availableCredit, 0),
                            number_format($totalWithSurcharge, 0)
                        )
                    ], 400);
                }

                \Log::info('✅ Validación de cupo exitosa, procesando venta a crédito');
            }

            // Crear la factura principal
            $invoice = Invoice::create($data);

            // 🎁 LOYALTY POINTS: Redimir puntos si el cliente los usó (ANTES de procesar items)
            if (isset($data['loyalty_points_redeemed']) && $data['loyalty_points_redeemed'] > 0) {
                try {
                    $systemSettings = \App\Models\SystemSetting::first();

                    if ($systemSettings && $systemSettings->enable_loyalty_system) {
                        $customer = \App\Models\Customer::find($data['customer_id']);

                        if ($customer) {
                            // Validar que el cliente tenga puntos suficientes
                            if (!$customer->hasLoyaltyPoints($data['loyalty_points_redeemed'])) {
                                \Log::warning('⚠️ Cliente no tiene suficientes puntos de fidelización', [
                                    'customer_id' => $customer->id,
                                    'available_points' => $customer->loyalty_points,
                                    'requested_points' => $data['loyalty_points_redeemed']
                                ]);

                                DB::rollback();
                                return response()->json([
                                    'success' => false,
                                    'message' => sprintf(
                                        'El cliente no tiene suficientes puntos. Disponible: %d - Solicitado: %d',
                                        $customer->loyalty_points,
                                        $data['loyalty_points_redeemed']
                                    )
                                ], 400);
                            }

                            // Redimir los puntos
                            $customer->redeemLoyaltyPoints(
                                $data['loyalty_points_redeemed'],
                                $invoice->id,
                                Auth::id()
                            );
                        }
                    }
                } catch (\Exception $e) {
                    // Fallar la venta si hay error al redimir puntos (está dentro de la transacción)
                    \Log::error('❌ Error al redimir puntos de fidelización', [
                        'error' => $e->getMessage(),
                        'invoice_id' => $invoice->id,
                        'customer_id' => $data['customer_id'],
                        'points_requested' => $data['loyalty_points_redeemed']
                    ]);

                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'Error al procesar puntos de fidelización: ' . $e->getMessage()
                    ], 500);
                }
            }

            // PROCESAR VENTA A CRÉDITO (después de crear y validar)
            if (isset($data['payment_method']) && $data['payment_method'] === 'credit') {
                \Log::info('💳 Procesando venta a crédito', [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->number,
                    'customer_id' => $data['customer_id'],
                    'total' => $data['total'],
                    'surcharge_amount' => $data['surcharge_amount'] ?? 0
                ]);

                // IMPORTANTE: Mantener status = 'paid' para que cuente en reportes de ventas
                // El payment_method='credit' ya indica que es a crédito
                // La columna cash_session_id=null indica que no está en caja
                \Log::info("✅ Factura a crédito mantiene status='paid' para contar en ventas, pero sin cash_session_id");

                // Actualizar deuda del cliente (ya validamos antes)
                $customer = \App\Models\Customer::find($data['customer_id']);
                if ($customer) {
                    $previousDebt = $customer->current_debt ?? 0;
                    $totalWithSurcharge = $data['total'] + ($data['surcharge_amount'] ?? 0);
                    $customer->current_debt = $previousDebt + $totalWithSurcharge;
                    $customer->save();

                    \Log::info('✅ Deuda del cliente actualizada', [
                        'customer_id' => $customer->id,
                        'customer_name' => $customer->name,
                        'previous_debt' => $previousDebt,
                        'added_amount' => $totalWithSurcharge,
                        'new_debt' => $customer->current_debt,
                        'credit_limit' => $customer->credit_limit,
                        'available_credit' => $customer->credit_limit - $customer->current_debt
                    ]);
                }
            }


            // Preparar array de items para el campo JSON
            $itemsForJson = [];

            // Calcular IVA proporcional para cada item
            $totalSubtotal = $data['subtotal'];
            $totalTaxAmount = $data['tax_amount'];

            // Insertar items detallados en invoice_items
            foreach ($data['items'] as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];

                // Calcular IVA proporcional basado en el subtotal del item
                $itemTaxAmount = $totalSubtotal > 0
                    ? ($subtotal / $totalSubtotal) * $totalTaxAmount
                    : 0;

                // Agregar al array JSON
                $itemsForJson[] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price']
                ];

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['product_sku'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                    'discount_amount' => 0,
                    'tax_amount' => $itemTaxAmount,
                    'notes' => null
                ]);

                // Actualizar stock del producto SOLO si es una venta real, NO para cotizaciones
                if ($data['type'] !== 'quote') {
                    $product = \App\Models\Product::find($item['product_id']);
                    if ($product) {
                        $previousStock = $product->current_stock;
                        $product->current_stock -= $item['quantity']; // Usar current_stock en lugar de quantity
                        $newStock = $product->current_stock;
                        $product->save();

                        // Log de movimiento de inventario
                        \App\Models\InventoryMovement::create([
                            'product_id' => $item['product_id'],
                            'type' => 'sale',
                            'quantity' => -$item['quantity'], // Negativo para salida
                            'previous_stock' => $previousStock,
                            'new_stock' => $newStock,
                            'unit_cost' => $product->cost_price ?? 0,
                            'reference' => 'Factura ' . $invoice->number,
                            'notes' => 'Venta POS - Factura ' . $invoice->number,
                            'user_id' => 1, // Usuario admin por defecto
                            'movement_date' => now()
                        ]);
                    }
                }
                // Para cotizaciones, NO crear movimiento de inventario ya que no afectan stock
            }

            // Actualizar el campo items con el JSON de productos
            $invoice->items = json_encode($itemsForJson);
            $invoice->save();

            // Procesar descuento promocional si existe
            \Log::info('🔍 Verificando descuento promocional', [
                'has_applied_discount' => isset($data['applied_discount']),
                'applied_discount_data' => $data['applied_discount'] ?? null
            ]);

            if (isset($data['applied_discount']) && !empty($data['applied_discount'])) {
                $discountData = $data['applied_discount'];

                \Log::info('📋 Datos del descuento recibidos', [
                    'discount_data' => $discountData,
                    'has_discount_id' => isset($discountData['discount_id']),
                    'discount_id' => $discountData['discount_id'] ?? null
                ]);

                if (isset($discountData['discount_id']) && $discountData['discount_id']) {
                    $discount = Discount::find($discountData['discount_id']);

                    \Log::info('🔍 Buscando descuento en BD', [
                        'discount_id' => $discountData['discount_id'],
                        'discount_found' => $discount !== null,
                        'discount_code' => $discount->code ?? 'N/A'
                    ]);

                    if ($discount) {
                        // VALIDACIÓN CRÍTICA: Verificar si el descuento aún puede usarse
                        $userIdentifier = $request->user_identifier ?: $request->ip();
                        $customerEmail = $request->customer_email;

                        if (!$discount->canBeUsedBy($userIdentifier, $customerEmail)) {
                            \Log::error('❌ Descuento no puede ser usado', [
                                'discount_code' => $discount->code,
                                'user_identifier' => $userIdentifier,
                                'current_usage' => $discount->getUsageCountBy($userIdentifier, $customerEmail),
                                'usage_limit' => $discount->usage_limit,
                                'allow_multiple' => $discount->allow_multiple_uses_per_user
                            ]);

                            return response()->json([
                                'success' => false,
                                'message' => 'El descuento ha alcanzado su límite de uso o ya fue utilizado por este usuario'
                            ], 400);
                        }
                        // Obtener información de identificación del usuario
                        $userIdentifier = $request->user_identifier ?: $request->ip();
                        $customerEmail = $request->customer_email;
                        $customerPhone = $request->customer_phone;

                        \Log::info('🚀 Registrando uso del descuento', [
                            'discount_code' => $discount->code,
                            'invoice_number' => $invoice->number,
                            'user_identifier' => $userIdentifier,
                            'customer_email' => $customerEmail,
                            'customer_phone' => $customerPhone,
                            'discount_amount' => $discountData['amount'] ?? 0,
                            'before_used_count' => $discount->used_count
                        ]);

                        try {
                            // Registrar el uso del descuento
                            $discount->recordUsage(
                                $discountData['amount'] ?? 0,
                                $userIdentifier,
                                $customerEmail,
                                $customerPhone,
                                $invoice->number,
                                [
                                    'user_agent' => $request->header('User-Agent'),
                                    'ip_address' => $request->ip(),
                                    'timestamp' => now()->toISOString()
                                ]
                            );

                            \Log::info('✅ Uso del descuento registrado exitosamente', [
                                'discount_code' => $discount->code,
                                'after_used_count' => $discount->fresh()->used_count
                            ]);

                            // Crear registro en applied_discounts
                            AppliedDiscount::create([
                                'customer_id' => $data['customer_id'],
                                'invoice_id' => $invoice->id,
                                'discount_type' => 'promotional',
                                'discount_value' => $discount->value,
                                'discount_amount' => $discountData['amount'] ?? 0,
                                'discount_reason' => "Código promocional: {$discount->code}",
                                'applied_by' => (string)($userId ?? 1),
                                'applied_at' => now()
                            ]);

                            \Log::info("✅ Descuento promocional aplicado completamente - Código: {$discount->code}, Factura: {$invoice->number}, Usuario: {$userIdentifier}");

                        } catch (\Exception $e) {
                            \Log::error('❌ Error registrando uso del descuento', [
                                'discount_code' => $discount->code,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                            throw $e; // Re-lanzar para que falle la transacción
                        }
                    } else {
                        \Log::warning('⚠️ Descuento no encontrado en BD', [
                            'discount_id' => $discountData['discount_id']
                        ]);
                    }
                } else {
                    \Log::warning('⚠️ No se encontró discount_id en los datos', [
                        'discount_data' => $discountData
                    ]);
                }
            } else {
                \Log::info('ℹ️ No hay descuento promocional en esta factura');
            }

            DB::commit();

            // Actualizar totales de la sesión de caja si es una venta
            if ($data['type'] !== 'quote' && isset($currentCashSession)) {
                $currentCashSession->updateSalesTotals();
                $currentCashSession->save();
            }

            // 🎁 LOYALTY POINTS: Ganar puntos por compra (solo para ventas pagadas, no cotizaciones)
            if ($data['type'] !== 'quote' && $data['status'] === 'paid') {
                try {
                    $systemSettings = \App\Models\SystemSetting::first();

                    if ($systemSettings && $systemSettings->enable_loyalty_system) {
                        $customer = \App\Models\Customer::find($data['customer_id']);

                        if ($customer) {
                            // Calcular puntos ganados basados en el total de la compra
                            $pointsEarned = \App\Models\Customer::calculatePointsToEarn($invoice->total);

                            if ($pointsEarned > 0) {
                                $customer->earnLoyaltyPoints(
                                    $invoice->total,
                                    $invoice->id,
                                    Auth::id()
                                );
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // No fallar la venta si hay error en loyalty points
                    \Log::error('❌ Error al procesar puntos de fidelización', [
                        'error' => $e->getMessage(),
                        'invoice_id' => $invoice->id
                    ]);
                }
            }

            $invoice->load(['customer', 'invoiceItems']);

            \Log::info('✅ Factura POS creada exitosamente', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->number,
                'total' => $invoice->total
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Factura creada exitosamente desde POS',
                'data' => $invoice
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();

            \Log::error('❌ ERROR CRÍTICO al crear factura POS', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la factura desde POS',
                'error' => $e->getMessage(),
                'details' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Generate PDF for invoice
     */
    public function generatePDF(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            // Validar datos requeridos básicos
            if (!isset($data['invoice_number']) || !isset($data['customer']) || !isset($data['items'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Faltan datos requeridos (invoice_number, customer, items)'
                ], 400);
            }

            // Generar PDF simple
            $pdfContent = $this->generateFallbackPDF($data);

            return response()->json([
                'success' => true,
                'message' => 'PDF generado exitosamente',
                'pdf_base64' => base64_encode($pdfContent)
            ]);

        } catch (\Exception $e) {
            error_log("Error in generatePDF: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error generando PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send invoice via WhatsApp
     */
    public function sendWhatsApp(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string',
                'invoice_id' => 'required|integer|exists:invoices,id',
                'customer_name' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $phone = $request->input('phone');
            $invoiceId = $request->input('invoice_id');
            $customerName = $request->input('customer_name', '');

            // Buscar la factura
            $invoice = Invoice::findOrFail($invoiceId);

            // Generar mensaje personalizado
            $message = "🧾 *Factura #{$invoice->invoice_number}*\n\n";
            $message .= "📅 Fecha: " . $invoice->date->format('d/m/Y') . "\n";
            if ($customerName) {
                $message .= "👤 Cliente: {$customerName}\n";
            }
            $message .= "💰 Total: $" . number_format($invoice->total, 0, ',', '.') . "\n\n";
            $message .= "¡Gracias por su compra! 😊";

            // Validar formato de número colombiano
            if (!preg_match('/^\+57[3][0-9]{9}$/', $phone)) {
                return response()->json([
                    'success' => false,
                    'error' => 'INVALID_PHONE',
                    'message' => 'Formato de número inválido. Use: +573001234567'
                ], 400);
            }

            // Generar PDF de la factura
            $invoiceData = [
                'type' => 'invoice',
                'invoice_number' => $invoice->number,
                'date' => $invoice->date->format('Y-m-d'),
                'due_date' => $invoice->due_date ? $invoice->due_date->format('Y-m-d') : null,
                'customer' => $invoice->customer ? $invoice->customer->name : 'Cliente General',
                'subtotal' => $invoice->subtotal,
                'tax_amount' => $invoice->tax_amount,
                'total' => $invoice->total,
                'items' => collect(json_decode($invoice->items, true) ?: [])->map(function($item) {
                    return [
                        'product_name' => $item['product_name'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total' => $item['quantity'] * $item['unit_price']
                    ];
                })->toArray()
            ];
            $pdfContent = $this->generateFallbackPDF($invoiceData);
            $fileName = 'factura_' . $invoice->number . '_' . time() . '.pdf';

            // Guardar temporalmente el PDF
            $tempPath = storage_path('app/temp/' . $fileName);
            if (!file_exists(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0777, true);
            }
            file_put_contents($tempPath, $pdfContent);

            // Usar servidor WhatsApp HTTP para enviar mensaje
            try {
                // Enviar a servidor WhatsApp via HTTP usando cURL
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'http://localhost:3002/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                    'phone' => $phone,
                    'message' => $message,
                    'pdfPath' => $tempPath
                ]));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json'
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);

                $response = curl_exec($ch);

                if (curl_error($ch)) {
                    throw new \Exception('cURL Error: ' . curl_error($ch));
                }

                curl_close($ch);
                $result = json_decode($response, true);

                // Limpiar archivo temporal
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

                // Verificar resultado
                if ($result && $result['success']) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Mensaje enviado por WhatsApp exitosamente',
                        'phone' => $phone,
                        'timestamp' => now()
                    ]);
                } else {
                    $errorMsg = $result['error'] ?? 'Error desconocido del servidor WhatsApp';
                    return response()->json([
                        'success' => false,
                        'error' => 'WHATSAPP_API_ERROR',
                        'message' => 'Error enviando mensaje: ' . $errorMsg
                    ], 500);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'WHATSAPP_API_ERROR',
                    'message' => 'Error del servicio WhatsApp: ' . $e->getMessage()
                ], 500);
            }        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error enviando WhatsApp',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send PDF file via WhatsApp (receives PDF from frontend)
     */
    public function sendWhatsAppPDF(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string',
                'pdf' => 'required|file|mimes:pdf',
                'message' => 'nullable|string',
                'customerName' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'VALIDATION_ERROR',
                    'message' => 'Datos inválidos: ' . implode(', ', $validator->errors()->all())
                ], 422);
            }

            $phone = $request->input('phone');
            $message = $request->input('message', '¡Gracias por su compra! Adjuntamos su factura.');
            $customerName = $request->input('customerName', 'Cliente');

            // Validar formato del teléfono
            if (!preg_match('/^\+57[0-9]{10}$/', $phone)) {
                return response()->json([
                    'success' => false,
                    'error' => 'INVALID_PHONE',
                    'message' => 'Formato de número inválido. Use: +5730012345'
                ], 422);
            }

            // Guardar el PDF temporalmente
            $pdfFile = $request->file('pdf');
            $tempPath = $pdfFile->storeAs('temp', 'factura_' . time() . '.pdf', 'public');
            $fullPath = storage_path('app/public/' . $tempPath);

            try {
                // Enviar al servicio de WhatsApp
                $ch = curl_init('http://localhost:3002/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                    'phone' => $phone,
                    'message' => $message,
                    'pdfPath' => $fullPath
                ]));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json'
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 60); // 60 segundos para envío de PDF

                $response = curl_exec($ch);

                if (curl_error($ch)) {
                    throw new \Exception('cURL Error: ' . curl_error($ch));
                }

                curl_close($ch);
                $result = json_decode($response, true);

                // Limpiar archivo temporal
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }

                if (!$result || !isset($result['success'])) {
                    return response()->json([
                        'success' => false,
                        'error' => 'WHATSAPP_API_ERROR',
                        'message' => 'Respuesta inválida del servicio WhatsApp'
                    ], 500);
                }

                if (!$result['success']) {
                    return response()->json([
                        'success' => false,
                        'error' => $result['error'] ?? 'WHATSAPP_API_ERROR',
                        'message' => $result['message'] ?? 'Error del servicio WhatsApp'
                    ], 500);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Mensaje enviado por WhatsApp exitosamente',
                    'phone' => $phone,
                    'timestamp' => now()
                ]);

            } catch (\Exception $e) {
                // Limpiar archivo temporal en caso de error
                if (isset($fullPath) && file_exists($fullPath)) {
                    unlink($fullPath);
                }

                return response()->json([
                    'success' => false,
                    'error' => 'WHATSAPP_API_ERROR',
                    'message' => 'Error del servicio WhatsApp: ' . $e->getMessage()
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error enviando PDF por WhatsApp',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send invoice via Email
     */
    public function sendEmail(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'pdf' => 'required|file|mimes:pdf',
                'subject' => 'required|string',
                'message' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $email = $request->input('email');
            $subject = $request->input('subject');
            $message = $request->input('message');
            $pdfFile = $request->file('pdf');

            // TODO: Implementar envío real de email con Laravel Mail
            // Por ahora simular envío exitoso
            sleep(1); // Simular tiempo de procesamiento

            return response()->json([
                'success' => true,
                'message' => 'Email enviado exitosamente',
                'email' => $email,
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error enviando email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate complete PDF content using HTML template
     */
    private function generateSimplePDF($data): string
    {
        try {
            // Verificar si existe el archivo template
            $templatePath = resource_path('views/invoice-template.html');

            if (!file_exists($templatePath)) {
                error_log("Template file not found: {$templatePath}");
                return $this->generateFallbackPDF($data);
            }

            // Cargar plantilla HTML
            $htmlTemplate = file_get_contents($templatePath);

            if ($htmlTemplate === false) {
                error_log("Could not read template file: {$templatePath}");
                return $this->generateFallbackPDF($data);
            }

            // Generar sección de productos
            $productsHtml = '';
            foreach ($data['items'] as $item) {
                $itemName = $item['name'] ?? $item['product_name'] ?? 'Producto';
                $quantity = $item['quantity'] ?? 1;
                $price = $item['price'] ?? $item['unit_price'] ?? 0;
                $subtotal = $quantity * $price;

                $productsHtml .= "<div class='product-line'>{$itemName}</div>";
                $productsHtml .= "<div class='product-line'>{$quantity} x \${$price}</div>";
                $productsHtml .= "<div class='product-line'>\${$subtotal}</div>";
                $productsHtml .= "<div style='margin: 5px 0;'></div>";
            }

            // Reemplazar variables en la plantilla
            $replacements = [
                '{{ company_name }}' => $data['company']['name'] ?? 'Mi Tienda POS',
                '{{ invoice_number }}' => $data['invoice_number'] ?? 'N/A',
                '{{ date }}' => date('d/m/Y, H:i', strtotime($data['date'] ?? 'now')),
                '{{ due_date }}' => date('d/m/Y, H:i', strtotime($data['due_date'] ?? '+1 month')),
                '{{ seller }}' => 'Vendedor Demo',
                '{{ customer_name }}' => $data['customer']['name'] ?? 'Cliente General',
                '{{ products_section }}' => $productsHtml,
                '{{ subtotal }}' => number_format($data['subtotal'] ?? 0, 0),
                '{{ tax_rate }}' => '0',
                '{{ tax_amount }}' => number_format($data['tax'] ?? 0, 0),
                '{{ total }}' => number_format($data['total'] ?? 0, 0),
                '{{ payment_method }}' => ucfirst($data['payment_method'] ?? 'Efectivo'),
                '{{ cash_received }}' => number_format($data['cash_received'] ?? 0, 0),
                '{{ change_due }}' => number_format($data['change_due'] ?? 0, 0),
            ];

            $html = str_replace(array_keys($replacements), array_values($replacements), $htmlTemplate);

            // Generar PDF básico usando HTML convertido a texto plano para PDF simple
            $pdfContent = $this->htmlToPdfPlain($html, $data);

            return $pdfContent;

        } catch (\Exception $e) {
            error_log("Error in generateSimplePDF: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            // Fallback al PDF simple original si hay error
            return $this->generateFallbackPDF($data);
        }
    }

    /**
     * Convert HTML to a simple PDF-like format
     */
    private function htmlToPdfPlain($html, $data): string
    {
        // Generar un PDF más completo con los datos reales
        $companyName = $data['company']['name'] ?? 'Mi Tienda POS';
        $invoiceNumber = $data['invoice_number'] ?? 'N/A';
        $customerName = $data['customer']['name'] ?? 'Cliente General';
        $total = number_format($data['total'] ?? 0, 0);
        $subtotal = number_format($data['subtotal'] ?? 0, 0);
        $taxAmount = number_format($data['tax'] ?? 0, 0);
        $paymentMethod = ucfirst($data['payment_method'] ?? 'Efectivo');
        $cashReceived = number_format($data['cash_received'] ?? 0, 0);
        $changeDue = number_format($data['change_due'] ?? 0, 0);
        $date = date('d/m/Y, H:i', strtotime($data['date'] ?? 'now'));
        $dueDate = date('d/m/Y, H:i', strtotime($data['due_date'] ?? '+1 month'));

        // Generar líneas de productos
        $productLines = '';
        foreach ($data['items'] as $item) {
            $itemName = $item['name'] ?? $item['product_name'] ?? 'Producto';
            $quantity = $item['quantity'] ?? 1;
            $price = $item['price'] ?? $item['unit_price'] ?? 0;
            $itemSubtotal = number_format($quantity * $price, 0);
            $productLines .= "({$itemName}) Tj 0 -15 Td ({$quantity} x \\${$price}) Tj 0 -15 Td (\\${$itemSubtotal}) Tj 0 -20 Td ";
        }

        $content = "%PDF-1.4
1 0 obj
<<
/Type /Catalog
/Pages 2 0 R
>>
endobj

2 0 obj
<<
/Type /Pages
/Kids [3 0 R]
/Count 1
>>
endobj

3 0 obj
<<
/Type /Page
/Parent 2 0 R
/MediaBox [0 0 612 792]
/Contents 4 0 R
>>
endobj

4 0 obj
<<
/Length 800
>>
stream
BT
/F1 16 Tf
50 750 Td
({$companyName}) Tj
0 -25 Td
(Factura #: {$invoiceNumber}) Tj
0 -20 Td
(Fecha: {$date}) Tj
0 -15 Td
(Vence: {$dueDate}) Tj
0 -25 Td
(Vendedor: Vendedor Demo) Tj
0 -15 Td
(Cliente: {$customerName}) Tj
0 -30 Td
/F1 14 Tf
(PRODUCTOS) Tj
0 -25 Td
/F1 12 Tf
{$productLines}
0 -20 Td
(================================) Tj
0 -20 Td
(Subtotal: \\${$subtotal}) Tj
0 -15 Td
(IVA \\(0%\\): \\${$taxAmount}) Tj
0 -15 Td
/F1 14 Tf
(TOTAL: \\${$total}) Tj
0 -25 Td
/F1 12 Tf
(PAGO) Tj
0 -15 Td
({$paymentMethod}: \\${$cashReceived}) Tj
0 -15 Td
(Cambio: \\${$changeDue}) Tj
0 -30 Td
(¡GRACIAS POR SU COMPRA!) Tj
0 -15 Td
(Conserve este recibo) Tj
0 -15 Td
(Devoluciones: 30 días) Tj
ET
endstream
endobj

xref
0 5
0000000000 65535 f
0000000010 00000 n
0000000079 00000 n
0000000173 00000 n
0000000301 00000 n
trailer
<<
/Size 5
/Root 1 0 R
>>
startxref
1200
%%EOF";

        return $content;
    }

    /**
     * Get WhatsApp connection status
     */
    public function getWhatsAppStatus(): JsonResponse
    {
        try {
            // Hacer petición al servidor de WhatsApp
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost:3002/status');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15); // 15 segundos para estado
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                throw new \Exception("Error connecting to WhatsApp service: " . $error);
            }

            if ($httpCode !== 200) {
                throw new \Exception("WhatsApp service returned HTTP " . $httpCode);
            }

            $status = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON response from WhatsApp service");
            }

            return response()->json([
                'success' => true,
                'status' => $status ?: ['connected' => false, 'error' => 'Invalid response']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => true, // Mantener success=true para no romper el frontend
                'status' => ['connected' => false, 'error' => $e->getMessage()]
            ]);
        }
    }

    /**
     * Get WhatsApp QR code for authentication
     */
    public function getWhatsAppQR(): JsonResponse
    {
        try {
            // Hacer petición al servidor de WhatsApp para obtener QR
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost:3002/qr');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                throw new \Exception("Error connecting to WhatsApp service: " . $error);
            }

            if ($httpCode !== 200) {
                throw new \Exception("WhatsApp service returned HTTP " . $httpCode);
            }

            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON response from WhatsApp service");
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error obteniendo QR code: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Initialize WhatsApp service
     */
    public function initializeWhatsApp(): JsonResponse
    {
        try {
            // Hacer petición al servicio de WhatsApp para inicializar
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost:3002/initialize');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                // Si no puede conectar, intentar iniciar el servicio
                $scriptPath = base_path('whatsapp-server.js');
                $command = "node {$scriptPath} > /dev/null 2>&1 &";
                shell_exec($command);

                return response()->json([
                    'success' => true,
                    'message' => 'Servicio WhatsApp iniciado. Esperando código QR...'
                ]);
            }

            if ($httpCode !== 200) {
                throw new \Exception("WhatsApp service returned HTTP " . $httpCode);
            }

            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON response from WhatsApp service");
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error iniciando WhatsApp: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Disconnect WhatsApp service
     */
    public function disconnectWhatsApp(): JsonResponse
    {
        try {
            // Hacer petición al servicio de WhatsApp para desconectar
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost:3002/disconnect');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                // Si no puede conectar al servicio, intentar terminar el proceso
                shell_exec("pkill -f whatsapp-server.js");

                return response()->json([
                    'success' => true,
                    'message' => 'WhatsApp desconectado (proceso terminado)'
                ]);
            }

            if ($httpCode !== 200) {
                throw new \Exception("WhatsApp service returned HTTP " . $httpCode);
            }

            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON response from WhatsApp service");
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error desconectando WhatsApp: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Clean WhatsApp session files
     */
    public function cleanWhatsAppSession(): JsonResponse
    {
        try {
            // Hacer petición al servicio de WhatsApp para limpiar sesión
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost:3002/clean-session');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo conectar al servicio de WhatsApp'
                ], 500);
            }

            if ($httpCode !== 200) {
                throw new \Exception("WhatsApp service returned HTTP " . $httpCode);
            }

            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON response from WhatsApp service");
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error limpiando sesión de WhatsApp: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate a simple PDF with invoice details
     */
    private function generateFallbackPDF($data): string
    {
        // Usar "Mi Tienda POS" como valor por defecto, igual que en el recibo
        $companyName = $data['company']['name'] ?? 'Mi Tienda POS';
        $documentType = $data['type'] ?? 'invoice';
        $documentNumber = '';

        if ($documentType === 'quotation') {
            $documentNumber = $data['quotation_number'] ?? 'N/A';
            $documentTitle = 'Cotización #';
        } else {
            $documentNumber = $data['invoice_number'] ?? 'N/A';
            $documentTitle = 'Factura #';
        }

        $customerName = $data['customer']['name'] ?? 'Cliente General';

        // Formatear fecha como en el recibo: 05/11/2025, 11:07
        $date = date('d/m/Y, H:i');
        $dueDate = date('d/m/Y, H:i', strtotime('+30 days'));

        // Datos del vendedor y sistema
        $cashier = 'Vendedor Demo';

        // Formatear totales con separador de miles como en el recibo (punto)
        $subtotal = number_format($data['subtotal'] ?? 0, 0, '.', '.');
        $taxRate = 0;
        $tax = number_format($data['tax'] ?? 0, 0, '.', '.');
        $total = number_format($data['total'] ?? 0, 0, '.', '.');

        // Datos de pago (solo para facturas)
        $cashReceived = number_format($data['cash_received'] ?? $data['total'] ?? 0, 0, '.', '.');
        $change = number_format($data['change_due'] ?? 0, 0, '.', '.');

        // Generar líneas de productos
        $itemsText = '';
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $itemName = $item['name'] ?? $item['product_name'] ?? 'Producto';
                $quantity = $item['quantity'] ?? 1;
                $priceRaw = $item['price'] ?? $item['unit_price'] ?? 0;
                $price = number_format($priceRaw, 0, '.', '.');
                $itemTotalRaw = $quantity * $priceRaw;
                $itemTotal = number_format($itemTotalRaw, 0, '.', '.');

                $itemsText .= "0 -20 Td (" . $itemName . ") Tj ";
                $itemsText .= "0 -15 Td (" . $quantity . " x \$" . $price . ") Tj ";
                $itemsText .= "200 0 Td (\$" . $itemTotal . ") Tj -200 0 Td ";
            }
        }

        // Texto específico según el tipo de documento
        $footerText = '';
        if ($documentType === 'quotation') {
            $footerText = "0 -30 Td (COTIZACION VALIDA POR 30 DIAS) Tj 0 -15 Td (Codigo: " . $documentNumber . ") Tj 0 -15 Td (Conserve este codigo para realizar) Tj 0 -15 Td (su compra posteriormente.) Tj";
        } else {
            $footerText = "0 -15 Td (Efectivo:) Tj 200 0 Td (\$" . $cashReceived . ") Tj -200 -15 Td (Cambio:) Tj 200 0 Td (\$" . $change . ") Tj -200 -30 Td (GRACIAS POR SU COMPRA) Tj 0 -15 Td (Conserve este comprobante) Tj 0 -15 Td (como garantia.) Tj";
        }

        $baseLength = 800;
        $pdfLength = $baseLength + strlen($itemsText) + strlen($footerText);
        $xrefPos = 650 + strlen($itemsText) + strlen($footerText);

        $content = "%PDF-1.4
1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj
2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj
3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Contents 4 0 R>>endobj
4 0 obj<</Length " . $pdfLength . ">>stream
BT
/F1 16 Tf 306 750 Td (" . $companyName . ") Tj
/F1 12 Tf -256 -40 Td (" . $documentTitle . ") Tj 200 0 Td (" . $documentNumber . ") Tj
-200 -20 Td (Fecha:) Tj 200 0 Td (" . $date . ") Tj
-200 -20 Td (Vence:) Tj 200 0 Td (" . $dueDate . ") Tj
-200 -20 Td (Vendedor:) Tj 200 0 Td (" . $cashier . ") Tj
-200 -20 Td (Cliente:) Tj 200 0 Td (" . $customerName . ") Tj
-200 -40 Td (PRODUCTOS) Tj
0 -10 Td (...................................) Tj
" . $itemsText . "
0 -15 Td (...................................) Tj
0 -20 Td (Subtotal:) Tj 200 0 Td (\$" . $subtotal . ") Tj
-200 -15 Td (IVA (" . $taxRate . "%):) Tj 200 0 Td (\$" . $tax . ") Tj
-200 -20 Td (TOTAL:) Tj 200 0 Td (\$" . $total . ") Tj
" . $footerText . "
ET
endstream endobj
xref
0 5
0000000000 65535 f
0000000015 00000 n
0000000068 00000 n
0000000125 00000 n
0000000213 00000 n
trailer<</Size 5/Root 1 0 R>>
startxref
" . $xrefPos . "
%%EOF";

        return $content;
    }

    /**
     * Get invoice items handling both old JSON format and new relation format
     */
    private function getInvoiceItems($invoice)
    {
        // SIEMPRE usar consulta manual con JOIN para garantizar que tengamos image_url
        $manualItems = \DB::table('invoice_items')
            ->leftJoin('products', 'invoice_items.product_id', '=', 'products.id')
            ->where('invoice_items.invoice_id', $invoice->id)
            ->select(
                'invoice_items.id',
                'invoice_items.product_id',
                'invoice_items.product_name',
                'invoice_items.quantity',
                'invoice_items.unit_price as price',
                'invoice_items.subtotal',
                'products.image_url'
            )
            ->get();

        if ($manualItems->count() > 0) {
            return $manualItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'quantity' => (int) $item->quantity,
                    'price' => (float) $item->price,
                    'subtotal' => (float) $item->subtotal,
                    'image_url' => $item->image_url,
                ];
            });
        }

        // Si tiene items como relación (fallback - no debería llegar aquí)
        if ($invoice->relationLoaded('items') &&
            $invoice->items !== null &&
            $invoice->items instanceof \Illuminate\Database\Eloquent\Collection &&
            $invoice->items->count() > 0) {

            return $invoice->items->map(function ($item) {
                // Buscar image_url del producto si tenemos product_id
                $imageUrl = null;
                if ($item->product_id) {
                    $product = \DB::table('products')
                        ->where('id', $item->product_id)
                        ->select('image_url')
                        ->first();
                    $imageUrl = $product ? $product->image_url : null;
                }

                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name ?? 'Producto sin nombre',
                    'quantity' => (int) $item->quantity,
                    'price' => (float) $item->unit_price,
                    'subtotal' => (float) $item->subtotal,
                    'image_url' => $imageUrl,
                ];
            });
        }

        // Si tiene items como JSON string (formato antiguo)
        $itemsJson = $invoice->getOriginal('items') ?? $invoice->getAttribute('items');
        if (is_string($itemsJson) && !empty($itemsJson)) {
            $itemsArray = json_decode($itemsJson, true);
            if (is_array($itemsArray)) {
                return collect($itemsArray)->map(function ($item) {
                    // Buscar image_url del producto si tenemos product_id
                    $imageUrl = null;
                    if (!empty($item['product_id'])) {
                        $product = \DB::table('products')
                            ->where('id', $item['product_id'])
                            ->select('image_url')
                            ->first();
                        $imageUrl = $product ? $product->image_url : null;
                    }

                    return [
                        'id' => $item['id'] ?? null,
                        'product_id' => $item['product_id'] ?? null,
                        'product_name' => $item['product_name'] ?? '',
                        'quantity' => (int) ($item['quantity'] ?? 0),
                        'price' => (float) ($item['unit_price'] ?? $item['price'] ?? 0),
                        'subtotal' => (float) ($item['subtotal'] ?? (($item['quantity'] ?? 0) * ($item['unit_price'] ?? $item['price'] ?? 0))),
                        'image_url' => $imageUrl,
                    ];
                });
            }
        }

        return collect([]);
    }

    /**
     * Send quotation via WhatsApp (using ID)
     */
    public function sendQuotationWhatsApp(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string',
                'quotation_id' => 'required|integer|exists:invoices,id',
                'customer_name' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $phone = $request->input('phone');
            $quotationId = $request->input('quotation_id');
            $customerName = $request->input('customer_name', '');

            // Buscar la cotización
            $quotation = Invoice::where('id', $quotationId)
                ->where('type', 'quote')
                ->firstOrFail();

            // Generar mensaje personalizado para cotización
            $message = "📋 *Cotización #{$quotation->number}*\n\n";
            $message .= "📅 Fecha: " . $quotation->date->format('d/m/Y') . "\n";
            if ($customerName) {
                $message .= "👤 Cliente: {$customerName}\n";
            }
            $message .= "💰 Total: $" . number_format($quotation->total, 0, ',', '.') . "\n\n";
            $message .= "🔖 Código de cotización: *{$quotation->number}*\n\n";
            $message .= "Puede usar este código para realizar su compra posteriormente.\n\n";
            $message .= "¡Gracias por elegirnos! 😊";

            // Validar formato de número colombiano
            if (!preg_match('/^\+57[3][0-9]{9}$/', $phone)) {
                return response()->json([
                    'success' => false,
                    'error' => 'INVALID_PHONE',
                    'message' => 'Formato de número inválido. Use: +573001234567'
                ], 400);
            }

            // Generar PDF de la cotización
            // Decodificar items si están como JSON string
            $items = $quotation->items;
            if (is_string($items)) {
                $items = json_decode($items, true);
            }

            $quotationData = [
                'type' => 'quotation',
                'quotation_number' => $quotation->number,
                'date' => $quotation->date->format('Y-m-d'),
                'customer' => $quotation->customer ? $quotation->customer->name : 'Cliente General',
                'subtotal' => $quotation->subtotal,
                'tax_amount' => $quotation->tax_amount,
                'total' => $quotation->total,
                'items' => collect($items)->map(function($item) {
                    return [
                        'product_name' => $item['product_name'] ?? $item['name'] ?? 'Producto',
                        'quantity' => $item['quantity'] ?? 1,
                        'unit_price' => $item['unit_price'] ?? $item['price'] ?? 0,
                        'total' => ($item['quantity'] ?? 1) * ($item['unit_price'] ?? $item['price'] ?? 0)
                    ];
                })->toArray()
            ];
            $pdfContent = $this->generateFallbackPDF($quotationData);
            $fileName = 'cotizacion_' . $quotation->number . '_' . time() . '.pdf';

            // Guardar temporalmente el PDF
            $tempPath = storage_path('app/temp/' . $fileName);
            if (!file_exists(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0777, true);
            }
            file_put_contents($tempPath, $pdfContent);

            // Usar servidor WhatsApp HTTP para enviar mensaje
            try {
                // Enviar a servidor WhatsApp via HTTP usando cURL
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'http://localhost:3002/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                    'phone' => $phone,
                    'message' => $message,
                    'pdfPath' => $tempPath
                ]));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json'
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);

                $response = curl_exec($ch);

                if (curl_error($ch)) {
                    throw new \Exception('cURL Error: ' . curl_error($ch));
                }

                curl_close($ch);
                $result = json_decode($response, true);

                // Limpiar archivo temporal
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

                // Verificar resultado
                if ($result && $result['success']) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Cotización enviada por WhatsApp exitosamente',
                        'phone' => $phone,
                        'timestamp' => now()
                    ]);
                } else {
                    $errorMsg = $result['error'] ?? 'Error desconocido del servidor WhatsApp';
                    return response()->json([
                        'success' => false,
                        'error' => 'WHATSAPP_API_ERROR',
                        'message' => 'Error enviando cotización: ' . $errorMsg
                    ], 500);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'WHATSAPP_API_ERROR',
                    'message' => 'Error del servicio WhatsApp: ' . $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error enviando cotización por WhatsApp',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send quotation PDF via WhatsApp (receives PDF from frontend)
     */
    public function sendQuotationWhatsAppPDF(Request $request): JsonResponse
    {
        try {
            // Log de datos recibidos para debug
            \Log::info('Recibiendo datos para sendQuotationWhatsAppPDF:', [
                'phone' => $request->input('phone'),
                'customerName' => $request->input('customerName'),
                'message_length' => strlen($request->input('message', '')),
                'has_pdf' => $request->hasFile('pdf'),
                'pdf_size' => $request->hasFile('pdf') ? $request->file('pdf')->getSize() : 0,
                'pdf_mime' => $request->hasFile('pdf') ? $request->file('pdf')->getMimeType() : null,
                'pdf_extension' => $request->hasFile('pdf') ? $request->file('pdf')->getClientOriginalExtension() : null,
            ]);

            $validator = Validator::make($request->all(), [
                'phone' => 'required|string',
                'pdf' => 'required|file|mimes:pdf|max:10240', // Max 10MB
                'message' => 'nullable|string',
                'customerName' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                \Log::error('Validación fallida en sendQuotationWhatsAppPDF:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'error' => 'VALIDATION_ERROR',
                    'message' => 'Datos inválidos: ' . implode(', ', $validator->errors()->all()),
                    'errors' => $validator->errors()->toArray()
                ], 422);
            }

            $phone = $request->input('phone');
            $customerName = $request->input('customerName', '');
            $customMessage = $request->input('message', '');

            // Validar formato de número colombiano
            if (!preg_match('/^\+57[3][0-9]{9}$/', $phone)) {
                return response()->json([
                    'success' => false,
                    'error' => 'INVALID_PHONE',
                    'message' => 'Formato de número inválido. Use: +573001234567'
                ], 400);
            }

            // Obtener el archivo PDF
            $pdfFile = $request->file('pdf');
            $fileName = 'cotizacion_' . time() . '.pdf';

            // Guardar el archivo temporalmente
            $tempPath = storage_path('app/temp/' . $fileName);
            if (!file_exists(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0777, true);
            }
            $pdfFile->move(dirname($tempPath), $fileName);

            // Generar mensaje por defecto si no se proporciona
            if (empty($customMessage)) {
                $customMessage = "📋 *Su cotización está lista*\n\n";
                if ($customerName) {
                    $customMessage .= "👤 Cliente: {$customerName}\n\n";
                }
                $customMessage .= "Adjunto encontrará su cotización en PDF.\n\n";
                $customMessage .= "¡Gracias por elegirnos! 😊";
            }

            // Enviar al servicio de WhatsApp
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'http://localhost:3002/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                    'phone' => $phone,
                    'message' => $customMessage,
                    'pdfPath' => $tempPath
                ]));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json'
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);

                $response = curl_exec($ch);

                if (curl_error($ch)) {
                    throw new \Exception('cURL Error: ' . curl_error($ch));
                }

                curl_close($ch);

                // Limpiar archivo temporal
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

                $result = json_decode($response, true);

                if (!$result) {
                    return response()->json([
                        'success' => false,
                        'error' => 'WHATSAPP_API_ERROR',
                        'message' => 'Respuesta inválida del servicio WhatsApp'
                    ], 500);
                }

                if (!$result['success']) {
                    return response()->json([
                        'success' => false,
                        'error' => $result['error'] ?? 'WHATSAPP_API_ERROR',
                        'message' => $result['message'] ?? 'Error del servicio WhatsApp'
                    ], 500);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Cotización enviada por WhatsApp exitosamente',
                    'phone' => $phone,
                    'customerName' => $customerName,
                    'timestamp' => now()
                ]);

            } catch (\Exception $e) {
                // Limpiar archivo temporal en caso de error
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

                return response()->json([
                    'success' => false,
                    'error' => 'WHATSAPP_API_ERROR',
                    'message' => 'Error del servicio WhatsApp: ' . $e->getMessage()
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'SERVER_ERROR',
                'message' => 'Error enviando PDF de cotización por WhatsApp',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
