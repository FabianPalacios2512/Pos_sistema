<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductOption;
use App\Models\ProductImage;
use App\Models\ProductOptionValue;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * ✅ Asegura que el symlink del tenant exista para servir archivos públicos
     * Crea: public/storage/tenants/{tenant_id} -> storage/tenant{tenant_id}/app/public
     *
     * NOTA: Esta función NO debe detener la ejecución si falla - las imágenes
     * se guardan correctamente en storage/, solo el acceso público podría fallar.
     */
    private function ensureTenantStorageLink()
    {
        try {
            $tenantId = tenant('id');
            if (!$tenantId) {
                return; // No es multi-tenant
            }

            // ✅ CORRECCIÓN: El storage del tenant está en storage/tenant{id}/app/public
            $tenantStoragePath = storage_path("tenant{$tenantId}/app/public");
            $symlinkPath = public_path("storage/tenants/{$tenantId}");

            // Si el symlink ya existe, no hacer nada
            if (is_link($symlinkPath) || is_dir($symlinkPath)) {
                return;
            }

            // Asegurar que exista el directorio del tenant
            if (!is_dir($tenantStoragePath)) {
                if (!@mkdir($tenantStoragePath, 0755, true)) {
                    \Log::warning("[Storage] No se pudo crear directorio tenant: {$tenantStoragePath}");
                    return;
                }
            }

            // Asegurar que exista el directorio base public/storage
            $storagePublicDir = public_path('storage');
            if (!is_dir($storagePublicDir)) {
                if (!is_link($storagePublicDir)) {
                    \Log::warning("[Storage] Directorio public/storage no existe. Ejecutar: php artisan storage:link");
                    @mkdir($storagePublicDir, 0755, true);
                }
            }

            // Crear directorio tenants si no existe
            $tenantsDir = public_path('storage/tenants');
            if (!is_dir($tenantsDir)) {
                if (!@mkdir($tenantsDir, 0755, true)) {
                    \Log::warning("[Storage] No se pudo crear directorio tenants: {$tenantsDir}");
                    return;
                }
            }

            // Crear el symlink apuntando al storage correcto del tenant
            if (@symlink($tenantStoragePath, $symlinkPath)) {
                \Log::info("✅ [Storage] Symlink creado para tenant: {$tenantId}", [
                    'symlink' => $symlinkPath,
                    'target' => $tenantStoragePath
                ]);
            } else {
                \Log::warning("⚠️ [Storage] No se pudo crear symlink, continuando sin él", [
                    'tenant' => $tenantId
                ]);
            }
        } catch (\Exception $e) {
            // NUNCA fallar por problemas de symlink - las imágenes igual se guardan en storage
            \Log::error("❌ [Storage] Error en ensureTenantStorageLink (ignorando): {$e->getMessage()}");
        }
    }

    public function index(Request $request)
    {
        $query = Product::with([
            'category',
            'supplier',
            'images' => function($q) {
                $q->orderBy('order');
            },
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

        // 🏢 Filtrar por warehouse (multi-sede)
        $warehouseId = $request->get('warehouse_id');
        if ($warehouseId) {
            $query->whereHas('warehouses', function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            });

            // Cargar solo el warehouse específico
            $query->with(['warehouses' => function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            }]);
        }

        // Obtener el número de elementos por página (por defecto 15, máximo 1000)
        $perPage = min($request->get('per_page', 15), 1000);

        $products = $query->orderBy('name')
                         ->paginate($perPage);

        // ✅ OPTIMIZACIÓN: Solo cargar ventas si se solicita explícitamente
        $includeSales = $request->get('with_sales', false);

        // Formatear las variantes y ajustar stock por warehouse
        $products->getCollection()->transform(function($product) use ($warehouseId, $includeSales) {
            // Formatear options_summary de variantes
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

            // 🏢 Si hay filtro de warehouse, ajustar el stock
            if ($warehouseId) {
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
            }

            // 💰 CALCULAR ventas e ingresos SOLO si se solicitan (evita N+1 queries)
            if ($includeSales) {
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
            } else {
                // Valores por defecto para no romper el frontend
                $product->total_sold = 0;
                $product->total_revenue = 0;
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
                'id', 'name', 'sku', 'barcode', 
                'cost_price',              // 💰 Precio de costo para calcular valor invertido
                'sale_price', 
                'sale_price as price',     // Alias para compatibilidad
                'current_stock', // ✅ Mantener current_stock sin alias
                'category_id', 'image_url as image',
                'active', 'manage_stock', 'product_type',
                'measurement_unit', 'allow_decimal'  // 📏 Incluir unidades de medida
            ])
            ->with([
                'category:id,name,color',
                'images' => function($q) {
                    $q->orderBy('order');
                },
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

                // 🖼️ Mapear imágenes al formato del POS
                if ($product->images && $product->images->isNotEmpty()) {
                    // Usar la imagen primaria o la primera
                    $primaryImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                    if ($primaryImage) {
                        $product->image = $primaryImage->image_url;
                        $product->image_url = $primaryImage->image_url;
                    }
                }

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

                // ✅ CAMBIO CRÍTICO: Si solo hay 1 bodega, usar current_stock directamente
                $totalWarehouses = \App\Models\Warehouse::count();

                if ($totalWarehouses === 1) {
                    // 🏪 MODO TIENDA ÚNICA: current_stock es la fuente única de verdad
                    $localStock = (int) $product->current_stock;
                } else if ($warehouseId && $product->warehouses) {
                    // 🏢 MODO MULTI-TIENDA: Usar stock de bodega específica
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
                $product->current_stock = $product->current_stock; // ✅ Mantener current_stock (stock total)
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
            'store_type' => 'nullable|in:general,fashion', // ✅ Tipo de tienda (general o moda)
            'store_category' => 'nullable|in:general,fashion', // DEPRECATED: Mantener por compatibilidad
            'category_id' => 'required|exists:categories,id',
            // Validación condicional
            'sku' => 'required_if:product_type,simple|nullable|string|unique:products,sku',
            // ✅ FLEXIBILIZAR: sale_price es requerido solo si no viene en variants[0].price
            'sale_price' => 'nullable|numeric|min:0',
            'variants' => 'required_if:product_type,variable|array',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ]);

        // ✅ Validación adicional para productos simples
        if ($request->product_type === 'simple') {
            if (!$request->sale_price && !isset($request->variants[0]['price'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'El precio de venta es requerido para productos simples'
                ], 422);
            }
        }

        return DB::transaction(function () use ($request) {
            // 2. Crear Producto Padre
            $productData = $request->only([
                'name', 'product_type', 'category_id', 'description',
                'brand_id', 'supplier_id', 'barcode', 'cost_price',
                'min_stock', 'measurement_unit', 'allow_decimal', 'tax_rate'
            ]);

            // ✅ Determinar store_type basado en el contexto de creación
            // Prioridad: 1) Request explícito, 2) store_category (legacy), 3) System settings
            if ($request->has('store_type')) {
                $productData['store_type'] = $request->store_type;
            } elseif ($request->has('store_category')) {
                $productData['store_type'] = $request->store_category; // Compatibilidad
            } else {
                // Detectar automáticamente desde configuración del sistema
                $systemStoreType = \DB::table('system_settings')->value('store_type');
                $productData['store_type'] = ($systemStoreType === 'fashion' || $systemStoreType === 'moda') ? 'fashion' : 'general';
            }

            // Si es simple, tomamos sku y precio del request.
            if ($request->product_type === 'simple') {
                $productData['sku'] = $request->sku;
                // ✅ Tomar sale_price o desde variants[0].price como fallback
                $productData['sale_price'] = $request->sale_price ?? ($request->variants[0]['price'] ?? 0);
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

            // ✅ Si se envió image_url (URL externa), incluirla en los datos del producto
            if ($request->has('image_url') && $request->input('image_url')) {
                $productData['image_url'] = $request->input('image_url');
            }

            $product = Product::create($productData);

            // 3. Manejo de Imágenes (Galería)
            if ($request->hasFile('images')) {
                // ✅ Asegurar que el symlink existe antes de guardar imágenes
                $this->ensureTenantStorageLink();

                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('products', 'public');

                    // Para multi-tenancy: construir URL con tenant
                    $tenantId = tenant('id');
                    if ($tenantId) {
                        $url = "/storage/tenants/{$tenantId}/{$path}";
                    } else {
                        $url = Storage::url($path);
                    }

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
                // --- LÓGICA SIMPLE ---

                // ✅ Si se enviaron variantes (productos de moda), crear la variante
                if ($request->has('variants') && is_array($request->variants) && count($request->variants) > 0) {
                    \Log::info('✅ [ProductController@store] Producto simple CON variante explícita');

                    $variantData = $request->variants[0]; // Tomar la primera variante
                    $costPrice = $variantData['cost_price'] ?? $variantData['cost'] ?? 0;

                    \Log::info('🐛 [ProductController@store] Variant Data:', [
                        'sku' => $variantData['sku'] ?? null,
                        'price' => $variantData['price'] ?? null,
                        'cost_price' => $costPrice,
                        'stock' => $variantData['stock'] ?? 0
                    ]);

                    // Crear variante
                    $variant = $product->variants()->create([
                        'sku' => $variantData['sku'] ?? $product->sku,
                        'price' => $variantData['price'],
                        'cost_price' => $costPrice,
                        'stock' => $variantData['stock'] ?? 0,
                        'active' => true,
                        'options_summary' => null
                    ]);

                    \Log::info('✅ [ProductController@store] Variant Created:', [
                        'id' => $variant->id,
                        'cost_price' => $variant->cost_price
                    ]);

                    // Guardar stock en bodega vinculado a la variante
                    $product->warehouses()->attach($warehouseId, [
                        'stock' => $variantData['stock'] ?? 0,
                        'product_variant_id' => $variant->id
                    ]);
                } else {
                    // LÓGICA LEGACY: Sin variantes explícitas
                    \Log::info('⚠️ [ProductController@store] Producto simple SIN variante (legacy)');

                    $stock = $request->current_stock ?? 0;

                    // Guardar en product_warehouse
                    $product->warehouses()->attach($warehouseId, [
                        'stock' => $stock,
                        'product_variant_id' => null
                    ]);
                }

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

                // ✅ Si no hay variantes o el array está vacío, crear variante por defecto
                $variants = $request->variants ?? [];
                if (empty($variants)) {
                    \Log::info('⚠️ [ProductController@store] No variants provided, creating default variant');
                    $variants = [[
                        'sku' => $request->sku ?? 'SKU-' . time(),
                        'price' => $request->sale_price ?? $request->price ?? 0,
                        'cost_price' => $request->cost_price ?? 0,
                        'stock' => $request->current_stock ?? $request->stock ?? 0,
                        'options' => []
                    ]];
                }

                if (is_array($variants)) {
                    foreach ($variants as $variantData) {
                        // 🐛 DEBUG: Ver qué datos llegan para la variante
                        \Log::info('🐛 [ProductController@store] Variant Data Received:', [
                            'sku' => $variantData['sku'] ?? null,
                            'price' => $variantData['price'] ?? null,
                            'cost' => $variantData['cost'] ?? null,
                            'cost_price' => $variantData['cost_price'] ?? null,
                            'stock' => $variantData['stock'] ?? null,
                            'all_data' => $variantData
                        ]);

                        $costPrice = $variantData['cost'] ?? $variantData['cost_price'] ?? 0;

                        \Log::info('🐛 [ProductController@store] Cost Price Calculated:', [
                            'cost_price' => $costPrice
                        ]);

                        // Crear Variante
                        $variant = $product->variants()->create([
                            'sku' => $variantData['sku'],
                            'price' => $variantData['price'],
                            'cost_price' => $costPrice,
                            'stock' => $variantData['stock'] ?? 0,
                            'options_summary' => $variantData['options'] ?? null // JSON
                        ]);

                        \Log::info('🐛 [ProductController@store] Variant Created:', [
                            'id' => $variant->id,
                            'cost_price' => $variant->cost_price
                        ]);

                        $totalStock += ($variantData['stock'] ?? 0);

                        // Vincular con Valores de Opción (Pivot)
                        if (isset($variantData['options'])) {
                            $options = $variantData['options'];

                            // 🔧 SOPORTE PARA AMBOS FORMATOS:
                            // Array de objetos: [{"name": "Talla", "value": "M"}]
                            // Objeto asociativo: {"Talla": "M", "Color": "Rojo"}

                            if (is_string($options)) {
                                $options = json_decode($options, true);
                            }

                            if (is_array($options)) {
                                // Detectar formato: ¿tiene 'name' y 'value'? → Array de objetos
                                if (isset($options[0]) && is_array($options[0]) && isset($options[0]['name'])) {
                                    // Formato: [{"name": "Talla", "value": "M"}]
                                    foreach ($options as $opt) {
                                        $optName = $opt['name'];
                                        $optVal = $opt['value'];
                                        if (isset($optionValueMap[$optName][$optVal])) {
                                            $variant->optionValues()->attach($optionValueMap[$optName][$optVal]);
                                        }
                                    }
                                } else {
                                    // Formato: {"Talla": "M", "Color": "Rojo"}
                                    foreach ($options as $optName => $optVal) {
                                        if (isset($optionValueMap[$optName][$optVal])) {
                                            $variant->optionValues()->attach($optionValueMap[$optName][$optVal]);
                                        }
                                    }
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
            'warehouses' => function($q) {
                $q->select('warehouses.id', 'warehouses.name', 'is_default')
                  ->withPivot('stock');
            },
            'options.values', // Opciones del producto con sus valores
            'variants', // Variantes sin eager loading de opciones
            'images'
        ]);

        // 🐛 DEBUG: Verificar store_category
        \Log::info('🔍 [ProductController@show] Product Data:', [
            'id' => $product->id,
            'name' => $product->name,
            'store_category' => $product->store_category,
            'product_type' => $product->product_type
        ]);

        // Formatear las variantes para incluir options como array simple
        if ($product->variants) {
            $product->variants->each(function($variant) {
                // Decodificar options_summary si existe
                if ($variant->options_summary) {
                    $variant->options = is_string($variant->options_summary)
                        ? json_decode($variant->options_summary, true)
                        : $variant->options_summary;
                } else {
                    $variant->options = [];
                }

                // 🐛 DEBUG: Verificar que cost_price venga en la variante
                \Log::info('🐛 [ProductController@show] Variant Data:', [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'price' => $variant->price,
                    'cost_price' => $variant->cost_price,
                    'stock' => $variant->stock
                ]);
            });
        }

        // Formatear las opciones del producto
        if ($product->options) {
            $product->options = $product->options->map(function($option) {
                return [
                    'name' => $option->name,
                    'values' => $option->values->pluck('value')->toArray()
                ];
            })->toArray();
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function update(Request $request, Product $product)
    {
        // 🔄 ACTUALIZACIÓN SIMPLE: Solo cambio de estado activo/inactivo
        if ($request->has('active') && !$request->has('name')) {
            $product->update([
                'active' => $request->input('active')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estado del producto actualizado',
                'data' => $product->fresh()
            ]);
        }

        \Log::info('🔄 [ProductController@update] Actualizando producto:', [
            'product_id' => $product->id,
            'current_type' => $product->product_type,
            'new_type' => $request->product_type,
            'has_files' => $request->hasFile('images'),
            'all_files' => $request->allFiles(),
            'images' => $request->images,
            'request_all' => $request->all(),
            'name' => $request->input('name'),
            'sku' => $request->input('sku'),
            'variants' => $request->variants,
            'cost_price' => $request->cost_price
        ]);

        return DB::transaction(function () use ($request, $product) {
            // 1. Actualizar tipo de producto si cambió
            $oldType = $product->product_type;
            $newType = $request->input('product_type', $oldType);

            // ✅ Obtener cost_price desde múltiples fuentes
            $costPrice = $request->input('cost_price')
                ?? $request->variants[0]['cost']
                ?? $request->variants[0]['cost_price']
                ?? 0;

            // 2. Actualizar campos básicos del producto
            $updateData = [
                'name' => $request->input('name'),
                'sku' => $request->input('sku'),
                'description' => $request->input('description', ''),
                'category_id' => $request->input('category_id'),
                'supplier_id' => $request->input('supplier_id'),
                'product_type' => $newType,
                'cost_price' => $costPrice,
            ];

            // ✅ Guardar image_url si viene en el request (URL externa)
            if ($request->has('image_url')) {
                $updateData['image_url'] = $request->input('image_url');
            }

            // ✅ Actualizar current_stock si viene en el request (edición directa por IA o manual)
            if ($request->has('current_stock')) {
                $updateData['current_stock'] = (int) $request->input('current_stock');
                \Log::info('📦 [ProductController@update] Actualizando stock directo:', [
                    'product_id' => $product->id,
                    'current_stock' => $updateData['current_stock']
                ]);
            }

            // ✅ Actualizar store_category si viene en el request (permite cambiar entre moda/general)
            if ($request->has('store_category')) {
                $updateData['store_category'] = $request->input('store_category');
            } elseif ($request->has('store_type')) {
                // Compatibilidad con campo legacy
                $updateData['store_category'] = $request->input('store_type');
            }

            // ✅ AGREGAR sale_price si es producto simple
            if ($newType === 'simple' && $request->has('sale_price')) {
                $updateData['sale_price'] = $request->input('sale_price');
            }

            $product->update($updateData);

            // 3. Manejo de Imágenes
            if ($request->hasFile('images')) {
                // ✅ Asegurar que el symlink existe antes de guardar imágenes
                $this->ensureTenantStorageLink();

                \Log::info('📸 [ProductController@update] Procesando imágenes', [
                    'product_id' => $product->id,
                    'num_files' => count($request->file('images'))
                ]);

                $currentMaxOrder = $product->images()->max('order') ?? 0;
                foreach ($request->file('images') as $index => $file) {
                    try {
                        \Log::info('🔍 [ProductController@update] Procesando archivo', [
                            'index' => $index,
                            'original_name' => $file->getClientOriginalName(),
                            'size' => $file->getSize(),
                            'mime' => $file->getMimeType(),
                            'is_valid' => $file->isValid(),
                            'error' => $file->getError()
                        ]);

                        $path = $file->store('products', 'public');

                        if (!$path) {
                            \Log::error('❌ [ProductController@update] store() retornó false/null');
                            continue;
                        }

                        // Para multi-tenancy: construir URL con tenant
                        $tenantId = tenant('id');
                        if ($tenantId) {
                            $url = "/storage/tenants/{$tenantId}/{$path}";
                        } else {
                            $url = Storage::url($path);
                        }

                        \Log::info('💾 [ProductController@update] Imagen guardada', [
                            'path' => $path,
                            'url' => $url,
                            'order' => $currentMaxOrder + $index + 1,
                            'exists_in_disk' => Storage::disk('public')->exists($path)
                        ]);

                        $product->images()->create([
                            'image_url' => $url,
                            'is_primary' => $currentMaxOrder === 0 && $index === 0,
                            'order' => $currentMaxOrder + $index + 1
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('❌ [ProductController@update] Error guardando imagen', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }
            } else {
                \Log::warning('⚠️ [ProductController@update] No se detectaron archivos de imagen en el request');
            }

            // 4. Manejar variantes y opciones
            if ($newType === 'simple') {
                // PRODUCTO SIMPLE

                // Eliminar opciones del producto
                $product->options()->delete();

                // ✅ Si se enviaron variantes (productos de moda), actualizar/crear la variante
                if ($request->has('variants') && is_array($request->variants) && count($request->variants) > 0) {
                    \Log::info('✅ [ProductController@update] Producto simple CON variante explícita');

                    // Eliminar variantes anteriores
                    $product->variants()->delete();

                    $variantData = $request->variants[0]; // Tomar la primera variante
                    $costPrice = $variantData['cost_price'] ?? $variantData['cost'] ?? 0;

                    \Log::info('🐛 [ProductController@update] Variant Data:', [
                        'sku' => $variantData['sku'] ?? null,
                        'price' => $variantData['price'] ?? null,
                        'cost_price' => $costPrice,
                        'stock' => $variantData['stock'] ?? 0
                    ]);

                    // Crear/actualizar variante
                    $variant = $product->variants()->create([
                        'sku' => $variantData['sku'] ?? $product->sku,
                        'price' => $variantData['price'],
                        'cost_price' => $costPrice,
                        'stock' => $variantData['stock'] ?? 0,
                        'active' => true,
                        'options_summary' => null
                    ]);

                    \Log::info('✅ [ProductController@update] Variant Updated:', [
                        'id' => $variant->id,
                        'cost_price' => $variant->cost_price
                    ]);

                    // Actualizar stock en bodega vinculado a la variante
                    DB::table('product_warehouse')
                        ->where('product_id', $product->id)
                        ->delete();

                    $warehouseId = $request->warehouse_id ?? Warehouse::first()->id ?? 1;
                    $product->warehouses()->attach($warehouseId, [
                        'stock' => $variantData['stock'] ?? 0,
                        'product_variant_id' => $variant->id
                    ]);

                    // Actualizar stock total
                    $product->update(['current_stock' => $variantData['stock'] ?? 0]);
                } else {
                    // LÓGICA LEGACY: Sin variantes explícitas, eliminar variantes
                    \Log::info('⚠️ [ProductController@update] Producto simple SIN variante (legacy)');

                    $product->variants()->delete();

                    // ✅ Obtener precio desde sale_price
                    $salePrice = $request->input('sale_price', 0);
                    $costPrice = $request->input('cost_price', 0);

                    \Log::info('💰 [ProductController@update] Producto SIMPLE (legacy):', [
                        'product_id' => $product->id,
                        'sale_price' => $salePrice,
                        'cost_price' => $costPrice,
                        'warehouse_stocks' => $request->warehouse_stocks
                    ]);

                    // ✅ Actualizar stock en warehouses SOLO si viene warehouse_stocks
                    if ($request->has('warehouse_stocks') && is_array($request->warehouse_stocks)) {
                    // Primero eliminar stocks anteriores (sin variant_id para productos simples)
                    DB::table('product_warehouse')
                        ->where('product_id', $product->id)
                        ->whereNull('product_variant_id')
                        ->delete();

                    $totalStock = 0;

                    foreach ($request->warehouse_stocks as $warehouseId => $stock) {
                        if ($stock > 0) {
                            DB::table('product_warehouse')->insert([
                                'product_id' => $product->id,
                                'warehouse_id' => $warehouseId,
                                'product_variant_id' => null, // NULL para productos simples
                                'stock' => $stock,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                            $totalStock += $stock;
                        }
                    }

                    // Actualizar stock total del producto
                    $product->update([
                        'current_stock' => $totalStock,
                        'sale_price' => $salePrice,
                        'image_url' => $request->input('image_url') // ✅ Guardar imagen
                    ]);

                    \Log::info('✅ [ProductController@update] Stock actualizado:', [
                        'total_stock' => $totalStock,
                        'warehouses' => count($request->warehouse_stocks)
                    ]);
                    } else {
                        // Fallback: Si no viene warehouse_stocks, actualizar precio e imagen
                        $product->update([
                            'sale_price' => $salePrice,
                            'image_url' => $request->input('image_url')
                        ]);
                        \Log::warning('⚠️ [ProductController@update] No se recibieron warehouse_stocks');
                    }
                }

            } else {
                // PRODUCTO VARIABLE - puede tener múltiples variantes con opciones

                // Guardar opciones del producto
                $optionValueMap = []; // Mapa: [NombreOpcion][Valor] => ID

                if ($request->has('options') && is_array($request->options)) {
                    // Eliminar opciones anteriores (Cascade eliminará valores)
                    $product->options()->delete();

                    // Crear nuevas opciones
                    foreach ($request->options as $optionData) {
                        $option = $product->options()->create(['name' => $optionData['name']]);

                        if (!empty($optionData['values'])) {
                            foreach ($optionData['values'] as $val) {
                                $valObj = $option->values()->create(['value' => $val]);
                                $optionValueMap[$optionData['name']][$val] = $valObj->id;
                            }
                        }
                    }
                }

                // Actualizar o crear variantes
                // ✅ Si no hay variantes o el array está vacío, crear variante por defecto
                $variants = $request->variants ?? [];
                if (empty($variants)) {
                    \Log::info('⚠️ [ProductController@update] No variants provided, creating default variant');
                    $variants = [[
                        'sku' => $request->sku ?? $product->sku ?? 'SKU-' . time(),
                        'price' => $request->sale_price ?? $request->price ?? $product->sale_price ?? 0,
                        'cost_price' => $request->cost_price ?? 0,
                        'stock' => $request->current_stock ?? $request->stock ?? $product->current_stock ?? 0,
                        'options' => []
                    ]];
                }

                if (is_array($variants)) {
                    // Eliminar variantes anteriores
                    $product->variants()->delete();

                    $warehouseId = $request->warehouse_id ?? Warehouse::first()->id ?? 1;

                    foreach ($variants as $variantData) {
                        // 🐛 DEBUG: Ver qué datos llegan para actualizar la variante
                        \Log::info('🐛 [ProductController@update] Variant Data Received:', [
                            'sku' => $variantData['sku'] ?? null,
                            'price' => $variantData['price'] ?? null,
                            'cost' => $variantData['cost'] ?? null,
                            'cost_price' => $variantData['cost_price'] ?? null,
                            'stock' => $variantData['stock'] ?? null,
                            'all_data' => $variantData
                        ]);

                        $costPrice = $variantData['cost'] ?? $variantData['cost_price'] ?? 0;

                        \Log::info('🐛 [ProductController@update] Cost Price Calculated:', [
                            'cost_price' => $costPrice
                        ]);

                        $variant = $product->variants()->create([
                            'sku' => $variantData['sku'],
                            'price' => $variantData['price'],
                            'cost_price' => $costPrice,
                            'stock' => $variantData['stock'] ?? 0,
                            'active' => $variantData['active'] ?? true,
                            'options_summary' => $variantData['options'] ?? null // ✅ Guardar resumen JSON
                        ]);

                        \Log::info('🐛 [ProductController@update] Variant Updated:', [
                            'id' => $variant->id,
                            'cost_price' => $variant->cost_price
                        ]);

                        // Vincular con Valores de Opción (Pivot)
                        if (isset($variantData['options'])) {
                            $options = $variantData['options'];

                            if (is_string($options)) {
                                $options = json_decode($options, true);
                            }

                            if (is_array($options)) {
                                // Formato: [{"name": "Talla", "value": "M"}]
                                if (isset($options[0]) && is_array($options[0]) && isset($options[0]['name'])) {
                                    foreach ($options as $opt) {
                                        $optName = $opt['name'];
                                        $optVal = $opt['value'];
                                        if (isset($optionValueMap[$optName][$optVal])) {
                                            $variant->optionValues()->attach($optionValueMap[$optName][$optVal]);
                                        }
                                    }
                                } else {
                                    // Formato objeto
                                    foreach ($options as $optName => $optVal) {
                                        if (isset($optionValueMap[$optName][$optVal])) {
                                            $variant->optionValues()->attach($optionValueMap[$optName][$optVal]);
                                        }
                                    }
                                }
                            }
                        }

                        // ✅ Guardar opciones de la variante en options_summary (JSON)
                        if (isset($variantData['options']) && is_array($variantData['options'])) {
                            $variant->update([
                                'options_summary' => json_encode($variantData['options'])
                            ]);
                        }

                        // Agregar stock en warehouse
                        DB::table('product_warehouse')->insert([
                            'product_id' => $product->id,
                            'product_variant_id' => $variant->id,
                            'warehouse_id' => $warehouseId,
                            'stock' => $variant->stock
                        ]);
                    }

                    // Recalcular stock total
                    $totalStock = DB::table('product_warehouse')
                        ->where('product_id', $product->id)
                        ->sum('stock');

                    $product->update(['current_stock' => $totalStock]);
                }
            }

            \Log::info('✅ Producto actualizado correctamente', [
                'product_id' => $product->id,
                'type' => $newType,
                'variants_count' => $product->variants()->count()
            ]);

            return response()->json([
                'success' => true,
                'data' => $product->load(['variants', 'options', 'images', 'warehouses']),
                'message' => 'Producto actualizado exitosamente'
            ]);
        });
    }

    public function destroy(Request $request, Product $product)
    {
        try {
            $productName = $product->name;

            // Guardar auditoría de eliminación
            $product->deleted_by = auth()->id();
            $product->deleted_reason = $request->input('reason', null);
            $product->save();

            // Desactivar variantes asociadas
            $product->variants()->update(['active' => false]);

            // Soft-delete: establece deleted_at, mantiene el registro en BD
            // Todas las FK (invoice_items, sale_items, inventory_movements) permanecen intactas
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => "Producto \"{$productName}\" eliminado exitosamente"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar productos eliminados (papelera)
     */
    public function trash()
    {
        try {
            $products = Product::onlyTrashed()
                ->with(['category'])
                ->orderBy('deleted_at', 'desc')
                ->get()
                ->map(function ($product) {
                    $deletedByUser = null;
                    if ($product->deleted_by) {
                        $deletedByUser = \App\Models\User::find($product->deleted_by);
                    }
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'barcode' => $product->barcode,
                        'category' => $product->category ? $product->category->name : null,
                        'sale_price' => $product->sale_price,
                        'cost_price' => $product->cost_price,
                        'current_stock' => $product->current_stock,
                        'image_url' => $product->image_url,
                        'deleted_at' => $product->deleted_at,
                        'deleted_by' => $product->deleted_by,
                        'deleted_by_name' => $deletedByUser ? $deletedByUser->name : 'Sistema',
                        'deleted_reason' => $product->deleted_reason,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $products,
                'total' => $products->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener productos eliminados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restaurar un producto eliminado
     */
    public function restore($id)
    {
        try {
            $product = Product::onlyTrashed()->findOrFail($id);
            $productName = $product->name;

            // Restaurar producto
            $product->restore();

            // Limpiar campos de auditoría de eliminación
            $product->deleted_by = null;
            $product->deleted_reason = null;
            $product->save();

            // Reactivar variantes
            $product->variants()->update(['active' => true]);

            return response()->json([
                'success' => true,
                'message' => "Producto \"{$productName}\" restaurado exitosamente"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar el producto: ' . $e->getMessage()
            ], 500);
        }
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
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'variant_id' => 'nullable|integer|exists:product_variants,id' // 👗 NUEVO: para productos fashion
        ]);

        $warehouseId = $request->warehouse_id;
        $variantId = $request->variant_id;

        // 👗 Si se proporciona variant_id, actualizar stock de variante específica
        if ($variantId) {
            $variant = \App\Models\ProductVariant::find($variantId);

            if (!$variant || $variant->product_id !== $product->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Variante no encontrada o no pertenece al producto'
                ], 404);
            }

            // Actualizar stock en product_variants
            $currentVariantStock = $variant->stock ?? 0;
            $newVariantStock = $currentVariantStock + $request->quantity;
            $variant->stock = max(0, $newVariantStock);
            $variant->save();

            // Actualizar stock en product_warehouse
            $warehouseId = $warehouseId ?? 1; // Warehouse por defecto
            $pivotExists = DB::table('product_warehouse')
                ->where('product_id', $product->id)
                ->where('product_variant_id', $variantId)
                ->where('warehouse_id', $warehouseId)
                ->exists();

            if ($pivotExists) {
                DB::table('product_warehouse')
                    ->where('product_id', $product->id)
                    ->where('product_variant_id', $variantId)
                    ->where('warehouse_id', $warehouseId)
                    ->increment('stock', $request->quantity);
            } else {
                DB::table('product_warehouse')->insert([
                    'product_id' => $product->id,
                    'product_variant_id' => $variantId,
                    'warehouse_id' => $warehouseId,
                    'stock' => max(0, $request->quantity),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Recalcular stock total del producto (suma de todas las variantes)
            $totalStock = DB::table('product_warehouse')
                ->where('product_id', $product->id)
                ->whereNotNull('product_variant_id')
                ->sum('stock');

            $product->current_stock = $totalStock;
            $product->save();

            // Crear movimiento de inventario
            \App\Models\InventoryMovement::create([
                'product_id' => $product->id,
                'type' => $request->type === 'purchase' || $request->type === 'adjustment' ? 'in' : 'out',
                'quantity' => $request->quantity,
                'previous_stock' => $currentVariantStock,
                'new_stock' => $variant->stock,
                'unit_cost' => $product->cost_price ?? 0,
                'reference' => $request->reference ?? 'Ajuste de variante',
                'notes' => "Variante ID: {$variantId}",
                'user_id' => auth()->id() ?? 1,
                'movement_date' => now()
            ]);

        } else {
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
        }

        return response()->json([
            'success' => true,
            'data' => $product->fresh(['variants']),
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
            'warehouse_id' => 'nullable|exists:warehouses,id', // Agregar validación de warehouse
        ]);

        try {
            $updatedCount = 0;
            $warehouseId = $request->warehouse_id ?? 1; // Warehouse por defecto si no se especifica
            $productIds = []; // Para recalcular stock de productos padre

            foreach ($request->variants as $variantData) {
                $variant = \App\Models\ProductVariant::find($variantData['id']);

                if ($variant) {
                    $updateData = [];

                    // 🛠️ FIX: Si se actualiza el stock, sincronizar con product_warehouse
                    if (isset($variantData['stock'])) {
                        $newStock = $variantData['stock'];
                        $updateData['stock'] = $newStock;

                        // Actualizar stock en product_warehouse
                        $pivotExists = DB::table('product_warehouse')
                            ->where('product_id', $variant->product_id)
                            ->where('product_variant_id', $variant->id)
                            ->where('warehouse_id', $warehouseId)
                            ->exists();

                        if ($pivotExists) {
                            DB::table('product_warehouse')
                                ->where('product_id', $variant->product_id)
                                ->where('product_variant_id', $variant->id)
                                ->where('warehouse_id', $warehouseId)
                                ->update(['stock' => $newStock]);
                        } else {
                            // Crear registro si no existe
                            DB::table('product_warehouse')->insert([
                                'product_id' => $variant->product_id,
                                'product_variant_id' => $variant->id,
                                'warehouse_id' => $warehouseId,
                                'stock' => $newStock,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }

                        // Marcar producto para recalcular stock total
                        $productIds[$variant->product_id] = true;
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

            // 🛠️ FIX: Recalcular current_stock de los productos padre afectados
            foreach (array_keys($productIds) as $productId) {
                $totalStock = DB::table('product_warehouse')
                    ->where('product_id', $productId)
                    ->sum('stock');

                DB::table('products')
                    ->where('id', $productId)
                    ->update(['current_stock' => $totalStock]);

                \Log::info('✅ Stock recalculado para producto variable:', [
                    'product_id' => $productId,
                    'new_total_stock' => $totalStock
                ]);
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

    /**
     * ✅ Eliminar imagen de producto - borra de BD y del sistema de archivos
     * DELETE /api/products/images/{imageId}
     */
    public function deleteImage($imageId)
    {
        try {
            $image = ProductImage::findOrFail($imageId);

            \Log::info('🗑️ [ProductController@deleteImage] Eliminando imagen', [
                'image_id' => $imageId,
                'image_url' => $image->image_url,
                'product_id' => $image->product_id
            ]);

            // Extraer la ruta del archivo desde la URL
            $imageUrl = $image->image_url;
            $filePath = null;

            // Si es una URL relativa tipo /storage/tenants/{tenant}/products/...
            if (preg_match('/\/storage\/tenants\/[^\/]+\/(.+)$/', $imageUrl, $matches)) {
                $filePath = $matches[1]; // products/xxxxx.jpg
            }
            // Si es una URL relativa tipo /storage/products/...
            elseif (preg_match('/\/storage\/(.+)$/', $imageUrl, $matches)) {
                $filePath = $matches[1]; // products/xxxxx.jpg
            }

            // Eliminar archivo físico si existe
            if ($filePath) {
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                    \Log::info('✅ [ProductController@deleteImage] Archivo físico eliminado', [
                        'path' => $filePath
                    ]);
                } else {
                    \Log::warning('⚠️ [ProductController@deleteImage] Archivo físico no encontrado', [
                        'path' => $filePath
                    ]);
                }
            }

            // Obtener el producto para actualizar la imagen principal si es necesario
            $product = Product::find($image->product_id);
            $wasPrimary = $image->is_primary;

            // Eliminar registro de la base de datos
            $image->delete();

            // Si era la imagen principal, actualizar la siguiente imagen como principal
            if ($wasPrimary && $product) {
                $nextImage = ProductImage::where('product_id', $product->id)
                    ->orderBy('order')
                    ->first();

                if ($nextImage) {
                    $nextImage->update(['is_primary' => true]);
                    $product->update(['image_url' => $nextImage->image_url]);
                } else {
                    // No hay más imágenes, limpiar image_url del producto
                    $product->update(['image_url' => null]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Imagen eliminada correctamente'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Imagen no encontrada'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('❌ [ProductController@deleteImage] Error', [
                'image_id' => $imageId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la imagen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar TODAS las imágenes de un producto
     * DELETE /products/{productId}/delete-image
     */
    public function deleteProductImage($productId)
    {
        try {
            $product = Product::findOrFail($productId);

            \Log::info('🗑️ [ProductController@deleteProductImage] Eliminando todas las imágenes del producto', [
                'product_id' => $productId,
                'product_name' => $product->name
            ]);

            // Obtener todas las imágenes del producto
            $images = ProductImage::where('product_id', $productId)->get();

            $deletedCount = 0;
            foreach ($images as $image) {
                // Extraer la ruta del archivo desde la URL
                $imageUrl = $image->image_url;
                $filePath = null;

                // Si es una URL relativa tipo /storage/tenants/{tenant}/products/...
                if (preg_match('/\/storage\/tenants\/[^\/]+\/(.+)$/', $imageUrl, $matches)) {
                    $filePath = $matches[1]; // products/xxxxx.jpg
                }
                // Si es una URL relativa tipo /storage/products/...
                elseif (preg_match('/\/storage\/(.+)$/', $imageUrl, $matches)) {
                    $filePath = $matches[1]; // products/xxxxx.jpg
                }

                // Eliminar archivo físico si existe
                if ($filePath) {
                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                        \Log::info('✅ [ProductController@deleteProductImage] Archivo físico eliminado', [
                            'path' => $filePath
                        ]);
                    }
                }

                // Eliminar registro de la base de datos
                $image->delete();
                $deletedCount++;
            }

            // Limpiar image_url del producto
            $product->update(['image_url' => null]);

            return response()->json([
                'success' => true,
                'message' => $deletedCount > 0
                    ? "Se eliminaron $deletedCount imagen(es) correctamente"
                    : 'No había imágenes para eliminar',
                'deleted_count' => $deletedCount
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('❌ [ProductController@deleteProductImage] Error', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar las imágenes: ' . $e->getMessage()
            ], 500);
        }
    }
}
