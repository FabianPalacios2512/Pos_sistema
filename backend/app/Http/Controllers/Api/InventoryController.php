<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\InventoryMovement;
use App\Models\Supplier;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryController extends Controller
{
    /**
     * 1. VISTA GENERAL DEL INVENTARIO
     * Dashboard principal con métricas clave
     */
    public function overview()
    {
        try {
            // Métricas generales
            $totalProducts = Product::count();
            $activeProducts = Product::active()->count();
            $lowStockProducts = Product::lowStock()->count();
            $outOfStockProducts = Product::where('current_stock', '<=', 0)->where('manage_stock', true)->count();

            // Valor total del inventario
            $totalInventoryValue = Product::active()
                ->selectRaw('SUM(current_stock * cost_price) as total')
                ->value('total') ?? 0;

            // Movimientos recientes (últimos 7 días)
            $recentMovements = InventoryMovement::with(['product', 'user'])
                ->where('movement_date', '>=', Carbon::now()->subDays(7))
                ->orderBy('movement_date', 'desc')
                ->limit(10)
                ->get();

            // Top productos más vendidos (último mes)
            $topSellingProducts = Product::select([
                    'products.id',
                    'products.name',
                    'products.sku',
                    'products.sale_price',
                    'products.cost_price',
                    'products.current_stock',
                    'products.image_url'
                ])
                ->selectRaw('SUM(invoice_items.quantity) as total_quantity_sold')
                ->selectRaw('SUM(invoice_items.quantity * invoice_items.unit_price) as total_revenue')
                ->join('invoice_items', 'products.id', '=', 'invoice_items.product_id')
                ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->where('invoices.date', '>=', Carbon::now()->subMonth())
                ->where('invoices.status', 'paid')
                ->groupBy([
                    'products.id',
                    'products.name',
                    'products.sku',
                    'products.sale_price',
                    'products.cost_price',
                    'products.current_stock',
                    'products.image_url'
                ])
                ->orderBy('total_quantity_sold', 'desc')
                ->limit(10)
                ->get();

            // Productos con stock bajo
            $lowStockProductsList = Product::with(['category'])
                ->lowStock()
                ->orderBy('current_stock', 'asc')
                ->limit(10)
                ->get();

            // Gráfico de movimientos por día (últimos 30 días)
            $movementsTrend = InventoryMovement::selectRaw('DATE(movement_date) as date, type, SUM(ABS(quantity)) as total')
                ->where('movement_date', '>=', Carbon::now()->subDays(30))
                ->groupBy('date', 'type')
                ->orderBy('date')
                ->get()
                ->groupBy('date');

            return response()->json([
                'success' => true,
                'data' => [
                    'metrics' => [
                        'totalProducts' => $totalProducts,
                        'activeProducts' => $activeProducts,
                        'lowStockProducts' => $lowStockProducts,
                        'outOfStockProducts' => $outOfStockProducts,
                        'totalInventoryValue' => $totalInventoryValue
                    ],
                    'recentMovements' => $recentMovements,
                    'topSellingProducts' => $topSellingProducts,
                    'lowStockProductsList' => $lowStockProductsList,
                    'movementsTrend' => $movementsTrend
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la vista general del inventario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 2. VISTA DE PRODUCTOS
     * Análisis detallado de productos individuales
     */
    public function products(Request $request)
    {
        try {
            $query = Product::with(['category', 'supplier']);

            // 🏢 Filtro por warehouse (multi-sede)
            if ($request->filled('warehouse_id')) {
                $warehouseId = $request->warehouse_id;
                $query->whereHas('warehouses', function($q) use ($warehouseId) {
                    $q->where('warehouse_id', $warehouseId);
                });

                // Cargar stock específico del warehouse seleccionado
                $query->with(['warehouses' => function($q) use ($warehouseId) {
                    $q->where('warehouse_id', $warehouseId);
                }]);
            }

            // Filtros
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%");
                });
            }

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->filled('supplier_id')) {
                $query->where('supplier_id', $request->supplier_id);
            }

            if ($request->filled('stock_status')) {
                switch ($request->stock_status) {
                    case 'low':
                        $query->lowStock();
                        break;
                    case 'out':
                        $query->where('current_stock', '<=', 0)->where('manage_stock', true);
                        break;
                    case 'normal':
                        $query->whereRaw('current_stock > min_stock')->where('manage_stock', true);
                        break;
                }
            }

            // Agregar métricas de rendimiento por producto
            // 🏢 Si hay filtro de warehouse, filtrar ventas y movimientos por esa sede
            if ($request->filled('warehouse_id')) {
                $warehouseId = $request->warehouse_id;
                $query->selectRaw('products.*,
                    (SELECT SUM(quantity) FROM invoice_items
                     JOIN invoices ON invoice_items.invoice_id = invoices.id
                     LEFT JOIN cash_sessions ON invoices.cash_session_id = cash_sessions.id
                     WHERE invoice_items.product_id = products.id
                     AND invoices.status = "paid"
                     AND (cash_sessions.warehouse_id = ? OR invoices.cash_session_id IS NULL)) as total_sold,
                    (SELECT SUM(quantity * unit_price) FROM invoice_items
                     JOIN invoices ON invoice_items.invoice_id = invoices.id
                     LEFT JOIN cash_sessions ON invoices.cash_session_id = cash_sessions.id
                     WHERE invoice_items.product_id = products.id
                     AND invoices.status = "paid"
                     AND (cash_sessions.warehouse_id = ? OR invoices.cash_session_id IS NULL)) as total_revenue,
                    (SELECT COUNT(*) FROM inventory_movements
                     WHERE product_id = products.id
                     AND warehouse_id = ?
                     AND movement_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as movement_count',
                    [$warehouseId, $warehouseId, $warehouseId]);
            } else {
                // Sin filtro de warehouse: métricas globales
                $query->selectRaw('products.*,
                    COALESCE((SELECT SUM(quantity) FROM invoice_items
                     JOIN invoices ON invoice_items.invoice_id = invoices.id
                     WHERE invoice_items.product_id = products.id AND invoices.status = "paid"), 0) as total_sold,
                    COALESCE((SELECT SUM(quantity * unit_price) FROM invoice_items
                     JOIN invoices ON invoice_items.invoice_id = invoices.id
                     WHERE invoice_items.product_id = products.id AND invoices.status = "paid"), 0) as total_revenue,
                    (SELECT COUNT(*) FROM inventory_movements WHERE product_id = products.id AND movement_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as movement_count');
            }

            $products = $query->paginate($request->get('per_page', 20));

            // 🏢 Si hay filtro de warehouse, ajustar el stock mostrado al del warehouse específico
            if ($request->filled('warehouse_id')) {
                $warehouseId = $request->warehouse_id;

                $products->getCollection()->transform(function($product) use ($warehouseId) {
                    // 🛠️ FIX: Para productos variables, sumar stock de TODAS sus variantes en el warehouse
                    if ($product->product_type === 'variable') {
                        $totalVariantStock = DB::table('product_warehouse')
                            ->where('warehouse_id', $warehouseId)
                            ->where('product_id', $product->id)
                            ->sum('stock');

                        $product->current_stock = (int)$totalVariantStock;
                        $product->stock = (int)$totalVariantStock;
                    } else {
                        // Para productos simples, usar el stock directo del warehouse
                        if ($product->warehouses && $product->warehouses->isNotEmpty()) {
                            $warehouseStock = $product->warehouses->first()->pivot->stock;
                            $product->current_stock = (int)$warehouseStock;
                            $product->stock = (int)$warehouseStock;
                        }
                    }

                    // 💰 CALCULAR ventas e ingresos filtradas por warehouse
                    $salesData = DB::table('invoice_items')
                        ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                        ->leftJoin('cash_sessions', 'invoices.cash_session_id', '=', 'cash_sessions.id')
                        ->where('invoice_items.product_id', $product->id)
                        ->where('invoices.status', 'paid')
                        ->where(function($q) use ($warehouseId) {
                            $q->where('cash_sessions.warehouse_id', $warehouseId)
                              ->orWhereNull('invoices.cash_session_id');
                        })
                        ->selectRaw('
                            COALESCE(SUM(invoice_items.quantity), 0) as total_sold,
                            COALESCE(SUM(invoice_items.quantity * invoice_items.unit_price), 0) as total_revenue
                        ')
                        ->first();

                    $product->total_sold = (int)($salesData->total_sold ?? 0);
                    $product->total_revenue = (float)($salesData->total_revenue ?? 0);

                    return $product;
                });
            } else {
                // ✅ IMPORTANTE: Transformar todos los productos para asegurar total_sold y total_revenue
                $products->getCollection()->transform(function($product) {
                    // 💰 CALCULAR ventas e ingresos directamente desde la BD
                    $salesData = DB::table('invoice_items')
                        ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                        ->where('invoice_items.product_id', $product->id)
                        ->where('invoices.status', 'paid')
                        ->selectRaw('
                            COALESCE(SUM(invoice_items.quantity), 0) as total_sold,
                            COALESCE(SUM(invoice_items.quantity * invoice_items.unit_price), 0) as total_revenue
                        ')
                        ->first();

                    $product->total_sold = (int)($salesData->total_sold ?? 0);
                    $product->total_revenue = (float)($salesData->total_revenue ?? 0);

                    \Log::info('💰 VENTAS CALCULADAS', [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'total_sold' => $product->total_sold,
                        'total_revenue' => $product->total_revenue
                    ]);

                    return $product;
                });
            }

            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los productos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 3. MOVIMIENTOS DE INVENTARIO
     * Historial y análisis de movimientos
     */
    public function movements(Request $request)
    {
        try {
            $query = InventoryMovement::with(['product', 'user']);

            // Filtros
            if ($request->filled('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('movement_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('movement_date', '<=', $request->date_to);
            }

            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            $movements = $query->orderBy('movement_date', 'desc')
                              ->paginate($request->get('per_page', 20));

            // Estadísticas del período filtrado
            $stats = [
                'totalEntries' => $query->clone()->entries()->sum('quantity'),
                'totalExits' => abs($query->clone()->exits()->sum('quantity')),
                'totalValue' => $query->clone()->get()->sum('total_value'),
                'typeBreakdown' => $query->clone()
                    ->reorder()
                    ->selectRaw('type, COUNT(*) as count, SUM(ABS(quantity)) as total_quantity')
                    ->groupBy('type')
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'data' => $movements,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los movimientos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 4. VISTA DE CLIENTES
     * Análisis de comportamiento de compra por cliente
     */
    public function customers(Request $request)
    {
        try {
            $query = Customer::with(['invoices.invoiceItems.product']);

            // Agregar métricas de compra por cliente
            $query->selectRaw('customers.*,
                (SELECT COUNT(*) FROM invoices WHERE customer_id = customers.id AND status = "paid") as total_orders,
                (SELECT SUM(total) FROM invoices WHERE customer_id = customers.id AND status = "paid") as total_spent,
                (SELECT MAX(date) FROM invoices WHERE customer_id = customers.id AND status = "paid") as last_purchase_date,
                (SELECT SUM(quantity) FROM invoice_items
                 JOIN invoices ON invoice_items.invoice_id = invoices.id
                 WHERE invoices.customer_id = customers.id AND invoices.status = "paid") as total_items_purchased');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            // Ordenar por diferentes criterios
            $sortBy = $request->get('sort_by', 'total_spent');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

            $customers = $query->paginate($request->get('per_page', 20));

            // Top productos por cliente (si se especifica un cliente)
            $customerProductAnalysis = null;
            if ($request->filled('customer_id')) {
                $customerProductAnalysis = InvoiceItem::select('products.name', 'products.id')
                    ->selectRaw('SUM(invoice_items.quantity) as total_quantity, SUM(invoice_items.subtotal + invoice_items.tax_amount) as total_spent')
                    ->join('products', 'invoice_items.product_id', '=', 'products.id')
                    ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                    ->where('invoices.customer_id', $request->customer_id)
                    ->where('invoices.status', 'paid')
                    ->groupBy('products.id', 'products.name')
                    ->orderBy('total_quantity', 'desc')
                    ->limit(10)
                    ->get();
            }

            return response()->json([
                'success' => true,
                'data' => $customers,
                'customerProductAnalysis' => $customerProductAnalysis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener análisis de clientes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 5. PROVEEDORES
     * Gestión y análisis de proveedores
     */
    public function suppliers(Request $request)
    {
        try {
            $query = Supplier::query();

            // Agregar métricas por proveedor
            $query->selectRaw('suppliers.*,
                (SELECT COUNT(*) FROM products WHERE supplier_id = suppliers.id) as total_products,
                (SELECT SUM(current_stock * cost_price) FROM products WHERE supplier_id = suppliers.id) as inventory_value,
                (SELECT AVG(cost_price) FROM products WHERE supplier_id = suppliers.id) as avg_cost_price');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('contact_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->filled('active')) {
                $query->where('active', $request->boolean('active'));
            }

            $suppliers = $query->paginate($request->get('per_page', 20));

            // Análisis específico por proveedor
            $supplierAnalysis = null;
            if ($request->filled('supplier_id')) {
                $supplierId = $request->supplier_id;

                $supplierAnalysis = [
                    'productCategories' => Product::select('categories.name', 'categories.id')
                        ->selectRaw('COUNT(*) as product_count')
                        ->join('categories', 'products.category_id', '=', 'categories.id')
                        ->where('products.supplier_id', $supplierId)
                        ->groupBy('categories.id', 'categories.name')
                        ->get(),

                    'topSellingProducts' => Product::select('products.name', 'products.id')
                        ->selectRaw('SUM(invoice_items.quantity) as total_sold')
                        ->leftJoin('invoice_items', 'products.id', '=', 'invoice_items.product_id')
                        ->leftJoin('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                        ->where('products.supplier_id', $supplierId)
                        ->where(function($query) {
                            $query->whereNull('invoices.status')->orWhere('invoices.status', 'paid');
                        })
                        ->groupBy('products.id', 'products.name')
                        ->orderBy('total_sold', 'desc')
                        ->limit(10)
                        ->get(),

                    'stockLevels' => [
                        'low_stock' => Product::where('supplier_id', $supplierId)->lowStock()->count(),
                        'out_of_stock' => Product::where('supplier_id', $supplierId)
                            ->where('current_stock', '<=', 0)
                            ->where('manage_stock', true)
                            ->count(),
                        'normal_stock' => Product::where('supplier_id', $supplierId)
                            ->whereRaw('current_stock > min_stock')
                            ->where('manage_stock', true)
                            ->count()
                    ]
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $suppliers,
                'supplierAnalysis' => $supplierAnalysis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener análisis de proveedores',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 6. ALERTAS
     * Sistema de alertas del inventario
     */
    public function alerts()
    {
        try {
            $alerts = [];

            // Stock bajo
            $lowStockProducts = Product::with(['category', 'supplier'])
                ->lowStock()
                ->get()
                ->map(function($product) {
                    return [
                        'type' => 'low_stock',
                        'priority' => 'medium',
                        'title' => 'Stock Bajo',
                        'message' => "El producto '{$product->name}' tiene stock bajo ({$product->current_stock} unidades)",
                        'product' => $product,
                        'action_required' => 'Reabastecer inventario'
                    ];
                });

            // Sin stock
            $outOfStockProducts = Product::with(['category', 'supplier'])
                ->where('current_stock', '<=', 0)
                ->where('manage_stock', true)
                ->get()
                ->map(function($product) {
                    return [
                        'type' => 'out_of_stock',
                        'priority' => 'high',
                        'title' => 'Sin Stock',
                        'message' => "El producto '{$product->name}' está agotado",
                        'product' => $product,
                        'action_required' => 'Reabastecer urgentemente'
                    ];
                });

            // Productos sin movimiento (últimos 30 días)
            $stagnantProducts = Product::with(['category', 'supplier'])
                ->whereDoesntHave('inventoryMovements', function($query) {
                    $query->where('movement_date', '>=', Carbon::now()->subDays(30));
                })
                ->where('current_stock', '>', 0)
                ->get()
                ->map(function($product) {
                    return [
                        'type' => 'stagnant',
                        'priority' => 'low',
                        'title' => 'Producto Estancado',
                        'message' => "El producto '{$product->name}' no ha tenido movimientos en 30 días",
                        'product' => $product,
                        'action_required' => 'Revisar estrategia de ventas'
                    ];
                });

            // Proveedores con deuda alta
            $highDebtSuppliers = Supplier::where('current_debt', '>', 0)
                ->whereRaw('current_debt >= credit_limit * 0.8')
                ->get()
                ->map(function($supplier) {
                    return [
                        'type' => 'high_debt',
                        'priority' => 'medium',
                        'title' => 'Deuda Alta con Proveedor',
                        'message' => "El proveedor '{$supplier->name}' tiene una deuda de $" . number_format($supplier->current_debt, 2),
                        'supplier' => $supplier,
                        'action_required' => 'Revisar pagos pendientes'
                    ];
                });

            $alerts = collect()
                ->merge($outOfStockProducts)
                ->merge($lowStockProducts)
                ->merge($stagnantProducts)
                ->merge($highDebtSuppliers)
                ->sortByDesc('priority')
                ->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'alerts' => $alerts,
                    'summary' => [
                        'total' => $alerts->count(),
                        'high_priority' => $alerts->where('priority', 'high')->count(),
                        'medium_priority' => $alerts->where('priority', 'medium')->count(),
                        'low_priority' => $alerts->where('priority', 'low')->count()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las alertas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 7. PREDICCIONES
     * Análisis predictivo del inventario
     */
    public function predictions(Request $request)
    {
        try {
            $period = $request->get('period', 30); // Días para la predicción

            // Predicción de demanda por producto
            $demandPredictions = Product::select('products.*')
                ->selectRaw('
                    AVG(daily_sales.quantity) as avg_daily_sales,
                    (AVG(daily_sales.quantity) * ?) as predicted_demand,
                    (current_stock / NULLIF(AVG(daily_sales.quantity), 0)) as days_of_stock
                ', [$period])
                ->leftJoin(DB::raw('(
                    SELECT
                        product_id,
                        DATE(invoices.date) as sale_date,
                        SUM(invoice_items.quantity) as quantity
                    FROM invoice_items
                    JOIN invoices ON invoice_items.invoice_id = invoices.id
                    WHERE invoices.date >= DATE_SUB(NOW(), INTERVAL 90 DAY)
                    AND invoices.status = "paid"
                    GROUP BY product_id, DATE(invoices.date)
                ) as daily_sales'), 'products.id', '=', 'daily_sales.product_id')
                ->groupBy('products.id')
                ->having('avg_daily_sales', '>', 0)
                ->orderBy('days_of_stock', 'asc')
                ->limit(20)
                ->get();

            // Productos que necesitarán reabastecimiento
            $restockNeeded = $demandPredictions->filter(function($product) {
                return $product->days_of_stock && $product->days_of_stock < 14; // Menos de 2 semanas
            });

            // Análisis de tendencias de ventas
            $salesTrends = Sale::selectRaw('
                    DATE(created_at) as date,
                    COUNT(*) as orders,
                    SUM(total_amount) as revenue,
                    AVG(total_amount) as avg_order_value
                ')
                ->where('created_at', '>=', Carbon::now()->subDays(90))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Predicción de ingresos
            $avgDailyRevenue = $salesTrends->avg('revenue');
            $predictedRevenue = $avgDailyRevenue * $period;

            // Análisis estacional (por día de la semana)
            $seasonalAnalysis = Sale::selectRaw('
                    DAYNAME(created_at) as day_name,
                    DAYOFWEEK(created_at) as day_number,
                    AVG(total_amount) as avg_revenue,
                    COUNT(*) as avg_orders
                ')
                ->where('created_at', '>=', Carbon::now()->subDays(90))
                ->groupBy('day_name', 'day_number')
                ->orderBy('day_number')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'demandPredictions' => $demandPredictions,
                    'restockNeeded' => $restockNeeded,
                    'salesTrends' => $salesTrends,
                    'predictions' => [
                        'period_days' => $period,
                        'predicted_revenue' => $predictedRevenue,
                        'avg_daily_revenue' => $avgDailyRevenue
                    ],
                    'seasonalAnalysis' => $seasonalAnalysis
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar predicciones',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * MÉTODO AUXILIAR: Registrar movimiento de inventario
     */
    public function recordMovement(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'type' => 'required|in:purchase,sale,adjustment,return,transfer',
                'quantity' => 'required|integer',
                'unit_cost' => 'nullable|numeric|min:0',
                'reference' => 'nullable|string|max:255',
                'notes' => 'nullable|string'
            ]);

            $product = Product::findOrFail($request->product_id);

            // Actualizar stock y registrar movimiento
            $product->updateStock(
                $request->quantity,
                $request->type,
                $request->reference,
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Movimiento registrado correctamente',
                'data' => $product->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el movimiento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * NOTIFICACIONES DE MOVIMIENTOS
     * Obtener movimientos recientes para notificaciones en el header
     */
    public function notifications(Request $request)
    {
        try {
            // Obtener los últimos movimientos (últimas 24 horas por defecto)
            $hours = $request->get('hours', 24);
            $limit = $request->get('limit', 15);

            $movements = InventoryMovement::with(['product:id,name,sku', 'user:id,name'])
                ->where('movement_date', '>=', Carbon::now()->subHours($hours))
                ->orderBy('movement_date', 'desc')
                ->limit($limit)
                ->get()
                ->map(function($movement) {
                    // Formatear para notificaciones
                    return [
                        'id' => $movement->id,
                        'type' => $movement->type,
                        'reason' => $movement->reason,
                        'product_name' => $movement->product->name ?? 'Producto desconocido',
                        'product_sku' => $movement->product->sku ?? '',
                        'quantity' => abs($movement->quantity),
                        'user_name' => $movement->user->name ?? 'Sistema',
                        'movement_date' => $movement->movement_date->format('Y-m-d H:i:s'),
                        'formatted_date' => $movement->movement_date->diffForHumans(),
                        'icon_type' => $this->getIconType($movement->type, $movement->reason),
                        'color' => $this->getColorType($movement->type, $movement->reason),
                        'title' => $this->getNotificationTitle($movement),
                        'description' => $this->getNotificationDescription($movement)
                    ];
                });

            // Contar movimientos no leídos (todos los recientes)
            $unreadCount = $movements->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'notifications' => $movements,
                    'unread_count' => $unreadCount,
                    'last_check' => now()->toIso8601String()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener notificaciones',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener el tipo de icono para la notificación
     */
    private function getIconType($type, $reason)
    {
        if ($type === 'in') {
            return match($reason) {
                'purchase' => 'box-in',
                'adjustment_positive' => 'plus',
                'returned' => 'return',
                default => 'arrow-up'
            };
        } else {
            return match($reason) {
                'sale' => 'shopping-cart',
                'adjustment_negative' => 'minus',
                'loss' => 'alert',
                'expired' => 'warning',
                'damaged' => 'x-circle',
                default => 'arrow-down'
            };
        }
    }

    /**
     * Obtener el color para la notificación
     */
    private function getColorType($type, $reason)
    {
        if ($type === 'in') {
            return 'emerald'; // Verde para entradas
        } else {
            return match($reason) {
                'sale' => 'blue',
                'loss', 'expired', 'damaged' => 'red',
                default => 'amber'
            };
        }
    }

    /**
     * Generar título de notificación
     */
    private function getNotificationTitle($movement)
    {
        $type = $movement->type === 'in' ? 'Entrada' : 'Salida';

        return match($movement->reason) {
            'sale' => 'Venta realizada',
            'purchase' => 'Compra registrada',
            'adjustment_positive' => 'Ajuste positivo',
            'adjustment_negative' => 'Ajuste negativo',
            'loss' => 'Pérdida registrada',
            'expired' => 'Producto vencido',
            'damaged' => 'Producto dañado',
            'returned' => 'Devolución',
            'transfer' => 'Transferencia',
            default => $type . ' de inventario'
        };
    }

    /**
     * Generar descripción de notificación
     */
    private function getNotificationDescription($movement)
    {
        $quantity = abs($movement->quantity);
        $productName = $movement->product->name ?? 'Producto';

        if ($movement->type === 'in') {
            return "+{$quantity} unidades de {$productName}";
        } else {
            return "-{$quantity} unidades de {$productName}";
        }
    }
}
