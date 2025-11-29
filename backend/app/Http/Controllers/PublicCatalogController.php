<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\OnlineOrder;
use App\Models\OnlineOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PublicCatalogController extends Controller
{
    /**
     * Muestra el catálogo público de productos
     */
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->availableForOnline();

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
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->public_description ?? $product->description,
                'price' => (float) $product->sale_price,
                'image' => $product->public_image ?? $product->image_url,
                'stock' => $product->current_stock,
                'category' => $product->category ? $product->category->name : null,
                'category_id' => $product->category_id,
                'sku' => $product->sku,
                'unit' => $product->unit,
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
        $categories = DB::table('categories')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->where('products.is_public', true)
            ->where('products.active', true)
            ->where('products.current_stock', '>', 0)
            ->where('categories.active', true)
            ->select('categories.id', 'categories.name')
            ->distinct()
            ->orderBy('categories.name')
            ->get();

        return response()->json([
            'success' => true,
            'categories' => $categories,
        ]);
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
