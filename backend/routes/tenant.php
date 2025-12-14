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
    Route::post('/orders', [App\Http\Controllers\PublicCatalogController::class, 'store']);
    Route::get('/orders/{uuid}', [App\Http\Controllers\PublicCatalogController::class, 'show']);

    // Ruta para buscar pedido por código (para el POS)
    Route::post('/orders/find-by-code', [App\Http\Controllers\PublicCatalogController::class, 'findByCode']);
});
