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
    
    // 🔍 TEMPORAL - Debug endpoint para verificar que las rutas están cargadas
    Route::get('/debug/routes', function () {
        $routes = collect(\Route::getRoutes())->filter(function($route) {
            return str_contains($route->uri(), 'web-catalog');
        })->map(function($route) {
            return [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'action' => $route->getActionName()
            ];
        })->values();
        
        return response()->json([
            'tenant_id' => tenant('id'),
            'web_catalog_routes_count' => $routes->count(),
            'routes' => $routes
        ]);
    });
    
    // 🔍 TEMPORAL - Verificar si la tabla web_catalog_configs existe
    Route::get('/debug/database', function () {
        try {
            $tableExists = \Schema::hasTable('web_catalog_configs');
            $configCount = $tableExists ? DB::table('web_catalog_configs')->count() : 0;
            $hasConfig = $tableExists ? DB::table('web_catalog_configs')->where('tenant_id', tenant('id'))->exists() : false;
            
            return response()->json([
                'tenant_id' => tenant('id'),
                'table_exists' => $tableExists,
                'total_configs' => $configCount,
                'tenant_has_config' => $hasConfig,
                'migrations_ran' => DB::table('migrations')->count(),
                'last_migration' => DB::table('migrations')->orderBy('id', 'desc')->first()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    });
});
