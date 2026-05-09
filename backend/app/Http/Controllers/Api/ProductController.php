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
     * Asegura que el symlink del tenant exista para servir archivos públicos.
     * Usa base_path() en lugar de storage_path() para evitar duplicación del
     * prefijo de tenant (storage_path() ya apunta a storage/tenant{id}/ en contexto tenant).
     */
    private function ensureTenantStorageLink()
    {
        try {
            $tenantId = tenant('id');
            if (!$tenantId) {
                return;
            }

            // IMPORTANTE: usar base_path() para evitar duplicación.
            // storage_path() en contexto tenant = storage/tenant{id}/
            // Entonces storage_path("tenant{id}/...") = storage/tenant{id}/tenant{id}/... (MAL)
            $tenantStoragePath = base_path("storage/tenant{$tenantId}/app/public");
            $symlinkPath = public_path("storage/tenants/{$tenantId}");

            // Si el symlink ya existe y apunta al target correcto, no hacer nada
            if (is_link($symlinkPath)) {
                $currentTarget = @readlink($symlinkPath);
                if ($currentTarget === $tenantStoragePath) {
                    return;
                }
                // Si apunta a un target incorrecto (path duplicado), eliminarlo
                @unlink($symlinkPath);
            } elseif (is_dir($symlinkPath)) {
                // Es un directorio real, no un symlink - reemplazar con symlink
                // Primero asegurar que el storage real existe
                if (!is_dir($tenantStoragePath)) {
                    @mkdir($tenantStoragePath, 0755, true);
                }
                // Mover contenido existente al storage real si hay archivos
                $files = @scandir($symlinkPath);
                if ($files) {
                    foreach ($files as $file) {
                        if ($file === '.' || $file === '..') continue;
                        $src = "{$symlinkPath}/{$file}";
                        $dst = "{$tenantStoragePath}/{$file}";
                        if (!file_exists($dst)) {
                            @rename($src, $dst);
                        }
                    }
                }
                // Eliminar directorio real y crear symlink
                @rmdir($symlinkPath);
                if (is_dir($symlinkPath)) {
                    // Si rmdir falló (directorio no vacío), usar rm -rf
                    @exec("rm -rf " . escapeshellarg($symlinkPath));
                }
            }

            if (!is_dir($tenantStoragePath)) {
                if (!@mkdir($tenantStoragePath, 0755, true)) {
                    \Log::warning("[Storage] No se pudo crear directorio: {$tenantStoragePath}");
                    return;
                }
            }

            // ✅ FIX: public/storage DEBE ser un symlink a storage/app/public, NO un directorio real.
            // Si es un directorio real, las imágenes de los tenants no serán accesibles vía web (404).
            $storagePublicDir = public_path('storage');
            if (!is_dir($storagePublicDir) && !is_link($storagePublicDir)) {
                // Crear como SYMLINK (igual que php artisan storage:link)
                $storageAppPublic = base_path('storage/app/public');
                if (!is_dir($storageAppPublic)) {
                    @mkdir($storageAppPublic, 0755, true);
                }
                if (!@symlink($storageAppPublic, $storagePublicDir)) {
                    // Si symlink falla (ej: permisos), crear directorio como fallback
                    \Log::warning("[Storage] No se pudo crear symlink public/storage -> storage/app/public. Creando directorio como fallback.");
                    @mkdir($storagePublicDir, 0755, true);
                }
            }

            $tenantsDir = public_path('storage/tenants');
            if (!is_dir($tenantsDir)) {
                @mkdir($tenantsDir, 0755, true);
            }

            if (!@symlink($tenantStoragePath, $symlinkPath)) {
                \Log::error("[Storage] No se pudo crear symlink de tenant: {$symlinkPath} -> {$tenantStoragePath}");
            }
        } catch (\Exception $e) {
            \Log::error("[Storage] Error en ensureTenantStorageLink: {$e->getMessage()}");
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
                  ->with(['optionValues.option', 'warehouses' => function($wq) {
                      $wq->select('warehouses.id', 'warehouses.name')->withPivot('stock');
                  }]);
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

            // 🏢 Ajustar stock según tipo de producto y warehouse
            $isVariable = $product->product_type === 'variable' || $product->type === 'variable';

            if ($warehouseId) {
                if ($isVariable) {
                    // Obtener stock PER-VARIANT para este warehouse desde product_warehouse
                    $warehouseVariantStock = DB::table('product_warehouse')
                        ->where('warehouse_id', $warehouseId)
                        ->where('product_id', $product->id)
                        ->whereNotNull('product_variant_id')
                        ->pluck('stock', 'product_variant_id'); // [variant_id => stock]

                    $totalVariantStock = $warehouseVariantStock->sum();
                    $product->current_stock = (int)$totalVariantStock;
                    $product->stock = (int)$totalVariantStock;

                    // Filtrar variantes: solo las que existen en este warehouse
                    // y sobreescribir el stock global con el stock del warehouse
                    if ($product->variants) {
                        $filtered = $product->variants->filter(function($variant) use ($warehouseVariantStock) {
                            return $warehouseVariantStock->has($variant->id);
                        })->each(function($variant) use ($warehouseVariantStock) {
                            $variant->stock = (int)$warehouseVariantStock->get($variant->id, 0);
                        })->values();
                        $product->setRelation('variants', $filtered);
                    }
                } else {
                    // Para productos simples, usar el stock directo del warehouse
                    if ($product->warehouses && $product->warehouses->isNotEmpty()) {
                        $warehouseStock = $product->warehouses->first()->pivot->stock;
                        $product->current_stock = (int)$warehouseStock;
                        $product->stock = (int)$warehouseStock;
                    }
                }
            } else {
                // Sin filtro de warehouse: recalcular current_stock para productos variables
                if ($isVariable && $product->variants && $product->variants->count() > 0) {
                    $totalVariantStock = $product->variants->sum('stock');
                    $product->current_stock = (int)$totalVariantStock;
                    $product->stock = (int)$totalVariantStock;
                }
            }

            // 💰 CALCULAR ventas e ingresos SOLO si se solicitan (evita N+1 queries)
            if ($includeSales) {
                $salesQuery = DB::table('invoice_items')
                    ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                    ->where('invoice_items.product_id', $product->id)
                    ->where('invoices.status', 'paid');

                // Filtrar ventas por warehouse (a través de cash_sessions)
                if ($warehouseId) {
                    $salesQuery->join('cash_sessions', 'invoices.cash_session_id', '=', 'cash_sessions.id')
                        ->where('cash_sessions.warehouse_id', $warehouseId);
                }

                $salesData = $salesQuery->selectRaw('
                        COALESCE(SUM(invoice_items.quantity), 0) as total_sold,
                        COALESCE(SUM(invoice_items.quantity * invoice_items.unit_price), 0) as total_revenue
                    ')
                    ->first();

                $product->total_sold = (int)($salesData->total_sold ?? 0);
                $product->total_revenue = (float)($salesData->total_revenue ?? 0);

                // Ventas por variante: combinar ventas con variant_id + históricas sin variant_id
                if ($isVariable && $product->variants && $product->variants->count() > 0) {
                    // 1) Ventas exactas por product_variant_id (nuevas)
                    $variantSalesQuery = DB::table('invoice_items')
                        ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                        ->where('invoice_items.product_id', $product->id)
                        ->whereNotNull('invoice_items.product_variant_id')
                        ->where('invoices.status', 'paid');

                    if ($warehouseId) {
                        $variantSalesQuery->join('cash_sessions', 'invoices.cash_session_id', '=', 'cash_sessions.id')
                            ->where('cash_sessions.warehouse_id', $warehouseId);
                    }

                    $variantSales = $variantSalesQuery->groupBy('invoice_items.product_variant_id')
                        ->selectRaw('
                            invoice_items.product_variant_id,
                            COALESCE(SUM(invoice_items.quantity), 0) as total_sold,
                            COALESCE(SUM(invoice_items.quantity * invoice_items.unit_price), 0) as total_revenue
                        ')
                        ->get()
                        ->keyBy('product_variant_id');

                    // 2) Ventas históricas SIN variant_id, agrupar por precio
                    $salesByPriceQuery = DB::table('invoice_items')
                        ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                        ->where('invoice_items.product_id', $product->id)
                        ->whereNull('invoice_items.product_variant_id')
                        ->where('invoices.status', 'paid');

                    if ($warehouseId) {
                        $salesByPriceQuery->join('cash_sessions', 'invoices.cash_session_id', '=', 'cash_sessions.id')
                            ->where('cash_sessions.warehouse_id', $warehouseId);
                    }

                    $salesByPrice = $salesByPriceQuery->groupBy('invoice_items.unit_price')
                        ->selectRaw('
                            invoice_items.unit_price,
                            COALESCE(SUM(invoice_items.quantity), 0) as total_sold,
                            COALESCE(SUM(invoice_items.quantity * invoice_items.unit_price), 0) as total_revenue
                        ')
                        ->get()
                        ->keyBy(function($item) {
                            return number_format((float)$item->unit_price, 2, '.', '');
                        });

                    // 3) Combinar ambas fuentes para cada variante
                    $product->variants->each(function($variant) use ($variantSales, $salesByPrice) {
                        $sold = 0;
                        $revenue = 0.0;

                        // Sumar ventas exactas por variant_id
                        $exact = $variantSales->get($variant->id);
                        if ($exact) {
                            $sold += (int)$exact->total_sold;
                            $revenue += (float)$exact->total_revenue;
                        }

                        // Sumar ventas históricas por precio
                        $variantPrice = number_format((float)($variant->price ?? 0), 2, '.', '');
                        $byPrice = $salesByPrice->get($variantPrice);
                        if ($byPrice) {
                            $sold += (int)$byPrice->total_sold;
                            $revenue += (float)$byPrice->total_revenue;
                        }

                        $variant->total_sold = $sold;
                        $variant->total_revenue = $revenue;
                    });
                }
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
                    $isVariable = $product->product_type === 'variable';

                    if ($isVariable) {
                        // Para productos variables: sumar stock de variantes en este warehouse
                        $localStock = (int) \Illuminate\Support\Facades\DB::table('product_warehouse')
                            ->where('warehouse_id', $warehouseId)
                            ->where('product_id', $product->id)
                            ->whereNotNull('product_variant_id')
                            ->sum('stock');
                    } else {
                        $currentWarehouse = $product->warehouses->firstWhere('id', $warehouseId);
                        if ($currentWarehouse) {
                            $localStock = (int) $currentWarehouse->pivot->stock;
                        }
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

                    // 🏢 Si hay warehouse, filtrar variantes y ajustar stock per-warehouse
                    if ($warehouseId && $searchScope !== 'global') {
                        $warehouseVariantStock = \Illuminate\Support\Facades\DB::table('product_warehouse')
                            ->where('warehouse_id', $warehouseId)
                            ->where('product_id', $product->id)
                            ->whereNotNull('product_variant_id')
                            ->pluck('stock', 'product_variant_id');

                        $filtered = $product->variants->filter(function($variant) use ($warehouseVariantStock) {
                            return $warehouseVariantStock->has($variant->id);
                        })->each(function($variant) use ($warehouseVariantStock) {
                            $variant->stock = (int)$warehouseVariantStock->get($variant->id, 0);
                        })->values();
                        $product->setRelation('variants', $filtered);
                    }
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
            // Obtener el warehouse real del tenant (nunca hardcodear 1)
            $defaultWarehouse = \App\Models\Warehouse::orderBy('id')->first();
            $warehouseId = $request->warehouse_id ?? ($defaultWarehouse ? $defaultWarehouse->id : null);

            if ($request->product_type === 'simple') {
                // --- LÓGICA SIMPLE ---

                // ✅ Si se enviaron variantes (productos de moda), crear la variante
                if ($request->has('variants') && is_array($request->variants) && count($request->variants) > 0) {
                    $variantData = $request->variants[0]; // Tomar la primera variante
                    $costPrice = $variantData['cost_price'] ?? $variantData['cost'] ?? 0;

                    // Crear variante
                    $variant = $product->variants()->create([
                        'sku' => $variantData['sku'] ?? $product->sku,
                        'price' => $variantData['price'],
                        'cost_price' => $costPrice,
                        'stock' => $variantData['stock'] ?? 0,
                        'active' => true,
                        'options_summary' => null
                    ]);

                    // Guardar stock en bodega vinculado a la variante
                    if ($warehouseId) {
                        $product->warehouses()->attach($warehouseId, [
                            'stock' => $variantData['stock'] ?? 0,
                            'product_variant_id' => $variant->id
                        ]);
                    }
                } else {
                    $stock = $request->current_stock ?? 0;

                    // Guardar en product_warehouse
                    if ($warehouseId) {
                        $product->warehouses()->attach($warehouseId, [
                            'stock' => $stock,
                            'product_variant_id' => null
                        ]);
                    }
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
                        $costPrice = $variantData['cost'] ?? $variantData['cost_price'] ?? 0;

                        // Crear Variante
                        $variant = $product->variants()->create([
                            'sku' => $variantData['sku'],
                            'price' => $variantData['price'],
                            'cost_price' => $costPrice,
                            'stock' => $variantData['stock'] ?? 0,
                            'options_summary' => $variantData['options'] ?? null // JSON
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
                        if ($warehouseId) {
                            $product->warehouses()->attach($warehouseId, [
                                'product_variant_id' => $variant->id,
                                'stock' => $variantData['stock'] ?? 0
                            ]);
                        }
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
            'options.values',
            'variants',
            'images'
        ]);

        // 🏢 Si se pasa warehouse_id, filtrar variantes y ajustar stock per-warehouse
        $warehouseId = request()->query('warehouse_id');

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
            });

            // Filtrar y ajustar stock per-warehouse si se solicita
            if ($warehouseId) {
                $warehouseVariantStock = DB::table('product_warehouse')
                    ->where('warehouse_id', $warehouseId)
                    ->where('product_id', $product->id)
                    ->whereNotNull('product_variant_id')
                    ->pluck('stock', 'product_variant_id');

                $filtered = $product->variants->filter(function($variant) use ($warehouseVariantStock) {
                    return $warehouseVariantStock->has($variant->id);
                })->each(function($variant) use ($warehouseVariantStock) {
                    $variant->stock = (int)$warehouseVariantStock->get($variant->id, 0);
                })->values();
                $product->setRelation('variants', $filtered);

                // Ajustar stock total del producto al warehouse
                $product->current_stock = $warehouseVariantStock->sum();
            }
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
                $this->ensureTenantStorageLink();

                $currentMaxOrder = $product->images()->max('order') ?? 0;
                foreach ($request->file('images') as $index => $file) {
                    try {
                        $path = $file->store('products', 'public');

                        if (!$path) {
                            continue;
                        }

                        // Para multi-tenancy: construir URL con tenant
                        $tenantId = tenant('id');
                        if ($tenantId) {
                            $url = "/storage/tenants/{$tenantId}/{$path}";
                        } else {
                            $url = Storage::url($path);
                        }

                        $product->images()->create([
                            'image_url' => $url,
                            'is_primary' => $currentMaxOrder === 0 && $index === 0,
                            'order' => $currentMaxOrder + $index + 1
                        ]);

                        // Actualizar la URL de la imagen principal en el producto
                        if ($index === 0) {
                            $product->update(['image_url' => $url]);
                        }
                    } catch (\Exception $e) {
                        \Log::error('[ProductController@update] Error guardando imagen', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }
            } else {
                // No files detected - this is normal for non-image updates
            }

            // 4. Manejar variantes y opciones
            if ($newType === 'simple') {
                // PRODUCTO SIMPLE

                // Eliminar opciones del producto
                $product->options()->delete();

                // ✅ Si se enviaron variantes (productos de moda), actualizar/crear la variante
                if ($request->has('variants') && is_array($request->variants) && count($request->variants) > 0) {
                    $product->variants()->delete();

                    $variantData = $request->variants[0];
                    $costPrice = $variantData['cost_price'] ?? $variantData['cost'] ?? 0;

                    // Crear/actualizar variante
                    $variant = $product->variants()->create([
                        'sku' => $variantData['sku'] ?? $product->sku,
                        'price' => $variantData['price'],
                        'cost_price' => $costPrice,
                        'stock' => $variantData['stock'] ?? 0,
                        'active' => true,
                        'options_summary' => null
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
                    $product->variants()->delete();

                    $salePrice = $request->input('sale_price', 0);
                    $costPrice = $request->input('cost_price', 0);

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

                    // Construir array de actualización final para el producto simple
                    $finalUpdate = [
                        'current_stock' => $totalStock,
                        'sale_price' => $salePrice
                    ];
                    // Solo actualizar la imagen si viene explícitamente como texto (ej: URL externa o limpieza)
                    // y no si acaba de subirse un archivo (lo cual ya actualizó la imagen en el paso 3)
                    if ($request->has('image_url')) {
                        $finalUpdate['image_url'] = $request->input('image_url');
                    }
                    
                    // Actualizar stock total del producto
                    $product->update($finalUpdate);
                    } else {
                        // Fallback: Si no viene warehouse_stocks, actualizar precio e imagen
                        $fallbackUpdate = ['sale_price' => $salePrice];
                        if ($request->has('image_url')) {
                            $fallbackUpdate['image_url'] = $request->input('image_url');
                        }
                        $product->update($fallbackUpdate);
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
                        $costPrice = $variantData['cost'] ?? $variantData['cost_price'] ?? 0;

                        $variant = $product->variants()->create([
                            'sku' => $variantData['sku'],
                            'price' => $variantData['price'],
                            'cost_price' => $costPrice,
                            'stock' => $variantData['stock'] ?? 0,
                            'active' => $variantData['active'] ?? true,
                            'options_summary' => $variantData['options'] ?? null // ✅ Guardar resumen JSON
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
        $simpleProducts = Product::lowStock()
            ->where(function($q) { $q->whereNull('product_type')->orWhere('product_type', 'simple'); })
            ->with(['category', 'supplier'])
            ->get();

        $variables = \App\Models\ProductVariant::with(['product.category', 'product.supplier'])
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->where('products.manage_stock', true)
            ->where('products.active', true)
            ->where('product_variants.active', true)
            ->whereColumn('product_variants.stock', '<=', 'products.min_stock')
            ->select('product_variants.*', 'products.name as parent_name', 'products.image_url as parent_image', 'products.min_stock as parent_min', 'products.category_id as parent_category')
            ->get()
            ->map(function($variant) {
                $variantName = $variant->parent_name;
                $summary = is_string($variant->options_summary) ? json_decode($variant->options_summary, true) : $variant->options_summary;

                if (is_array($summary) && count($summary) > 0) {
                    $optsStr = collect($summary)->map(function($o) { return $o['value'] ?? ''; })->filter()->join(', ');
                    $variantName .= ' (' . $optsStr . ')';
                } elseif ($variant->sku) {
                    $variantName .= ' (' . $variant->sku . ')';
                } else {
                    $variantName .= ' (Variante)';
                }

                return [
                    'id' => 'v-' . $variant->id,
                    'name' => $variantName,
                    'sku' => $variant->sku,
                    'image_url' => $variant->parent_image,
                    'current_stock' => $variant->stock,
                    'min_stock' => $variant->parent_min,
                    'category' => $variant->product ? $variant->product->category : null,
                    'supplier' => $variant->product ? $variant->product->supplier : null,
                    'product_type' => 'variant',
                    'original_product_id' => $variant->product_id
                ];
            });

        $products = collect($simpleProducts)->concat($variables)->sortBy('current_stock')->values();

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

            $warehouseId = $warehouseId ?? 1; // Warehouse por defecto

            // Obtener stock ACTUAL en el warehouse específico (no global)
            $currentWarehouseStock = (int) DB::table('product_warehouse')
                ->where('product_id', $product->id)
                ->where('product_variant_id', $variantId)
                ->where('warehouse_id', $warehouseId)
                ->value('stock') ?? 0;

            $quantity = $request->quantity; // Diferencia enviada por el frontend

            // Actualizar stock en product_warehouse para este warehouse
            $pivotExists = DB::table('product_warehouse')
                ->where('product_id', $product->id)
                ->where('product_variant_id', $variantId)
                ->where('warehouse_id', $warehouseId)
                ->exists();

            if ($pivotExists) {
                $newWarehouseStock = max(0, $currentWarehouseStock + $quantity);
                DB::table('product_warehouse')
                    ->where('product_id', $product->id)
                    ->where('product_variant_id', $variantId)
                    ->where('warehouse_id', $warehouseId)
                    ->update(['stock' => $newWarehouseStock, 'updated_at' => now()]);
            } else {
                $newWarehouseStock = max(0, $quantity);
                DB::table('product_warehouse')->insert([
                    'product_id' => $product->id,
                    'product_variant_id' => $variantId,
                    'warehouse_id' => $warehouseId,
                    'stock' => $newWarehouseStock,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Recalcular stock GLOBAL de la variante (suma de TODOS los warehouses)
            $globalVariantStock = (int) DB::table('product_warehouse')
                ->where('product_id', $product->id)
                ->where('product_variant_id', $variantId)
                ->sum('stock');

            $variant->stock = $globalVariantStock;
            $variant->save();

            // Recalcular stock total del producto (suma de todas las variantes en TODOS los warehouses)
            $totalStock = DB::table('product_warehouse')
                ->where('product_id', $product->id)
                ->whereNotNull('product_variant_id')
                ->sum('stock');

            $product->current_stock = $totalStock;
            $product->save();

            // Crear movimiento de inventario
            \App\Models\InventoryMovement::create([
                'product_id' => $product->id,
                'type' => $quantity >= 0 ? 'in' : 'out',
                'quantity' => $quantity,
                'previous_stock' => $currentWarehouseStock,
                'new_stock' => $newWarehouseStock ?? max(0, $quantity),
                'unit_cost' => $product->cost_price ?? 0,
                'reference' => $request->reference ?? 'Ajuste de variante',
                'notes' => "Variante ID: {$variantId}, Bodega ID: {$warehouseId}",
                'user_id' => auth()->id() ?? 1,
                'warehouse_id' => $warehouseId,
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
            \Log::error('[ProductController@deleteImage] Error', [
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
            \Log::error('[ProductController@deleteProductImage] Error', [
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
