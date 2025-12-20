<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductOption;
use App\Models\ProductImage;
use App\Models\ProductOptionValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            },
            'variants' => function($q) {
                $q->select('product_variants.*')
                  ->with(['optionValues.option']);
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

        // Formatear las variantes para incluir options_summary
        $products->getCollection()->transform(function($product) {
            if ($product->variants) {
                $product->variants->each(function($variant) {
                    $optionsSummary = $variant->optionValues->map(function($optionValue) {
                        return [
                            'name' => $optionValue->option->name,
                            'value' => $optionValue->value
                        ];
                    });
                    $variant->options_summary = $optionsSummary;
                });
            }
            return $product;
        });

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
                'active', 'manage_stock', 'product_type',
                'measurement_unit', 'allow_decimal'  // 📏 Incluir unidades de medida
            ])
            ->with([
                'category:id,name,color',
                'warehouses' => function($q) {
                    $q->select('warehouses.id', 'warehouses.name', 'stock');
                },
                'variants' => function($query) {
                    $query->select('product_variants.*')
                          ->where('active', true)
                          ->with(['optionValues.option']);
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

                // 👕 Formatear variantes con options_summary
                if ($product->product_type === 'variable' && $product->variants) {
                    $product->variants->each(function($variant) {
                        $optionsSummary = $variant->optionValues->map(function($optionValue) {
                            return [
                                'name' => $optionValue->option->name,
                                'value' => $optionValue->value
                            ];
                        });
                        $variant->options_summary = $optionsSummary;
                    });
                }

                // Agregar campo auxiliar para el frontend
                $product->has_variants = $product->product_type === 'variable';

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
        // 🐛 DEBUG: Ver datos recibidos
        \Log::info('🚀 [ProductController@store] REQUEST:', $request->all());

        // Normalizar input: permitir 'type' como alias de 'product_type'
        if ($request->has('type') && !$request->has('product_type')) {
            $request->merge(['product_type' => $request->type]);
        }

        // 1. Validación
        $request->validate([
            'name' => 'required|string|max:255',
            'product_type' => 'required|in:simple,variable',
            'category_id' => 'required|exists:categories,id',
            // Validación condicional
            'sku' => 'required_if:product_type,simple|nullable|string|unique:products,sku',
            'sale_price' => 'required_if:product_type,simple|nullable|numeric|min:0',
            'variants' => 'required_if:product_type,variable|array',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ]);

        return DB::transaction(function () use ($request) {
            // 2. Crear Producto Padre
            $productData = $request->only([
                'name', 'product_type', 'category_id', 'description',
                'brand_id', 'supplier_id', 'barcode', 'cost_price',
                'min_stock', 'measurement_unit', 'allow_decimal', 'tax_rate'
            ]);

            // Si es simple, tomamos sku y precio del request.
            if ($request->product_type === 'simple') {
                $productData['sku'] = $request->sku;
                $productData['sale_price'] = $request->sale_price;
                $productData['current_stock'] = $request->current_stock ?? 0;
            } else {
                // Para variable, calculamos el precio base desde las variantes
                $productData['sku'] = $request->sku;

                // Calcular precio mínimo de las variantes para mostrar "Desde $X"
                $minPrice = 0;
                if ($request->has('variants') && is_array($request->variants) && count($request->variants) > 0) {
                    $prices = array_column($request->variants, 'price');
                    $minPrice = min($prices);
                }

                $productData['sale_price'] = $minPrice;
                $productData['current_stock'] = 0; // Suma de variantes se actualiza después
            }

            $product = Product::create($productData);

            // 3. Manejo de Imágenes (Galería)
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('products', 'public');
                    $url = Storage::url($path);

                    $product->images()->create([
                        'image_url' => $url,
                        'is_primary' => $index === 0,
                        'order' => $index
                    ]);

                    // Legacy support: primera imagen en tabla products
                    if ($index === 0) {
                        $product->update(['image_url' => $url]);
                    }
                }
            }

            // 4. Lógica Híbrida
            $warehouseId = $request->warehouse_id ?? 1; // Default warehouse

            if ($request->product_type === 'simple') {
                // --- LÓGICA SIMPLE (LEGACY) ---
                $stock = $request->current_stock ?? 0;

                // Guardar en product_warehouse
                $product->warehouses()->attach($warehouseId, [
                    'stock' => $stock,
                    'product_variant_id' => null
                ]);

                // Soporte para warehouse_stocks (Multi-sede explícito)
                if ($request->has('warehouse_stocks') && is_array($request->warehouse_stocks)) {
                    $product->warehouses()->syncWithoutDetaching([]); // Limpiar si es necesario o manejar lógica
                    foreach ($request->warehouse_stocks as $whId => $stk) {
                        $product->warehouses()->syncWithoutDetaching([
                            $whId => ['stock' => $stk, 'product_variant_id' => null]
                        ]);
                    }
                }

            } else {
                // --- LÓGICA VARIABLE ---

                // a. Guardar Opciones (Atributos)
                $optionValueMap = []; // Mapa: [NombreOpcion][Valor] => ID

                if ($request->has('options') && is_array($request->options)) {
                    foreach ($request->options as $optData) {
                        // Crear Opción (ej: Talla)
                        $option = $product->options()->create(['name' => $optData['name']]);

                        // Crear Valores (ej: S, M)
                        foreach ($optData['values'] as $val) {
                            $valObj = $option->values()->create(['value' => $val]);
                            $optionValueMap[$optData['name']][$val] = $valObj->id;
                        }
                    }
                }

                // b. Iterar Variantes
                $totalStock = 0;

                if ($request->has('variants') && is_array($request->variants)) {
                    foreach ($request->variants as $variantData) {
                        // Crear Variante
                        $variant = $product->variants()->create([
                            'sku' => $variantData['sku'],
                            'price' => $variantData['price'],
                            'stock' => $variantData['stock'] ?? 0,
                            'options_summary' => $variantData['options'] ?? null // JSON
                        ]);

                        $totalStock += ($variantData['stock'] ?? 0);

                        // Vincular con Valores de Opción (Pivot)
                        if (isset($variantData['options']) && is_array($variantData['options'])) {
                            foreach ($variantData['options'] as $optName => $optVal) {
                                if (isset($optionValueMap[$optName][$optVal])) {
                                    $variant->optionValues()->attach($optionValueMap[$optName][$optVal]);
                                }
                            }
                        }

                        // Guardar Stock en Bodega (product_warehouse)
                        // Asumimos que el stock inicial va a la bodega seleccionada
                        $product->warehouses()->attach($warehouseId, [
                            'product_variant_id' => $variant->id,
                            'stock' => $variantData['stock'] ?? 0
                        ]);
                    }
                }

                // Actualizar stock total del padre
                $product->update(['current_stock' => $totalStock]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Producto creado exitosamente',
                'data' => $product->load('variants', 'options', 'images')
            ], 201);
        });
    }

    public function show(Product $product)
    {
        $product->load([
            'category',
            'supplier',
            'variants' => function($query) {
                $query->select('product_variants.*')
                      ->with(['optionValues.option']);
            },
            'images'
        ]);

        // Formatear las variantes para incluir options_summary
        if ($product->variants) {
            $product->variants->each(function($variant) {
                $optionsSummary = $variant->optionValues->map(function($optionValue) {
                    return [
                        'name' => $optionValue->option->name,
                        'value' => $optionValue->value
                    ];
                });
                $variant->options_summary = $optionsSummary;
            });
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function update(Request $request, Product $product)
    {
        \Log::info('🔄 [ProductController@update] Actualizando producto:', [
            'product_id' => $product->id,
            'type' => $product->product_type,
            'data' => $request->all()
        ]);

        return DB::transaction(function () use ($request, $product) {
            // 1. Actualizar campos básicos
            $product->update($request->except(['warehouse_id', 'warehouse_stocks', 'variants', 'options', 'images']));

            // 2. Manejo de Imágenes (Agregar nuevas)
            if ($request->hasFile('images')) {
                $currentMaxOrder = $product->images()->max('order') ?? 0;
                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('products', 'public');
                    $url = Storage::url($path);

                    $product->images()->create([
                        'image_url' => $url,
                        'is_primary' => false,
                        'order' => $currentMaxOrder + $index + 1
                    ]);
                }
            }

            // 3. Lógica según tipo
            if ($product->product_type === 'simple') {
                // --- LÓGICA SIMPLE (LEGACY) ---

                // Actualizar stock en warehouses
                if ($request->has('warehouse_stocks') && is_array($request->warehouse_stocks)) {
                    $warehouseStocks = [];
                    foreach ($request->warehouse_stocks as $warehouseId => $stock) {
                        $stockValue = intval($stock);
                        if ($stockValue >= 0) {
                            $warehouseStocks[$warehouseId] = ['stock' => $stockValue, 'product_variant_id' => null];
                        }
                    }
                    $product->warehouses()->syncWithoutDetaching($warehouseStocks);
                }

                // Recalcular stock total
                $totalStock = $product->warehouses()->whereNull('product_variant_id')->sum('stock');
                $product->update(['current_stock' => $totalStock]);

            } else {
                // --- LÓGICA VARIABLE ---
                // Por ahora, solo permitimos actualizar campos básicos del padre y agregar imágenes.
                // La edición de variantes (agregar/quitar opciones) es compleja y requiere un endpoint dedicado o lógica avanzada.
                // Si se envían variantes, podríamos intentar actualizar precios/stock de las existentes por SKU.

                if ($request->has('variants') && is_array($request->variants)) {
                    foreach ($request->variants as $variantData) {
                        if (isset($variantData['id'])) {
                            // Actualizar variante existente
                            $variant = ProductVariant::find($variantData['id']);
                            if ($variant && $variant->product_id === $product->id) {
                                $variant->update([
                                    'sku' => $variantData['sku'] ?? $variant->sku,
                                    'price' => $variantData['price'] ?? $variant->price,
                                    'stock' => $variantData['stock'] ?? $variant->stock
                                ]);

                                // Actualizar stock en warehouse (asumiendo warehouse_id del request o default)
                                $warehouseId = $request->warehouse_id ?? 1;
                                // Buscar si existe registro en pivot, si no crear
                                $existingPivot = DB::table('product_warehouse')
                                    ->where('product_id', $product->id)
                                    ->where('product_variant_id', $variant->id)
                                    ->where('warehouse_id', $warehouseId)
                                    ->first();

                                if ($existingPivot) {
                                    DB::table('product_warehouse')
                                        ->where('id', $existingPivot->id)
                                        ->update(['stock' => $variantData['stock'] ?? 0]);
                                } else {
                                    $product->warehouses()->attach($warehouseId, [
                                        'product_variant_id' => $variant->id,
                                        'stock' => $variantData['stock'] ?? 0
                                    ]);
                                }
                            }
                        }
                    }

                    // Recalcular stock total del padre
                    $totalStock = $product->variants()->sum('stock');
                    $product->update(['current_stock' => $totalStock]);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $product->load(['variants', 'options', 'images', 'warehouses']),
                'message' => 'Producto actualizado exitosamente'
            ]);
        });
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

    /**
     * Actualización masiva de variantes (stock, precio, costo)
     */
    public function bulkUpdateVariants(Request $request)
    {
        $request->validate([
            'variants' => 'required|array',
            'variants.*.id' => 'required|exists:product_variants,id',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.cost_price' => 'nullable|numeric|min:0',
        ]);

        try {
            $updatedCount = 0;

            foreach ($request->variants as $variantData) {
                $variant = \App\Models\ProductVariant::find($variantData['id']);

                if ($variant) {
                    $updateData = [];

                    if (isset($variantData['stock'])) {
                        $updateData['stock'] = $variantData['stock'];
                    }

                    if (isset($variantData['price'])) {
                        $updateData['price'] = $variantData['price'];
                    }

                    if (isset($variantData['cost_price'])) {
                        $updateData['cost_price'] = $variantData['cost_price'];
                    }

                    if (!empty($updateData)) {
                        $variant->update($updateData);
                        $updatedCount++;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Se actualizaron {$updatedCount} variante(s) exitosamente",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en bulkUpdateVariants:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar las variantes: ' . $e->getMessage()
            ], 500);
        }
    }
}
