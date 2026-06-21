<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Category::with(['products' => function($query) {
                $query->where('active', true)->count();
            }])
            ->withCount(['products' => function($query) {
                $query->where('active', true);
            }])
            // Removed filter - show ALL categories (active and inactive)
            ->orderBy('name')
            ->get();

            // Calculate revenue for each category from paid invoices
            foreach ($categories as $category) {
                $revenue = DB::table('invoice_items')
                    ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                    ->join('products', 'invoice_items.product_id', '=', 'products.id')
                    ->where('products.category_id', $category->id)
                    ->where('invoices.status', 'paid')
                    ->sum(DB::raw('invoice_items.quantity * invoice_items.unit_price'));
                
                $category->revenue = round($revenue, 2);
            }

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Categorías obtenidas exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las categorías',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint optimizado para POS - solo campos necesarios
     */
    public function forPos()
    {
        try {
            $categories = Category::select(['id', 'name', 'color', 'icon', 'image_url'])
                ->where('active', true)
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Categorías para POS obtenidas exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las categorías para POS',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:categories',
                'description' => 'nullable|string|max:500',
                'icon' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:7',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->except('image');

            if ($request->hasFile('image')) {
                $tenantId = tenant('id') ?? 'default';
                $file = $request->file('image');
                $filename = 'cat_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
                $path = Storage::disk('s3')->putFileAs("tenants/{$tenantId}/categories", $file, $filename, 'public');
                $data['image_url'] = Storage::disk('s3')->url($path);
            }

            $category = Category::create($data);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Categoría creada exitosamente'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la categoría',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $category = Category::with(['products' => function($query) {
                $query->where('active', true);
            }])
            ->withCount(['products' => function($query) {
                $query->where('active', true);
            }])
            ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Categoría obtenida exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $category = Category::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
                'description' => 'nullable|string|max:500',
                'icon' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:7',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->has('active') && $request->active === false && $category->active === true) {
                $category->products()->where('active', true)->update([
                    'active' => false,
                    'deactivated_by_category' => true
                ]);
            }
            
            if ($request->has('active') && $request->active === true && $category->active === false) {
                $category->products()->where('deactivated_by_category', true)->update([
                    'active' => true,
                    'deactivated_by_category' => false
                ]);
            }

            $updateData = $request->except('image');

            if ($request->hasFile('image')) {
                if ($category->image_url) {
                    $parsedUrl = parse_url($category->image_url, PHP_URL_PATH);
                    $pathParts = explode('/storage/', $parsedUrl);
                    $storagePath = count($pathParts) > 1 ? $pathParts[1] : ltrim($parsedUrl, '/');
                    
                    if (Storage::disk('s3')->exists($storagePath)) {
                        Storage::disk('s3')->delete($storagePath);
                    }
                }

                $tenantId = tenant('id') ?? 'default';
                $file = $request->file('image');
                $filename = 'cat_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
                $path = Storage::disk('s3')->putFileAs("tenants/{$tenantId}/categories", $file, $filename, 'public');
                $updateData['image_url'] = Storage::disk('s3')->url($path);
            }

            $category->update($updateData);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Categoría actualizada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la categoría',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $category = Category::findOrFail($id);

            // Verificar si hay productos asociados
            if ($category->products()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la categoría porque tiene productos asociados'
                ], 422);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la categoría',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
