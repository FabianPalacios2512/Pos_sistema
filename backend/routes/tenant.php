<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->prefix('api')->group(function () {
    // Aquí cargaremos las rutas de la API del POS
    require __DIR__ . '/tenant_api.php';
});

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    });
});

// Rutas públicas del catálogo (sin autenticación)
Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->prefix('api/public')->group(function () {
    Route::get('/catalog', [App\Http\Controllers\PublicCatalogController::class, 'index']);
    Route::get('/catalog/categories', [App\Http\Controllers\PublicCatalogController::class, 'categories']);
    Route::get('/catalog/config', [App\Http\Controllers\PublicCatalogController::class, 'getPublicConfig']);
    Route::post('/orders', [App\Http\Controllers\PublicCatalogController::class, 'store']);
    Route::get('/orders/{uuid}', [App\Http\Controllers\PublicCatalogController::class, 'show']);

    // Ruta para buscar pedido por código (para el POS)
    Route::post('/orders/find-by-code', [App\Http\Controllers\PublicCatalogController::class, 'findByCode']);
    
    // 🔍 TEMPORAL - Debug endpoint para diagnosticar productos
    Route::get('/debug/products', function () {
        $data = [
            'tenant_id' => tenant('id'),
            'total_products' => \App\Models\Product::count(),
            'active_products' => \App\Models\Product::where('active', true)->count(),
            'public_products' => \App\Models\Product::where('is_public', true)->count(),
            'products_with_stock' => \App\Models\Product::where('current_stock', '>', 0)->count(),
            'available_online' => \App\Models\Product::where('is_public', true)
                ->where('active', true)
                ->where('current_stock', '>', 0)
                ->count(),
            'sample_products' => \App\Models\Product::select('id', 'name', 'active', 'is_public', 'current_stock', 'sale_price')
                ->limit(10)
                ->get()
                ->map(function($p) {
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'active' => $p->active,
                        'is_public' => $p->is_public,
                        'stock' => $p->current_stock,
                        'price' => $p->sale_price,
                        'available_online' => $p->is_public && $p->active && $p->current_stock > 0
                    ];
                })
        ];
        
        return response()->json($data);
    });
});
