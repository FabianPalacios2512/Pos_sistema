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
            'warehouses:warehouses.id,warehouses.name' // 🏢 Incluir bodegas con stock
        ]);

        // Filtrar por estado (activo/inactivo/todos)
        $status = $request->get('status', 'active'); // Por defecto solo activos

        if ($status === 'active') {
            $query->where('active', true);
        } elseif ($status === 'inactive') {
            $query->where('active', false);
        }
        // Si es 'all', no aplicamos filtro de estado

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
                'active', 'manage_stock'
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

                // Información de stock por bodega
                $product->warehouse_stock = $product->warehouses ? $product->warehouses->toArray() : [];

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
        if ($request->has('warehouse_stocks') && is_array($request->warehouse_stocks)) {
            foreach ($request->warehouse_stocks as $warehouseId => $stock) {
                $stockValue = intval($stock);
                if ($stockValue > 0) { // Solo guardar si hay stock
                    $product->warehouses()->attach($warehouseId, [
                        'stock' => $stockValue
                    ]);
                }
            }
        } else {
            // Fallback al sistema anterior (una sola bodega)
            if ($request->has('warehouse_id') && $request->warehouse_id) {
                $product->warehouses()->attach($request->warehouse_id, [
                    'stock' => $request->current_stock ?? 0
                ]);
            } else {
                // Si no se especificó bodega, buscar la bodega predeterminada
                $defaultWarehouse = \App\Models\Warehouse::where('is_default', true)->first();
                if ($defaultWarehouse) {
                    $product->warehouses()->attach($defaultWarehouse->id, [
                        'stock' => $request->current_stock ?? 0
                    ]);
                }
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
        // Si se está activando manualmente el producto, limpiar la bandera
        if ($request->has('active') && $request->active === true && $product->active === false) {
            $request->merge(['deactivated_by_category' => false]);
        }

        $product->update($request->except(['warehouse_id', 'warehouse_stocks']));

        // 🏢 NUEVO: Sistema Multi-Tienda - Actualizar stock en múltiples tiendas
        if ($request->has('warehouse_stocks') && is_array($request->warehouse_stocks)) {
            // Sincronizar stock: actualizar existentes, agregar nuevos, mantener antiguos
            $warehouseStocks = [];
            foreach ($request->warehouse_stocks as $warehouseId => $stock) {
                $stockValue = intval($stock);
                if ($stockValue >= 0) { // Incluir stock 0 para actualizar
                    $warehouseStocks[$warehouseId] = ['stock' => $stockValue];
                }
            }

            // sync() actualiza/inserta/elimina según sea necesario
            $product->warehouses()->sync($warehouseStocks);
        }

        $product->load(['category', 'supplier']);

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
            'type' => 'required|in:purchase,sale,adjustment,return,transfer'
        ]);

        $product->updateStock(
            $request->quantity,
            $request->type,
            $request->reference ?? 'Manual',
            auth()->id() ?? 1
        );

        return response()->json([
            'success' => true,
            'data' => $product->fresh(),
            'message' => 'Stock actualizado exitosamente'
        ]);
    }
}
