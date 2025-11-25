<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier']);

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
        $products = Product::select([
                'id', 'name', 'sku', 'barcode', 'sale_price as price',
                'current_stock as stock', 'category_id', 'image_url as image',
                'active', 'manage_stock'
            ])
            ->with(['category:id,name,color'])
            ->where('active', true)
            ->where('current_stock', '>', 0) // Solo productos con stock
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                // Añadir el nombre de la categoría directamente al producto
                $product->category_name = $product->category ? $product->category->name : 'Sin categoría';
                return $product;
            });

        return response()->json([
            'success' => true,
            'data' => $products,
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
            'min_stock' => 'required|integer|min:0'
        ]);

        $product = Product::create($request->all());
        $product->load(['category', 'supplier']);

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
        
        $product->update($request->all());
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
