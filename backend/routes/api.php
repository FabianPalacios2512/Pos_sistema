<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TenantRegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Central)
|--------------------------------------------------------------------------
*/

// Rutas públicas (sin autenticación)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/register-tenant', [TenantRegisterController::class, 'register']);
Route::post('/check-domain', [TenantRegisterController::class, 'checkDomain']);
Route::post('/auth/validate-admin', [AuthController::class, 'validateAdmin']);

// Ruta de prueba
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Central funcionando correctamente',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});
