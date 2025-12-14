<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with([
            'category',
            'supplier',
            'warehouses' => function($q) {
                $q->select('warehouses.id', 'warehouses.name')
                  ->withPivot('stock'); // 🏢 Incluir stock de la tabla pivot
            }
        ]);

        // Filtrar por estado (activo/inactivo/todos)
        $status = $request->get('status', 'active'); // Por defecto solo activos

        if ($status === 'active') {
            $query->where('active', true);
        } elseif ($status === 'inactive') {
            $query->where('active', false);
        }
        // Si es 'all', no aplicamos filtro de estado

        // 🏭 Filtrar por proveedor
        if ($request->has('supplier_id') && $request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Obtener el número de elementos por página (por defecto 15, máximo 1000)
        $perPage = min($request->get('per_page', 15), 1000);

        $products = $query->orderBy('name')
                         ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Productos obtenidos exitosamente'
        ]);
    }

    // Endpoint optimizado para POS - sin paginación, solo campos necesarios
    public function forPos(Request $request)
    {
        $warehouseId = $request->query('warehouse_id');
        $searchScope = $request->query('scope', 'local'); // 'local' o 'global'

        $query = Product::select([
                'id', 'name', 'sku', 'barcode', 'sale_price as price',
                'current_stock as stock', 'category_id', 'image_url as image',
                'active', 'manage_stock',
                'measurement_unit', 'allow_decimal'  // 📏 Incluir unidades de medida
            ])
            ->with([
                'category:id,name,color',
                'warehouses' => function($q) {
                    $q->select('warehouses.id', 'warehouses.name', 'stock');
                }
            ])
            ->where('active', true);

        // 🏪 BÚSQUEDA LOCAL vs GLOBAL
        if ($warehouseId) {
            if ($searchScope === 'global') {
                // 🌍 MODO GLOBAL: Mostrar productos con stock en CUALQUIER bodega
                $query->whereHas('warehouses', function($q) {
                    $q->where('stock', '>', 0);
                });
            } else {
                // 📍 MODO LOCAL: SOLO productos de la bodega actual
                $query->whereHas('warehouses', function($q) use ($warehouseId) {
                    $q->where('warehouse_id', $warehouseId)
                      ->where('stock', '>', 0);
                });
            }
        } else {
            // Sin bodega especificada, mostrar productos con stock en CUALQUIER bodega
            $query->where('current_stock', '>', 0);
        }

        $products = $query->orderBy('name')
            ->get()
            ->map(function ($product) use ($warehouseId, $searchScope) {
                // Añadir el nombre de la categoría directamente al producto
                $product->category_name = $product->category ? $product->category->name : 'Sin categoría';
                $product->category_color = $product->category ? $product->category->color : '#6b7280';

                // Información de stock por bodega (transformar a formato correcto)
                $product->warehouse_stock = $product->warehouses ? $product->warehouses->map(function($wh) {
                    return [
                        'warehouse_id' => $wh->id,
                        'name' => $wh->name,
                        'stock' => (int) $wh->pivot->stock
                    ];
                })->toArray() : [];

                // Determinar stock local y bodegas alternativas
                $localStock = 0;
                $alternativeWarehouses = [];

                if ($warehouseId && $product->warehouses) {
                    $currentWarehouse = $product->warehouses->firstWhere('id', $warehouseId);

                    if ($currentWarehouse) {
                        $localStock = (int) $currentWarehouse->pivot->stock;
                    }

                    // Si es búsqueda global, encontrar bodegas alternativas
                    if ($searchScope === 'global') {
                        foreach ($product->warehouses as $warehouse) {
                            if ($warehouse->id != $warehouseId && $warehouse->pivot->stock > 0) {
                                $alternativeWarehouses[] = [
                                    'id' => $warehouse->id,
                                    'name' => $warehouse->name,
                                    'stock' => (int) $warehouse->pivot->stock
                                ];
                            }
                        }
                    }
                }

                $product->stock = $localStock;
                $product->is_remote = $searchScope === 'global' && $localStock === 0 && count($alternativeWarehouses) > 0;
                $product->alternative_warehouses = $alternativeWarehouses;

                // 📏 Agregar accessors de unidades de medida
                $product->unit_abbreviation = $product->unit_abbreviation;
                $product->unit_name = $product->unit_name;
                $product->quantity_step = $product->quantity_step;

                return $product;
            });

        return response()->json([
            'success' => true,
            'data' => $products,
            'warehouse_id' => $warehouseId,
            'search_scope' => $searchScope,
            'message' => 'Productos para POS obtenidos exitosamente'
        ]);
    }

    public function store(Request $request)
    {
        // 🐛 DEBUG: Ver exactamente qué datos llegan del frontend
        \Log::info('🚀 [ProductController@store] REQUEST RECIBIDO:', [
            'all_data' => $request->all(),
            'warehouse_stocks' => $request->warehouse_stocks,
            'warehouse_id' => $request->warehouse_id,
            'has_warehouse_stocks' => $request->has('warehouse_stocks'),
            'is_array' => is_array($request->warehouse_stocks),
            'count' => $request->has('warehouse_stocks') ? count($request->warehouse_stocks ?? []) : 0
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'category_id' => 'required|exists:categories,id',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'warehouse_stocks' => 'nullable|array' // 🏢 Stock por cada tienda { warehouse_id: cantidad }
        ]);

        $product = Product::create($request->except(['warehouse_id', 'warehouse_stocks']));
        $product->load(['category', 'supplier']);

        // 🏢 NUEVO: Sistema Multi-Tienda - Asignar stock a múltiples tiendas
        if ($request->has('warehouse_stocks') && is_array($request->warehouse_stocks) && count($request->warehouse_stocks) > 0) {
            // ✅ Solo crear en las sedes especificadas explícitamente
            \Log::info('📦 [ProductController] Creando producto en sedes específicas:', [
                'product_id' => $product->id,
                'warehouse_stocks' => $request->warehouse_stocks
            ]);

            foreach ($request->warehouse_stocks as $warehouseId => $stock) {
                $stockValue = intval($stock);
                $product->warehouses()->attach($warehouseId, [
                    'stock' => $stockValue
                ]);

                \Log::info("✅ [ProductController] Producto creado en sede {$warehouseId} con stock {$stockValue}");
            }
        } elseif ($request->has('warehouse_id') && $request->warehouse_id) {
            // Fallback: una sola bodega especificada
            \Log::info('📦 [ProductController] Creando producto en bodega única:', [
                'product_id' => $product->id,
                'warehouse_id' => $request->warehouse_id,
                'stock' => $request->current_stock ?? 0
            ]);

            $product->warehouses()->attach($request->warehouse_id, [
                'stock' => $request->current_stock ?? 0
            ]);
        } else {
            // Último fallback: bodega predeterminada
            $defaultWarehouse = \App\Models\Warehouse::where('is_default', true)->first();
            if ($defaultWarehouse) {
                \Log::info('⚠️ [ProductController] No se especificaron bodegas, usando bodega predeterminada:', [
                    'product_id' => $product->id,
                    'default_warehouse_id' => $defaultWarehouse->id
                ]);

                $product->warehouses()->attach($defaultWarehouse->id, [
                    'stock' => $request->current_stock ?? 0
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Producto creado exitosamente'
        ], 201);
    }

    public function show(Product $product)
    {
        $product->load(['category', 'supplier']);
        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function update(Request $request, Product $product)
    {
        \Log::info('🔄 [ProductController@update] Actualizando producto:', [
            'product_id' => $product->id,
            'warehouse_stocks' => $request->warehouse_stocks,
            'has_warehouse_stocks' => $request->has('warehouse_stocks'),
            'is_array' => is_array($request->warehouse_stocks ?? null)
        ]);

        // Si se está activando manualmente el producto, limpiar la bandera
        if ($request->has('active') && $request->active === true && $product->active === false) {
            $request->merge(['deactivated_by_category' => false]);
        }

        $product->update($request->except(['warehouse_id', 'warehouse_stocks']));

        // 🏢 CRÍTICO: Sistema Multi-Tienda - Actualizar stock SIN ELIMINAR relaciones existentes
        if ($request->has('warehouse_stocks') && is_array($request->warehouse_stocks) && count($request->warehouse_stocks) > 0) {
            // Preparar datos para sincronización
            $warehouseStocks = [];
            foreach ($request->warehouse_stocks as $warehouseId => $stock) {
                $stockValue = intval($stock);
                if ($stockValue >= 0) { // Incluir stock 0 para actualizar
                    $warehouseStocks[$warehouseId] = ['stock' => $stockValue];
                }
            }

            \Log::info('✅ [ProductController@update] Sincronizando warehouse_stocks:', [
                'warehouse_stocks' => $warehouseStocks
            ]);

            // 🔥 USAR syncWithoutDetaching en lugar de sync para NO ELIMINAR relaciones
            // Esto actualiza las que están en el array, pero mantiene las demás
            $product->warehouses()->syncWithoutDetaching($warehouseStocks);
        } else {
            \Log::warning('⚠️ [ProductController@update] NO se recibió warehouse_stocks válido - NO se modificarán las relaciones');
        }

        // Recalcular current_stock sumando todas las warehouses
        $totalStock = $product->warehouses()->sum('product_warehouse.stock');
        $product->update(['current_stock' => $totalStock]);

        $product->load(['category', 'supplier', 'warehouses']);

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Producto actualizado exitosamente'
        ]);
    }

    public function destroy(Product $product)
    {
        $product->update(['active' => false]);
        return response()->json([
            'success' => true,
            'message' => 'Producto desactivado exitosamente'
        ]);
    }

    public function lowStock()
    {
        $products = Product::lowStock()
                          ->with(['category', 'supplier'])
                          ->orderBy('current_stock')
                          ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Productos con stock bajo'
        ]);
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:purchase,sale,adjustment,return,transfer',
            'warehouse_id' => 'nullable|exists:warehouses,id' // NUEVO: validar warehouse_id
        ]);

        $warehouseId = $request->warehouse_id;

        // Si se proporciona warehouse_id, usar método multi-sede
        if ($warehouseId) {
            $product->updateStockInWarehouse(
                $warehouseId,
                $request->quantity,
                $request->type,
                $request->reference ?? 'Manual',
                auth()->id() ?? 1
            );
        } else {
            // Si no hay warehouse_id, usar método tradicional (sede por defecto)
            $product->updateStock(
                $request->quantity,
                $request->type,
                $request->reference ?? 'Manual',
                auth()->id() ?? 1
            );
        }

        return response()->json([
            'success' => true,
            'data' => $product->fresh(),
            'message' => 'Stock actualizado exitosamente'
        ]);
    }
}
