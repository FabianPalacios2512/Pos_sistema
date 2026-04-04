<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\OnlineOrder;
use App\Models\OnlineOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class PublicCatalogController extends Controller
{
    /**
     * Obtiene la configuración cacheada (1 hora)
     */
    private function getCachedConfig()
    {
        $tenantId = tenant('id');
        return Cache::remember("web_catalog_config_{$tenantId}", 3600, function() use ($tenantId) {
            return DB::table('web_catalog_configs')
                ->where('tenant_id', $tenantId)
                ->first();
        });
    }

    /**
     * Muestra el catálogo público de productos
     */
    public function index(Request $request)
    {
        // Obtener configuración cacheada
        $config = $this->getCachedConfig();

        // ✅ VALIDACIÓN CRÍTICA: Si store_active está en false, no mostrar nada
        if (!$config || !$config->store_active) {
            return response()->json([
                'success' => false,
                'message' => 'Catálogo no disponible',
                'products' => []
            ]);
        }

        $query = Product::with(['category', 'images', 'options.values', 'variants.optionValues'])
            ->availableForOnline();

        // ⚠️ IMPORTANTE: Si hay configuración de categorías visibles, respetarla
        if ($config && $config->visible_categories) {
            $visibleCategories = json_decode($config->visible_categories, true);
            
            // Si el array está vacío o es null, NO mostrar ningún producto
            if (empty($visibleCategories)) {
                // Retornar array vacío porque no hay categorías seleccionadas
                return response()->json([]);
            }
            
            // Si hay categorías configuradas, filtrar solo esas
            $query->whereIn('category_id', $visibleCategories);
        }
        // Si no existe la configuración o visible_categories es null, mostrar todo (por defecto)

        // Ocultar productos sin stock si está configurado
        if ($config && $config->hide_out_of_stock) {
            $query->where('current_stock', '>', 0);
        }

        // Búsqueda
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('public_description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filtro por categoría
        if ($request->has('category_id') && $request->category_id !== '') {
            $query->where('category_id', $request->category_id);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');

        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('sale_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('sale_price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $products = $query->get()->map(function($product) {
            $mainImage = $product->public_image ?? $product->image_url;

            // Solo agregar URL base si NO es base64 ni URL absoluta
            if ($mainImage && !str_starts_with($mainImage, 'http') && !str_starts_with($mainImage, 'data:')) {
                $mainImage = url($mainImage);
            }

            $images = $product->images->sortBy('order')->map(function($img) {
                $url = $img->image_url;
                // Solo agregar URL base si NO es base64 ni URL absoluta
                if ($url && !str_starts_with($url, 'http') && !str_starts_with($url, 'data:')) {
                    return url($url);
                }
                return $url;
            })->values()->toArray();

            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->public_description ?? $product->description,
                'price' => (float) $product->sale_price,
                'image' => $mainImage,
                'images' => $images,
                'stock' => $product->current_stock,
                'category' => $product->category ? $product->category->name : null,
                'category_id' => $product->category_id,
                'sku' => $product->sku,
                'unit' => $product->unit,
                'measurement_unit' => $product->measurement_unit, // 📏 Para productos por peso
                'allow_decimal' => $product->allow_decimal, // 📏 Para decimales
                'type' => $product->product_type,
                'options' => $product->options->map(function($opt) {
                    return [
                        'id' => $opt->id,
                        'name' => $opt->name,
                        'values' => $opt->values->map(function($val) {
                            return [
                                'id' => $val->id,
                                'value' => $val->value
                            ];
                        })
                    ];
                }),
                'variants' => $product->variants->map(function($var) use ($product) {
                    // Construir options_summary para el modal de variantes
                    $optionsSummary = $var->optionValues->map(function($ov) use ($product) {
                        // Buscar el nombre de la opción
                        $option = $product->options->firstWhere('id', $ov->product_option_id);
                        return [
                            'name' => $option ? $option->name : 'Opción',
                            'value' => $ov->value
                        ];
                    })->toArray();

                    return [
                        'id' => $var->id,
                        'sku' => $var->sku,
                        'price' => (float) $var->price,
                        'stock' => $var->stock,
                        'options_summary' => $optionsSummary, // Para el modal de variantes
                        'option_values' => $var->optionValues->map(function($ov) {
                             return [
                                 'option_id' => $ov->product_option_id,
                                 'value_id' => $ov->id,
                                 'value' => $ov->value
                             ];
                        })
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    /**
     * Obtiene las categorías disponibles en el catálogo
     */
    public function categories()
    {
        // Obtener configuración cacheada
        $config = $this->getCachedConfig();

        // ✅ VALIDACIÓN CRÍTICA: Si store_active está en false, no mostrar categorías
        if (!$config || !$config->store_active) {
            return response()->json([
                'success' => false,
                'message' => 'Catálogo no disponible',
                'categories' => []
            ]);
        }

        $query = DB::table('categories')
            ->where('categories.active', true)
            ->select('categories.id', 'categories.name')
            ->orderBy('categories.name');

        // Si hay categorías visibles configuradas, filtrar solo esas
        if ($config && $config->visible_categories) {
            $visibleCategories = json_decode($config->visible_categories, true);
            if (!empty($visibleCategories)) {
                $query->whereIn('categories.id', $visibleCategories);
            }
        }

        $categories = $query->get();

        return response()->json([
            'success' => true,
            'categories' => $categories,
        ]);
    }

    /**
     * Obtiene la configuración pública del catálogo
     */
    public function getPublicConfig()
    {
        try {
            // Obtener configuración cacheada
            $config = $this->getCachedConfig();

            if (!$config) {
                // Retornar configuración por defecto (speed-market es segura para todas las tiendas)
                return response()->json([
                    'success' => true,
                    'data' => [
                        'store_active' => true,
                        'template' => 'speed-market', // Plantilla B - Compatible con todas las tiendas
                        'primary_color' => '#10B981',
                        'logo_url' => '',
                        'banner_url' => '',
                        'whatsapp_number' => '',
                        'currency_symbol' => '$',
                        'delivery_cost' => 0,
                        'minimum_order' => 0,
                        'store_name' => DB::table('system_settings')->value('company_name')
                            ?? \Stancl\Tenancy\Facades\Tenancy::tenant()?->business_name
                            ?? tenant('name')
                            ?? 'Mi Tienda'
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'store_active' => $config->store_active,
                    'template' => $config->template,
                    'primary_color' => $config->primary_color,
                    'logo_url' => $config->logo_url,
                    'banner_url' => $config->banner_url,
                    'whatsapp_number' => $config->whatsapp_number,
                    'visible_categories' => json_decode($config->visible_categories ?? '[]', true),
                    'show_prices' => $config->show_prices,
                    'hide_out_of_stock' => $config->hide_out_of_stock ?? false,
                    'currency_symbol' => '$',
                    'delivery_cost' => $config->delivery_cost,
                    'minimum_order' => $config->minimum_order,
                    'custom_message' => $config->custom_message ?? 'Hola, quiero hacer el siguiente pedido:',
                    'store_name' => DB::table('system_settings')->value('company_name')
                        ?? \Stancl\Tenancy\Facades\Tenancy::tenant()?->business_name
                        ?? tenant('name')
                        ?? 'Mi Tienda'
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting public catalog config: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar configuración'
            ], 500);
        }
    }

    /**
     * Crea un nuevo pedido desde el catálogo público
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_document' => 'required|string|min:6|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string',
            'delivery_type' => 'required|in:pickup,delivery',
            'note' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'customer_name.required' => 'El nombre es obligatorio',
            'customer_phone.required' => 'El teléfono es obligatorio',
            'customer_document.required' => 'El documento es obligatorio',
            'customer_document.min' => 'El documento debe tener al menos 6 caracteres',
            'delivery_type.required' => 'Debe seleccionar un método de entrega',
            'items.required' => 'Debe agregar al menos un producto',
            'items.*.product_id.exists' => 'Producto no válido',
            'items.*.quantity.min' => 'La cantidad debe ser al menos 1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Validar stock disponible para cada producto
            $stockErrors = [];
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);

                if (!$product) {
                    $stockErrors[] = "Producto no encontrado";
                    continue;
                }

                if (!$product->is_public || !$product->active) {
                    $stockErrors[] = "{$product->name} no está disponible";
                    continue;
                }

                if ($product->manage_stock && $product->current_stock < $item['quantity']) {
                    $stockErrors[] = "{$product->name} - Stock insuficiente (Disponible: {$product->current_stock})";
                }
            }

            if (!empty($stockErrors)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Algunos productos no tienen stock suficiente',
                    'errors' => $stockErrors,
                ], 422);
            }

            // Actualizar datos del cliente si existe en la BD
            $existingCustomer = DB::table('customers')
                ->where('document_number', $request->customer_document)
                ->where('active', true)
                ->first();

            if ($existingCustomer) {
                // Actualizar datos del cliente existente
                DB::table('customers')
                    ->where('id', $existingCustomer->id)
                    ->update([
                        'name' => $request->customer_name,
                        'phone' => $request->customer_phone,
                        'email' => $request->customer_email ?: $existingCustomer->email,
                        'address' => $request->customer_address ?: $existingCustomer->address,
                        'updated_at' => now(),
                    ]);
            }

            // Crear el pedido
            $order = OnlineOrder::create([
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_document' => $request->customer_document,
                'customer_address' => $request->customer_address,
                'delivery_type' => $request->delivery_type,
                'note' => $request->note,
                'status' => 'pending',
                'total' => 0,
            ]);

            // Crear los items del pedido
            $subtotal = 0;
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);

                $orderItem = OnlineOrderItem::create([
                    'online_order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->sale_price,
                    'subtotal' => $product->sale_price * $item['quantity'],
                    'special_instructions' => $item['special_instructions'] ?? null,
                ]);

                $subtotal += $orderItem->subtotal;
            }

            // Actualizar el total del pedido
            $order->update([
                'subtotal' => $subtotal,
                'total' => $subtotal,
            ]);

            DB::commit();

            // Generar enlace de WhatsApp (si existe configuración)
            $businessPhone = '573000000000'; // TODO: Obtener del SystemSettings
            $whatsappLink = $order->getWhatsAppLink($businessPhone);

            return response()->json([
                'success' => true,
                'message' => 'Pedido creado exitosamente',
                'order' => [
                    'uuid' => $order->uuid,
                    'order_number' => $order->order_number,
                    'total' => (float) $order->total,
                    'whatsapp_link' => $whatsappLink,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al crear el pedido',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtiene un pedido por UUID
     */
    public function show($uuid)
    {
        $order = OnlineOrder::with('items.product')
            ->where('uuid', $uuid)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido no encontrado',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'order' => [
                'uuid' => $order->uuid,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'customer_address' => $order->customer_address,
                'delivery_type' => $order->delivery_type,
                'status' => $order->status,
                'total' => (float) $order->total,
                'note' => $order->note,
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'items' => $order->items->map(function($item) {
                    return [
                        'product_name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'subtotal' => (float) $item->subtotal,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Busca un pedido por su código (order_number)
     * Este endpoint es para el cajero en el POS
     */
    public function findByCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Código de pedido requerido',
            ], 422);
        }

        // Buscar por order_number completo (PED-XXX) o solo por número (XXX)
        $code = $request->code;

        $order = OnlineOrder::with(['items.product'])
            ->where(function($query) use ($code) {
                $query->where('order_number', $code)
                      ->orWhere('order_number', 'like', "%{$code}");
            })
            ->where('status', '!=', 'cancelled')
            ->latest()
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido no encontrado o ya fue cancelado',
            ], 404);
        }

        // Verificar disponibilidad de stock antes de cargar
        $stockIssues = [];
        foreach ($order->items as $item) {
            if ($item->product) {
                if ($item->product->manage_stock && $item->product->current_stock < $item->quantity) {
                    $stockIssues[] = [
                        'product' => $item->product_name,
                        'requested' => $item->quantity,
                        'available' => $item->product->current_stock,
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'customer_document' => $order->customer_document,
                'customer_address' => $order->customer_address,
                'delivery_type' => $order->delivery_type,
                'status' => $order->status,
                'note' => $order->note,
                'subtotal' => (float) $order->subtotal,
                'total' => (float) $order->total,
                'items' => $order->items->map(function($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'product_sku' => $item->product_sku,
                        'quantity' => $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'subtotal' => (float) $item->subtotal,
                        'special_instructions' => $item->special_instructions,
                    ];
                }),
            ],
            'stock_issues' => $stockIssues,
        ]);
    }

    /**
     * Busca un cliente por documento (optimizado con índice)
     * Este endpoint es público para el catálogo web
     */
    public function findCustomerByDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Documento inválido',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar cliente por documento_number (tiene índice en la BD)
        // Solo devolver datos básicos necesarios para el formulario
        $customer = DB::table('customers')
            ->select('name', 'phone', 'email', 'address', 'document_number')
            ->where('document_number', $request->document)
            ->where('active', true)
            ->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'found' => false,
                'message' => 'Cliente no encontrado'
            ]);
        }

        return response()->json([
            'success' => true,
            'found' => true,
            'customer' => [
                'name' => $customer->name,
                'phone' => $customer->phone,
                'email' => $customer->email ?? '',
                'address' => $customer->address ?? '',
            ]
        ]);
    }

    /**
     * Marca un pedido como completado
     */
    public function markComplete(Request $request, $id)
    {
        $order = OnlineOrder::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido no encontrado',
            ], 404);
        }

        $order->complete();

        return response()->json([
            'success' => true,
            'message' => 'Pedido marcado como completado',
        ]);
    }
}
