<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\InventoryMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryTestController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        try {
            // Obtener período desde query parameter (por defecto: month)
            $period = $request->get('period', 'month');

            // Calcular fechas según el período
            $now = Carbon::now();

            // Si es rango personalizado, usar start_date y end_date
            if ($period === 'custom') {
                $startDate = $request->get('start_date')
                    ? Carbon::parse($request->get('start_date'))->startOfDay()
                    : $now->copy()->startOfMonth();

                $endDate = $request->get('end_date')
                    ? Carbon::parse($request->get('end_date'))->endOfDay()
                    : $now->copy()->endOfDay();
            } else {
                $startDate = $this->getStartDateForPeriod($period, $now);
                $endDate = $now->copy()->endOfDay();
            }

            $currentYear = $now->year;
            $currentMonth = $now->month;

            $totalProducts = Product::count();
            $activeProducts = Product::where('active', true)->count();
            $lowStockProducts = Product::whereColumn('current_stock', '<=', 'min_stock')->count();
            $outOfStockProducts = Product::where('current_stock', '<=', 0)->count();

            // Valor de COSTO (inventario)
            $totalInventoryValue = DB::selectOne('SELECT SUM(current_stock * cost_price) as total FROM products WHERE active = 1 AND current_stock > 0')->total ?? 0;

            // Valor de VENTA (potencial)
            $totalSaleValue = DB::selectOne('SELECT SUM(current_stock * sale_price) as total FROM products WHERE active = 1 AND current_stock > 0')->total ?? 0;

            // Ganancia potencial
            $potentialProfit = $totalSaleValue - $totalInventoryValue;
            $inventoryProfitMargin = $totalInventoryValue > 0 ? round(($potentialProfit / $totalInventoryValue) * 100, 1) : 0;

            // Ventas del período seleccionado - DINÁMICO
            $periodSales = DB::table('invoices')
                ->where('status', 'paid')
                ->where('date', '>=', $startDate)
                ->where('date', '<=', $endDate)
                ->sum('total');

            $periodTransactions = DB::table('invoices')
                ->where('status', 'paid')
                ->where('date', '>=', $startDate)
                ->where('date', '<=', $endDate)
                ->count();

            // Movimientos del período seleccionado - DINÁMICO
            $periodMovements = InventoryMovement::where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->selectRaw('
                    SUM(CASE WHEN quantity > 0 THEN quantity ELSE 0 END) as entries,
                    SUM(CASE WHEN quantity < 0 THEN ABS(quantity) ELSE 0 END) as exits
                ')
                ->first();

            $stockEntries = intval($periodMovements->entries ?? 0);
            $stockExits = intval($periodMovements->exits ?? 0);
            $stockNet = $stockEntries - $stockExits;

            // Movimientos recientes DINÁMICOS (últimos 10)
            $recentMovements = InventoryMovement::with(['product:id,name'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($movement) {
                    return [
                        'id' => $movement->id,
                        'product_name' => $movement->product ? $movement->product->name : 'Producto eliminado',
                        'type' => $movement->type,
                        'quantity' => $movement->quantity,
                        'reference' => $movement->reference ?? 'Sin referencia',
                        'movement_date' => $movement->movement_date,
                        'created_at' => $movement->created_at->format('Y-m-d H:i:s')
                    ];
                });

            // Productos más vendidos del período seleccionado - DESDE INVOICES
            $invoicesThisPeriod = DB::table('invoices')
                ->where('status', 'paid')
                ->where('date', '>=', $startDate)
                ->where('date', '<=', $endDate)
                ->select('items')
                ->get();

            $productSales = [];
            foreach ($invoicesThisPeriod as $invoice) {
                $items = json_decode($invoice->items, true);
                if (is_array($items)) {
                    foreach ($items as $item) {
                        $productId = $item['product_id'] ?? null;
                        $quantity = $item['quantity'] ?? 0;
                        $revenue = ($item['unit_price'] ?? 0) * $quantity;

                        if ($productId) {
                            if (!isset($productSales[$productId])) {
                                $productSales[$productId] = [
                                    'quantity' => 0,
                                    'revenue' => 0
                                ];
                            }
                            $productSales[$productId]['quantity'] += $quantity;
                            $productSales[$productId]['revenue'] += $revenue;
                        }
                    }
                }
            }

            // Ordenar por cantidad y obtener top 5
            uasort($productSales, function($a, $b) {
                return $b['quantity'] <=> $a['quantity'];
            });

            $topProductIds = array_slice(array_keys($productSales), 0, 5);
            $topSellingProducts = collect($topProductIds)->map(function($productId) use ($productSales) {
                $product = Product::find($productId);
                return [
                    'id' => $productId,
                    'name' => $product ? $product->name : 'Producto #' . $productId,
                    'total_quantity_sold' => $productSales[$productId]['quantity'],
                    'total_revenue' => $productSales[$productId]['revenue']
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'metrics' => [
                        'totalProducts' => $totalProducts,
                        'activeProducts' => $activeProducts,
                        'lowStockProducts' => $lowStockProducts,
                        'outOfStockProducts' => $outOfStockProducts,
                        'totalInventoryValue' => round($totalInventoryValue, 2),
                        'totalSaleValue' => round($totalSaleValue, 2),
                        'potentialProfit' => round($potentialProfit, 2),
                        'inventoryProfitMargin' => $inventoryProfitMargin,
                        'monthlySales' => round($periodSales, 2),
                        'averageProfitMargin' => 40
                    ],
                    'monthlyTransactions' => $periodTransactions,
                    'stockVariation' => [
                        'entries' => $stockEntries,
                        'exits' => $stockExits,
                        'net' => $stockNet
                    ],
                    'recentMovements' => $recentMovements,
                    'topSellingProducts' => $topSellingProducts
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener dashboard: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getStartDateForPeriod($period, $now)
    {
        switch ($period) {
            case 'day':
                return $now->copy()->startOfDay();
            case 'week':
                // Incluir últimos 7 días para capturar todas las ventas recientes
                return $now->copy()->subDays(6)->startOfDay();
            case 'month':
                return $now->copy()->startOfMonth();
            case 'year':
                return $now->copy()->startOfYear();
            default:
                return $now->copy()->startOfMonth();
        }
    }

    private function getStartDate($period)
    {
        $now = Carbon::now();

        switch ($period) {
            case 'day':
                return $now->copy()->startOfDay();
            case 'week':
                return $now->copy()->subDays(6)->startOfDay();
            case 'month':
                return $now->copy()->startOfMonth();
            case 'year':
                return $now->copy()->startOfYear();
            default:
                return $now->copy()->startOfMonth();
        }
    }

    public function products(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', 'month');
            $category = $request->get('category');
            $supplier = $request->get('supplier');
            $search = $request->get('search');

            // Calcular fechas según el período
            $now = Carbon::now();
            $startDate = $this->getStartDateForPeriod($period, $now);

            // Query base para productos
            $query = Product::with(['category:id,name', 'supplier:id,name'])
                ->where('active', true);

            // Aplicar filtros
            if ($category) {
                $query->where('category_id', $category);
            }

            if ($supplier) {
                $query->where('supplier_id', $supplier);
            }

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('barcode', 'LIKE', "%{$search}%")
                      ->orWhere('sku', 'LIKE', "%{$search}%");
                });
            }

            $products = $query->get()->map(function($product) use ($startDate, $now) {
                // Rentabilidad unitaria
                $unitProfit = $product->sale_price - $product->cost_price;
                $unitProfitMargin = $product->cost_price > 0 ?
                    round(($unitProfit / $product->cost_price) * 100, 1) : 0;

                // Rentabilidad total (stock × ganancia unitaria)
                $totalProfit = $product->current_stock * $unitProfit;

                // Ventas del período desde invoices
                $periodSales = $this->getProductSalesInPeriod($product->id, $startDate, $now);

                // Rotación de inventario (días promedio para agotar)
                $rotationDays = $this->calculateInventoryRotation($product->id, $product->current_stock);

                // Stock status
                $stockStatus = 'good';
                if ($product->current_stock <= 0) {
                    $stockStatus = 'out';
                } elseif ($product->current_stock <= $product->min_stock) {
                    $stockStatus = 'low';
                }

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'barcode' => $product->barcode,
                    'sku' => $product->sku,
                    'cost_price' => $product->cost_price,
                    'sale_price' => $product->sale_price,
                    'current_stock' => $product->current_stock,
                    'min_stock' => $product->min_stock,
                    'max_stock' => $product->max_stock,
                    'category' => $product->category ? $product->category->name : 'Sin categoría',
                    'supplier' => $product->supplier ? $product->supplier->name : 'Sin proveedor',
                    'stock_status' => $stockStatus,
                    'unit_profit' => round($unitProfit, 2),
                    'unit_profit_margin' => $unitProfitMargin,
                    'total_profit_potential' => round($totalProfit, 2),
                    'period_sales' => $periodSales,
                    'rotation_days' => $rotationDays
                ];
            });

            // Obtener filtros disponibles
            $categories = DB::table('categories')->select('id', 'name')->get();
            $suppliers = DB::table('suppliers')->select('id', 'name')->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $products,
                    'filters' => [
                        'categories' => $categories,
                        'suppliers' => $suppliers
                    ],
                    'summary' => [
                        'total_products' => $products->count(),
                        'products_in_stock' => $products->where('current_stock', '>', 0)->count(), // Productos disponibles para venta
                        'total_value_cost' => $products->sum(function($p) { return $p['current_stock'] * $p['cost_price']; }),
                        'total_value_sale' => $products->sum(function($p) { return $p['current_stock'] * $p['sale_price']; }),
                        'total_profit_potential' => $products->sum('total_profit_potential'),
                        'out_of_stock' => $products->where('stock_status', 'out')->count(),
                        'low_stock' => $products->where('stock_status', 'low')->count()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener productos: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getProductSalesInPeriod($productId, $startDate, $endDate)
    {
        $invoices = DB::table('invoices')
            ->where('status', 'paid')
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->select('items')
            ->get();

        $totalQuantity = 0;
        $totalRevenue = 0;

        foreach ($invoices as $invoice) {
            $items = json_decode($invoice->items, true);
            if (is_array($items)) {
                foreach ($items as $item) {
                    if (($item['product_id'] ?? null) == $productId) {
                        $quantity = $item['quantity'] ?? 0;
                        $revenue = ($item['unit_price'] ?? 0) * $quantity;
                        $totalQuantity += $quantity;
                        $totalRevenue += $revenue;
                    }
                }
            }
        }

        return [
            'quantity' => $totalQuantity,
            'revenue' => round($totalRevenue, 2)
        ];
    }

    private function calculateInventoryRotation($productId, $currentStock)
    {
        if ($currentStock <= 0) return 0;

        // Calcular ventas promedio de los últimos 30 días
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $sales = $this->getProductSalesInPeriod($productId, $thirtyDaysAgo, Carbon::now());

        $dailyAverageSales = $sales['quantity'] / 30;

        return $dailyAverageSales > 0 ? round($currentStock / $dailyAverageSales, 1) : 999;
    }

    /**
     * Vista de movimientos de inventario (datos reales)
     */
    public function movements(Request $request)
    {
        try {
            $period = $request->get('period', 'week');
            $movementType = $request->get('type', ''); // 'entry', 'exit', 'adjustment'
            $userId = $request->get('user', '');
            $productId = $request->get('product', '');
            $perPage = $request->get('per_page', 50); // Paginación
            $page = $request->get('page', 1);

            // Calcular período
            $endDate = Carbon::now();

            switch ($period) {
                case 'day':
                    $startDate = $endDate->copy()->startOfDay();
                    break;
                case 'week':
                    $startDate = $endDate->copy()->subDays(6);
                    break;
                case 'month':
                    $startDate = $endDate->copy()->startOfMonth();
                    break;
                case 'year':
                    $startDate = $endDate->copy()->startOfYear();
                    break;
                default:
                    $startDate = $endDate->copy()->subDays(6);
            }

            // Obtener movimientos reales de la tabla inventory_movements CON PAGINACIÓN
            $movementsQuery = DB::table('inventory_movements as im')
                ->join('products as p', 'im.product_id', '=', 'p.id')
                ->join('users as u', 'im.user_id', '=', 'u.id')
                ->whereBetween('im.movement_date', [$startDate, $endDate])
                ->when($userId, function ($query) use ($userId) {
                    return $query->where('im.user_id', $userId);
                })
                ->when($productId, function ($query) use ($productId) {
                    return $query->where('im.product_id', $productId);
                })
                ->when($movementType, function ($query) use ($movementType) {
                    if ($movementType === 'entry') {
                        return $query->where('im.type', 'purchase');
                    } elseif ($movementType === 'exit') {
                        return $query->where('im.type', 'sale');
                    } else {
                        return $query->where('im.type', $movementType);
                    }
                })
                ->select(
                    'im.id as movement_id',
                    'im.movement_date',
                    'im.type as movement_type',
                    'im.quantity',
                    'im.previous_stock',
                    'im.new_stock',
                    'im.unit_cost',
                    'im.reference',
                    'im.notes',
                    'p.id as product_id',
                    'p.name as product_name',
                    'p.sale_price',
                    'u.name as user_name'
                )
                ->orderBy('im.movement_date', 'desc');

            // Aplicar paginación
            $movements = $movementsQuery->paginate($perPage);

            // Procesar movimientos para formato estándar
            $processedMovements = $movements->getCollection()->map(function ($movement) {
                $type = $movement->movement_type;
                $reason = $this->getMovementReason($type);
                $isNegative = in_array($type, ['sale', 'adjustment']) && $movement->quantity < 0;

                // Calcular valor total
                $unitPrice = $movement->unit_cost ?: $movement->sale_price ?: 0;
                $totalValue = abs($movement->quantity) * $unitPrice;
                if ($isNegative) {
                    $totalValue = -$totalValue;
                }

                return [
                    'movement_id' => $movement->movement_id,
                    'movement_date' => $movement->movement_date,
                    'movement_type' => $isNegative ? 'exit' : 'entry',
                    'movement_reason' => $reason,
                    'product_id' => $movement->product_id,
                    'product_name' => $movement->product_name,
                    'quantity' => $movement->quantity,
                    'unit_price' => $unitPrice,
                    'total_value' => $totalValue,
                    'user_name' => $movement->user_name,
                    'reference' => $movement->reference ?: 'N/A',
                    'document_number' => $this->extractDocumentNumber($movement->reference),
                    'previous_stock' => $movement->previous_stock,
                    'new_stock' => $movement->new_stock,
                    'notes' => $movement->notes
                ];
            });

            // Calcular resumen
            $summary = [
                'total_movements' => $processedMovements->count(),
                'total_entries' => $processedMovements->where('movement_type', 'entry')->count(),
                'total_exits' => $processedMovements->where('movement_type', 'exit')->count(),
                'total_entry_value' => $processedMovements->where('movement_type', 'entry')->sum('total_value'),
                'total_exit_value' => abs($processedMovements->where('movement_type', 'exit')->sum('total_value')),
                'net_movement' => $processedMovements->sum('total_value'),
            ];

            // Obtener filtros disponibles
            $availableUsers = DB::table('users')
                ->whereIn('id', function ($query) use ($startDate, $endDate) {
                    $query->select('user_id')
                        ->from('inventory_movements')
                        ->whereBetween('movement_date', [$startDate, $endDate]);
                })
                ->select('id', 'name')
                ->get();

            $availableProducts = DB::table('products')
                ->whereIn('id', function ($query) use ($startDate, $endDate) {
                    $query->select('product_id')
                        ->from('inventory_movements')
                        ->whereBetween('movement_date', [$startDate, $endDate]);
                })
                ->select('id', 'name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'movements' => $processedMovements,
                    'summary' => $summary,
                    'pagination' => [
                        'current_page' => $movements->currentPage(),
                        'last_page' => $movements->lastPage(),
                        'per_page' => $movements->perPage(),
                        'total' => $movements->total(),
                        'from' => $movements->firstItem(),
                        'to' => $movements->lastItem()
                    ],
                    'filters' => [
                        'users' => $availableUsers,
                        'products' => $availableProducts,
                    ],
                    'period' => [
                        'start' => $startDate->format('Y-m-d'),
                        'end' => $endDate->format('Y-m-d'),
                        'period_type' => $period
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error obteniendo movimientos: ' . $e->getMessage(),
                'error' => $e->getTraceAsString()
            ], 500);
        }
    }

    private function getMovementReason($type)
    {
        $reasons = [
            'sale' => 'Venta',
            'purchase' => 'Compra',
            'adjustment' => 'Ajuste',
            'return' => 'Devolución',
            'transfer' => 'Transferencia'
        ];
        return $reasons[$type] ?? ucfirst($type);
    }

    private function extractDocumentNumber($reference)
    {
        if (!$reference) return 'N/A';

        // Extraer número de documento de referencias como "Sale#20", "INV-001019", etc.
        if (preg_match('/Sale#(\d+)/', $reference, $matches)) {
            return 'Venta #' . $matches[1];
        }
        if (preg_match('/(INV-\d+)/', $reference, $matches)) {
            return $matches[1];
        }

        return $reference;
    }

    /**
     * Vista por cliente - Usando datos REALES en tiempo real de invoices
     */
    public function customers(Request $request)
    {
        try {
            $period = $request->input('period', 'week');
            $search = $request->input('search');
            $orderBy = $request->input('order_by', 'total_spent');
            $orderDirection = $request->input('order_direction', 'desc');

            // Configurar fechas según el período para filtrar facturas
            $startDate = $this->getStartDate($period);
            $endDate = Carbon::now();

            // Obtener TODOS los clientes con sus datos reales calculados de invoices e invoice_items
            $customersQuery = DB::table('customers')
                ->leftJoin('invoices', function($join) {
                    $join->on('customers.id', '=', 'invoices.customer_id')
                         ->where('invoices.status', '=', 'paid');
                })
                ->leftJoin('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
                ->leftJoin('applied_discounts', 'invoices.id', '=', 'applied_discounts.invoice_id')
                ->groupBy('customers.id', 'customers.name', 'customers.email', 'customers.phone',
                         'customers.city', 'customers.document_type', 'customers.document_number',
                         'customers.credit_limit', 'customers.current_debt', 'customers.has_discount',
                         'customers.discount_type', 'customers.discount_value')
                ->selectRaw('
                    customers.id,
                    customers.name,
                    customers.email,
                    customers.phone,
                    customers.city,
                    customers.document_type,
                    customers.document_number,
                    customers.credit_limit,
                    customers.current_debt,
                    customers.has_discount,
                    customers.discount_type,
                    customers.discount_value,
                    COALESCE(COUNT(DISTINCT invoices.id), 0) as total_orders,
                    COALESCE(SUM(invoices.total), 0) as total_spent,
                    COALESCE(COUNT(DISTINCT invoice_items.product_id), 0) as unique_products,
                    COALESCE(SUM(invoice_items.quantity), 0) as total_items,
                    COALESCE(SUM(applied_discounts.discount_amount), 0) as total_discounts_received,
                    MAX(invoices.created_at) as last_purchase_date,
                    MIN(invoices.created_at) as first_purchase_date
                ');

            // Aplicar búsqueda si se proporciona
            if ($search) {
                $customersQuery->where('customers.name', 'like', "%{$search}%");
            }

            // Aplicar ordenamiento
            $customersQuery->orderBy($orderBy, $orderDirection);

            // Obtener todos los clientes con datos calculados
            $customers = $customersQuery->get()->map(function ($customer) use ($startDate, $endDate) {
                // Determinar si el cliente estuvo activo en el período
                $wasActiveInPeriod = $customer->last_purchase_date &&
                    Carbon::parse($customer->last_purchase_date)->between($startDate, $endDate);

                // Determinar estado general del cliente
                $isActive = $customer->total_orders > 0;

                // Calcular frecuencia de compra (días promedio entre compras)
                $purchaseFrequency = 0;
                if ($customer->total_orders > 1 && $customer->first_purchase_date && $customer->last_purchase_date) {
                    $daysBetween = Carbon::parse($customer->first_purchase_date)
                        ->diffInDays(Carbon::parse($customer->last_purchase_date));
                    $purchaseFrequency = $daysBetween > 0 ? round($daysBetween / ($customer->total_orders - 1), 1) : 0;
                }

                return [
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'city' => $customer->city,
                    'document_type' => $customer->document_type,
                    'document_number' => $customer->document_number,
                    'total_purchases' => (int)$customer->total_orders,
                    'total_spent' => (float)$customer->total_spent,
                    // Ahora tenemos datos reales de las nuevas tablas
                    'unique_products_bought' => (int)$customer->unique_products,
                    'total_items_bought' => (int)$customer->total_items,
                    'total_discounts_received' => (float)$customer->total_discounts_received,
                    'purchase_frequency_days' => $purchaseFrequency,
                    'credit_limit' => (float)$customer->credit_limit,
                    'current_debt' => (float)$customer->current_debt,
                    'available_credit' => (float)($customer->credit_limit - $customer->current_debt),
                    'last_purchase_date' => $customer->last_purchase_date,
                    'first_purchase_date' => $customer->first_purchase_date,
                    'active_in_period' => $wasActiveInPeriod,
                    'status' => $isActive ? 'active' : 'inactive',
                    'debt_status' => $customer->current_debt > 0 ? 'with_debt' : 'no_debt',
                    'credit_usage_percent' => $customer->credit_limit > 0
                        ? round(($customer->current_debt / $customer->credit_limit) * 100, 1)
                        : 0,
                    'avg_purchase_value' => $customer->total_orders > 0
                        ? round($customer->total_spent / $customer->total_orders, 2)
                        : 0,
                    'avg_items_per_purchase' => $customer->total_orders > 0
                        ? round($customer->total_items / $customer->total_orders, 1)
                        : 0,
                    'has_discount' => (bool)$customer->has_discount,
                    'discount_type' => $customer->discount_type,
                    'discount_value' => (float)($customer->discount_value ?? 0)
                ];
            });

            // Calcular resumen general
            $totalCustomers = $customers->count();
            $activeCustomers = $customers->where('status', 'active')->count();
            $inactiveCustomers = $totalCustomers - $activeCustomers;
            $customersWithDebt = $customers->where('current_debt', '>', 0)->count();

            // Clientes activos en el período seleccionado
            $activeInPeriod = $customers->where('active_in_period', true)->count();

            // Totales de clientes activos
            $totalRevenue = $customers->where('status', 'active')->sum('total_spent');
            $totalDebt = $customers->sum('current_debt');
            $totalCreditLimit = $customers->sum('credit_limit');

            // Cliente top
            $topCustomer = $customers->where('status', 'active')
                ->sortByDesc('total_spent')
                ->first();

            // Cliente con más deuda
            $topDebtor = $customers->where('current_debt', '>', 0)
                ->sortByDesc('current_debt')
                ->first();

            $summary = [
                'total_customers' => $totalCustomers,
                'active_customers' => $activeCustomers,
                'inactive_customers' => $inactiveCustomers,
                'customers_with_debt' => $customersWithDebt,
                'active_in_period' => $activeInPeriod,
                'total_revenue' => $totalRevenue,
                'total_debt' => $totalDebt,
                'total_credit_limit' => $totalCreditLimit,
                'avg_customer_value' => $activeCustomers > 0 ? round($totalRevenue / $activeCustomers, 2) : 0,
                'credit_utilization' => $totalCreditLimit > 0 ? round(($totalDebt / $totalCreditLimit) * 100, 1) : 0,
                'top_customer' => $topCustomer ? [
                    'name' => $topCustomer['customer_name'],
                    'total_spent' => $topCustomer['total_spent'],
                    'total_orders' => $topCustomer['total_purchases']
                ] : null,
                'top_debtor' => $topDebtor ? [
                    'name' => $topDebtor['customer_name'],
                    'debt' => $topDebtor['current_debt']
                ] : null
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'customers' => $customers->values(),
                    'summary' => $summary,
                    'period' => [
                        'start' => $startDate->format('Y-m-d'),
                        'end' => $endDate->format('Y-m-d'),
                        'period_type' => $period
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error obteniendo análisis de clientes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener alertas inteligentes del inventario
     */
    public function alerts(Request $request): JsonResponse
    {
        try {
            $period = $request->input('period', 'month');
            $severity = $request->input('severity'); // 'critical', 'warning', 'info'
            $userId = $request->input('user_id', 1); // Default user para pruebas

            // Obtener alertas descartadas por el usuario
            $dismissedAlerts = DB::table('dismissed_alerts')
                ->where('user_id', $userId)
                ->pluck('alert_key')
                ->toArray();

            $alerts = [];

            // 1. ALERTAS DE STOCK BAJO/AGOTADO
            $stockAlerts = $this->getStockAlerts($dismissedAlerts);
            $alerts = array_merge($alerts, $stockAlerts);

            // 2. ALERTAS DE PRODUCTOS SIN ROTACIÓN
            $rotationAlerts = $this->getRotationAlerts($period, $dismissedAlerts);
            $alerts = array_merge($alerts, $rotationAlerts);

            // 3. ALERTAS DE MARGEN NEGATIVO
            $marginAlerts = $this->getMarginAlerts($dismissedAlerts);
            $alerts = array_merge($alerts, $marginAlerts);

            // 4. ALERTAS DE DESCUENTOS EXCESIVOS
            $discountAlerts = $this->getDiscountAlerts($period, $dismissedAlerts);
            $alerts = array_merge($alerts, $discountAlerts);

            // 5. ALERTAS DE TENDENCIAS NEGATIVAS
            $trendAlerts = $this->getTrendAlerts($period, $dismissedAlerts);
            $alerts = array_merge($alerts, $trendAlerts);

            // Filtrar por severidad si se especifica
            if ($severity) {
                $alerts = array_filter($alerts, function($alert) use ($severity) {
                    return $alert['severity'] === $severity;
                });
            }

            // Ordenar por severidad (critical > warning > info) y fecha
            usort($alerts, function($a, $b) {
                $severityOrder = ['critical' => 3, 'warning' => 2, 'info' => 1];
                $severityCompare = $severityOrder[$b['severity']] - $severityOrder[$a['severity']];

                if ($severityCompare === 0) {
                    return strtotime($b['created_at']) - strtotime($a['created_at']);
                }

                return $severityCompare;
            });

            // Estadísticas de alertas
            $summary = [
                'total_alerts' => count($alerts),
                'critical' => count(array_filter($alerts, fn($a) => $a['severity'] === 'critical')),
                'warning' => count(array_filter($alerts, fn($a) => $a['severity'] === 'warning')),
                'info' => count(array_filter($alerts, fn($a) => $a['severity'] === 'info')),
                'categories' => [
                    'stock' => count(array_filter($alerts, fn($a) => $a['category'] === 'stock')),
                    'rotation' => count(array_filter($alerts, fn($a) => $a['category'] === 'rotation')),
                    'margin' => count(array_filter($alerts, fn($a) => $a['category'] === 'margin')),
                    'discount' => count(array_filter($alerts, fn($a) => $a['category'] === 'discount')),
                    'trend' => count(array_filter($alerts, fn($a) => $a['category'] === 'trend'))
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'alerts' => array_values($alerts),
                    'summary' => $summary,
                    'period' => $period,
                    'generated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generando alertas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Descartar alerta para que no se vuelva a mostrar
     */
    public function dismissAlert(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'alert_key' => 'required|string',
                'alert_type' => 'required|string',
                'product_id' => 'nullable|integer',
                'user_id' => 'integer'
            ]);

            $userId = $request->input('user_id', 1); // Default user para pruebas

            DB::table('dismissed_alerts')->updateOrInsert(
                [
                    'user_id' => $userId,
                    'alert_key' => $request->alert_key
                ],
                [
                    'alert_type' => $request->alert_type,
                    'product_id' => $request->product_id,
                    'dismissed_at' => now(),
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Alerta descartada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error descartando alerta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar alertas de stock bajo y agotado
     */
    private function getStockAlerts(array $dismissedAlerts = []): array
    {
        $alerts = [];

        // Productos sin stock
        $outOfStock = Product::where('active', true)
            ->where('current_stock', '<=', 0)
            ->get();

        foreach ($outOfStock as $product) {
            $alertKey = "stock_out_{$product->id}";

            // Verificar si la alerta fue descartada
            if (in_array($alertKey, $dismissedAlerts)) {
                continue;
            }

            $alerts[] = [
                'id' => $alertKey,
                'alert_key' => $alertKey,
                'title' => 'Producto agotado',
                'message' => "El producto '{$product->name}' está sin stock",
                'severity' => 'critical',
                'category' => 'stock',
                'product_id' => $product->id,
                'product_name' => $product->name,
                'current_stock' => $product->current_stock,
                'min_stock' => $product->min_stock,
                'created_at' => now()->toISOString(),
                'action_url' => "/products/{$product->id}/restock",
                'action_text' => 'Reabastecer'
            ];
        }

        // Productos con stock bajo (incluyendo productos con stock 0 pero min_stock > 0)
        $lowStock = Product::where('active', true)
            ->whereRaw('current_stock <= min_stock')
            ->where('min_stock', '>', 0)  // Solo alertar si hay un mínimo configurado
            ->get();

        foreach ($lowStock as $product) {
            $alerts[] = [
                'id' => 'stock_low_' . $product->id,
                'title' => 'Stock bajo',
                'message' => "El producto '{$product->name}' tiene stock bajo ({$product->current_stock} unidades)",
                'severity' => 'warning',
                'category' => 'stock',
                'product_id' => $product->id,
                'product_name' => $product->name,
                'current_stock' => $product->current_stock,
                'min_stock' => $product->min_stock,
                'created_at' => now()->toISOString(),
                'action_url' => "/products/{$product->id}/restock",
                'action_text' => 'Reabastecer'
            ];
        }

        return $alerts;
    }

    /**
     * Generar alertas de productos sin rotación
     */
    private function getRotationAlerts(string $period, array $dismissedAlerts = []): array
    {
        $alerts = [];
        $daysThreshold = $period === 'week' ? 14 : 30; // Sin ventas en 14-30 días
        $startDate = Carbon::now()->subDays($daysThreshold);

        // Productos con stock pero sin ventas recientes
        $productsWithoutSales = Product::where('active', true)
            ->where('current_stock', '>', 0)
            ->whereNotExists(function($query) use ($startDate) {
                $query->select(DB::raw(1))
                    ->from('invoice_items')
                    ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                    ->whereRaw('invoice_items.product_id = products.id')
                    ->where('invoices.created_at', '>=', $startDate)
                    ->where('invoices.status', 'paid');
            })
            ->get();

        foreach ($productsWithoutSales as $product) {
            $alertKey = "rotation_{$product->id}";

            // Verificar si la alerta fue descartada
            if (in_array($alertKey, $dismissedAlerts)) {
                continue;
            }

            $stockValue = $product->current_stock * $product->cost_price;

            $alerts[] = [
                'id' => 'rotation_' . $product->id,
                'alert_key' => $alertKey,
                'title' => 'Producto sin rotación',
                'message' => "'{$product->name}' no ha tenido ventas en {$daysThreshold} días (\$" . number_format($stockValue, 0) . " inmovilizado)",
                'severity' => $stockValue > 100000 ? 'warning' : 'info',
                'category' => 'rotation',
                'product_id' => $product->id,
                'product_name' => $product->name,
                'days_without_sales' => $daysThreshold,
                'stock_value' => $stockValue,
                'current_stock' => $product->current_stock,
                'created_at' => now()->toISOString(),
                'action_url' => "/products/{$product->id}/promotion",
                'action_text' => 'Crear promoción'
            ];
        }

        return $alerts;
    }

    /**
     * Generar alertas de margen negativo
     */
    private function getMarginAlerts(array $dismissedAlerts = []): array
    {
        $alerts = [];

        // Productos con margen negativo (precio venta < precio costo)
        $negativeMarginProducts = Product::where('active', true)
            ->whereRaw('sale_price < cost_price')
            ->get();

        foreach ($negativeMarginProducts as $product) {
            $alertId = 'margin_negative_' . $product->id;

            // Verificar si esta alerta fue descartada
            if (in_array($alertId, $dismissedAlerts)) {
                continue;
            }

            $loss = $product->cost_price - $product->sale_price;
            $lossPercentage = round(($loss / $product->cost_price) * 100, 1);

            $alerts[] = [
                'id' => $alertId,
                'title' => 'Margen negativo',
                'message' => "'{$product->name}' tiene margen negativo (-{$lossPercentage}%)",
                'severity' => 'critical',
                'category' => 'margin',
                'product_id' => $product->id,
                'product_name' => $product->name,
                'cost_price' => $product->cost_price,
                'sale_price' => $product->sale_price,
                'loss_per_unit' => $loss,
                'loss_percentage' => $lossPercentage,
                'created_at' => now()->toISOString(),
                'action_url' => "/products/{$product->id}/adjust-price",
                'action_text' => 'Ajustar precio'
            ];
        }

        return $alerts;
    }

    /**
     * Generar alertas de descuentos excesivos
     */
    private function getDiscountAlerts(string $period): array
    {
        $alerts = [];
        $startDate = $this->getStartDate($period);

        // Descuentos que superen el 20% en el período
        $excessiveDiscounts = DB::table('applied_discounts')
            ->join('invoices', 'applied_discounts.invoice_id', '=', 'invoices.id')
            ->join('customers', 'applied_discounts.customer_id', '=', 'customers.id')
            ->where('applied_discounts.applied_at', '>=', $startDate)
            ->whereRaw('(applied_discounts.discount_amount / invoices.subtotal) > 0.20')
            ->select(
                'applied_discounts.*',
                'invoices.number as invoice_number',
                'invoices.subtotal',
                'customers.name as customer_name'
            )
            ->get();

        foreach ($excessiveDiscounts as $discount) {
            $discountPercentage = round(($discount->discount_amount / $discount->subtotal) * 100, 1);

            $alerts[] = [
                'id' => 'discount_excessive_' . $discount->id,
                'title' => 'Descuento excesivo',
                'message' => "Descuento de {$discountPercentage}% aplicado en factura {$discount->invoice_number}",
                'severity' => $discountPercentage > 30 ? 'critical' : 'warning',
                'category' => 'discount',
                'invoice_number' => $discount->invoice_number,
                'customer_name' => $discount->customer_name,
                'discount_amount' => $discount->discount_amount,
                'discount_percentage' => $discountPercentage,
                'discount_reason' => $discount->discount_reason,
                'created_at' => $discount->applied_at,
                'action_url' => "/invoices/{$discount->invoice_id}",
                'action_text' => 'Ver factura'
            ];
        }

        return $alerts;
    }

    /**
     * Generar alertas de tendencias negativas
     */
    private function getTrendAlerts(string $period): array
    {
        $alerts = [];

        // Comparar ventas del período actual vs período anterior
        $currentStart = $this->getStartDate($period);
        $currentEnd = Carbon::now();

        $periodDays = $currentStart->diffInDays($currentEnd);
        $previousStart = $currentStart->copy()->subDays($periodDays);
        $previousEnd = $currentStart->copy()->subDay();

        // Ventas período actual
        $currentSales = DB::table('invoices')
            ->where('status', 'paid')
            ->whereBetween('created_at', [$currentStart, $currentEnd])
            ->sum('total');

        // Ventas período anterior
        $previousSales = DB::table('invoices')
            ->where('status', 'paid')
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->sum('total');

        if ($previousSales > 0) {
            $changePercentage = round((($currentSales - $previousSales) / $previousSales) * 100, 1);

            if ($changePercentage < -10) { // Caída superior al 10%
                $alerts[] = [
                    'id' => 'trend_sales_decline',
                    'title' => 'Caída en ventas',
                    'message' => "Las ventas han caído {$changePercentage}% vs período anterior",
                    'severity' => $changePercentage < -25 ? 'critical' : 'warning',
                    'category' => 'trend',
                    'current_sales' => $currentSales,
                    'previous_sales' => $previousSales,
                    'change_percentage' => $changePercentage,
                    'period' => $period,
                    'created_at' => now()->toISOString(),
                    'action_url' => '/reports/sales-analysis',
                    'action_text' => 'Analizar ventas'
                ];
            }
        }

        return $alerts;
    }

    /**
     * Obtener predicciones inteligentes del inventario
     */
    public function predictions(Request $request): JsonResponse
    {
        try {
            $period = $request->input('period', 'month');
            $forecastDays = $request->input('forecast_days', 30);

            $predictions = [];

            // 1. PREDICCIÓN DE AGOTAMIENTO DE STOCK
            $stockPredictions = $this->getStockDepletionPredictions($forecastDays);

            // 2. PREDICCIÓN DE PRODUCTOS MÁS VENDIDOS
            $salesPredictions = $this->getSalesPredictions($period, $forecastDays);

            // 3. RECOMENDACIONES DE COMPRA
            $purchaseRecommendations = $this->getPurchaseRecommendations($forecastDays);

            // 4. ANÁLISIS DE TENDENCIAS
            $trendAnalysis = $this->getTrendAnalysis($period);

            return response()->json([
                'success' => true,
                'data' => [
                    'stock_depletion' => $stockPredictions,
                    'sales_forecast' => $salesPredictions,
                    'purchase_recommendations' => $purchaseRecommendations,
                    'trend_analysis' => $trendAnalysis,
                    'forecast_period' => $forecastDays,
                    'based_on_period' => $period,
                    'generated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generando predicciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Predecir cuándo se agotará el stock de cada producto
     */
    private function getStockDepletionPredictions(int $forecastDays): array
    {
        $predictions = [];
        $analysisStart = Carbon::now()->subDays(30); // Analizar últimos 30 días

        $products = Product::where('active', true)
            ->where('current_stock', '>', 0)
            ->get();

        foreach ($products as $product) {
            // Calcular promedio de ventas diarias
            $salesData = DB::table('invoice_items')
                ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->where('invoice_items.product_id', $product->id)
                ->where('invoices.status', 'paid')
                ->where('invoices.created_at', '>=', $analysisStart)
                ->sum('invoice_items.quantity');

            $dailyAverage = $salesData / 30; // Promedio diario

            if ($dailyAverage > 0) {
                $daysUntilDepletion = round($product->current_stock / $dailyAverage);
                $depletionDate = Carbon::now()->addDays($daysUntilDepletion);

                $urgency = 'low';
                if ($daysUntilDepletion <= 7) {
                    $urgency = 'critical';
                } elseif ($daysUntilDepletion <= 14) {
                    $urgency = 'high';
                } elseif ($daysUntilDepletion <= 30) {
                    $urgency = 'medium';
                }

                $predictions[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'current_stock' => $product->current_stock,
                    'daily_average_sales' => round($dailyAverage, 2),
                    'days_until_depletion' => $daysUntilDepletion,
                    'estimated_depletion_date' => $depletionDate->format('Y-m-d'),
                    'urgency' => $urgency,
                    'confidence' => $salesData > 0 ? 'high' : 'low'
                ];
            }
        }

        // Ordenar por urgencia
        usort($predictions, function($a, $b) {
            $urgencyOrder = ['critical' => 4, 'high' => 3, 'medium' => 2, 'low' => 1];
            return $urgencyOrder[$b['urgency']] - $urgencyOrder[$a['urgency']];
        });

        return array_slice($predictions, 0, 20); // Top 20 productos más urgentes
    }

    /**
     * Predecir productos que se venderán más
     */
    private function getSalesPredictions(string $period, int $forecastDays): array
    {
        $startDate = $this->getStartDate($period);
        $periodDays = Carbon::now()->diffInDays($startDate);

        // Obtener ventas históricas por producto
        $salesData = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->where('invoices.status', 'paid')
            ->where('invoices.created_at', '>=', $startDate)
            ->groupBy('invoice_items.product_id', 'products.name')
            ->select(
                'invoice_items.product_id',
                'products.name as product_name',
                DB::raw('SUM(invoice_items.quantity) as total_sold'),
                DB::raw('COUNT(DISTINCT invoices.id) as transactions'),
                DB::raw('AVG(invoice_items.quantity) as avg_per_transaction')
            )
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        $predictions = [];

        foreach ($salesData as $data) {
            $dailyAverage = $data->total_sold / $periodDays;
            $forecastSales = round($dailyAverage * $forecastDays);

            // Calcular tendencia (crecimiento/decrecimiento)
            $trend = $this->calculateProductTrend($data->product_id, $periodDays);

            $predictions[] = [
                'product_id' => $data->product_id,
                'product_name' => $data->product_name,
                'historical_sales' => $data->total_sold,
                'daily_average' => round($dailyAverage, 2),
                'forecast_sales' => $forecastSales,
                'transactions' => $data->transactions,
                'avg_per_transaction' => round($data->avg_per_transaction, 2),
                'trend' => $trend,
                'confidence' => $data->transactions >= 5 ? 'high' : 'medium'
            ];
        }

        return $predictions;
    }

    /**
     * Generar recomendaciones de compra
     */
    private function getPurchaseRecommendations(int $forecastDays): array
    {
        $recommendations = [];
        $safetyStock = 1.2; // 20% de stock de seguridad

        $products = Product::where('active', true)->get();

        foreach ($products as $product) {
            // Calcular demanda proyectada
            $historicalSales = DB::table('invoice_items')
                ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->where('invoice_items.product_id', $product->id)
                ->where('invoices.status', 'paid')
                ->where('invoices.created_at', '>=', Carbon::now()->subDays(30))
                ->sum('invoice_items.quantity');

            $dailyDemand = $historicalSales / 30;
            $projectedDemand = round($dailyDemand * $forecastDays * $safetyStock);

            $needToBuy = max(0, $projectedDemand - $product->current_stock);

            if ($needToBuy > 0) {
                $priority = 'low';
                if ($product->current_stock <= $product->min_stock) {
                    $priority = 'critical';
                } elseif ($product->current_stock <= ($product->min_stock * 2)) {
                    $priority = 'high';
                } elseif ($needToBuy >= $projectedDemand * 0.5) {
                    $priority = 'medium';
                }

                $recommendations[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'current_stock' => $product->current_stock,
                    'min_stock' => $product->min_stock,
                    'projected_demand' => $projectedDemand,
                    'recommended_purchase' => $needToBuy,
                    'estimated_cost' => $needToBuy * $product->cost_price,
                    'priority' => $priority,
                    'daily_demand' => round($dailyDemand, 2)
                ];
            }
        }

        // Ordenar por prioridad
        usort($recommendations, function($a, $b) {
            $priorityOrder = ['critical' => 4, 'high' => 3, 'medium' => 2, 'low' => 1];
            return $priorityOrder[$b['priority']] - $priorityOrder[$a['priority']];
        });

        return $recommendations;
    }

    /**
     * Análisis de tendencias
     */
    private function getTrendAnalysis(string $period): array
    {
        $currentStart = $this->getStartDate($period);
        $currentEnd = Carbon::now();
        $periodDays = $currentStart->diffInDays($currentEnd);

        $previousStart = $currentStart->copy()->subDays($periodDays);
        $previousEnd = $currentStart->copy()->subDay();

        // Análisis de ventas
        $currentSales = DB::table('invoices')
            ->where('status', 'paid')
            ->whereBetween('created_at', [$currentStart, $currentEnd])
            ->sum('total');

        $previousSales = DB::table('invoices')
            ->where('status', 'paid')
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->sum('total');

        $salesGrowth = $previousSales > 0 ?
            round((($currentSales - $previousSales) / $previousSales) * 100, 1) : 0;

        // Análisis de transacciones
        $currentTransactions = DB::table('invoices')
            ->where('status', 'paid')
            ->whereBetween('created_at', [$currentStart, $currentEnd])
            ->count();

        $previousTransactions = DB::table('invoices')
            ->where('status', 'paid')
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->count();

        $transactionGrowth = $previousTransactions > 0 ?
            round((($currentTransactions - $previousTransactions) / $previousTransactions) * 100, 1) : 0;

        // Ticket promedio
        $currentAvgTicket = $currentTransactions > 0 ? $currentSales / $currentTransactions : 0;
        $previousAvgTicket = $previousTransactions > 0 ? $previousSales / $previousTransactions : 0;

        $avgTicketGrowth = $previousAvgTicket > 0 ?
            round((($currentAvgTicket - $previousAvgTicket) / $previousAvgTicket) * 100, 1) : 0;

        return [
            'sales' => [
                'current' => $currentSales,
                'previous' => $previousSales,
                'growth_percentage' => $salesGrowth,
                'trend' => $salesGrowth >= 0 ? 'positive' : 'negative'
            ],
            'transactions' => [
                'current' => $currentTransactions,
                'previous' => $previousTransactions,
                'growth_percentage' => $transactionGrowth,
                'trend' => $transactionGrowth >= 0 ? 'positive' : 'negative'
            ],
            'average_ticket' => [
                'current' => round($currentAvgTicket, 2),
                'previous' => round($previousAvgTicket, 2),
                'growth_percentage' => $avgTicketGrowth,
                'trend' => $avgTicketGrowth >= 0 ? 'positive' : 'negative'
            ]
        ];
    }

    /**
     * Calcular tendencia de un producto específico
     */
    private function calculateProductTrend(int $productId, int $periodDays): string
    {
        $halfPeriod = intval($periodDays / 2);

        $firstHalfStart = Carbon::now()->subDays($periodDays);
        $firstHalfEnd = Carbon::now()->subDays($halfPeriod);

        $secondHalfStart = Carbon::now()->subDays($halfPeriod);
        $secondHalfEnd = Carbon::now();

        $firstHalfSales = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoice_items.product_id', $productId)
            ->where('invoices.status', 'paid')
            ->whereBetween('invoices.created_at', [$firstHalfStart, $firstHalfEnd])
            ->sum('invoice_items.quantity');

        $secondHalfSales = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoice_items.product_id', $productId)
            ->where('invoices.status', 'paid')
            ->whereBetween('invoices.created_at', [$secondHalfStart, $secondHalfEnd])
            ->sum('invoice_items.quantity');

        if ($firstHalfSales == 0 && $secondHalfSales == 0) {
            return 'stable';
        }

        if ($firstHalfSales == 0) {
            return 'growing';
        }

        $growthRate = (($secondHalfSales - $firstHalfSales) / $firstHalfSales) * 100;

        if ($growthRate > 20) {
            return 'growing';
        } elseif ($growthRate < -20) {
            return 'declining';
        } else {
            return 'stable';
        }
    }
}
