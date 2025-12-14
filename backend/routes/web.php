<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Central\DashboardController;
use App\Http\Controllers\Api\AuthController;

Route::get('/', function () {
    return view('welcome');
});

/**
 * ============================================
 * AUTENTICACIÓN DE SUPER ADMIN
 * ============================================
 */
Route::post('/api/admin/login', [AuthController::class, 'login'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('admin.login');

/**
 * ============================================
 * PANEL DE SUPER ADMIN (GOD MODE)
 * ============================================
 * Estas rutas son para el panel central de administración
 * Solo accesibles desde dominios centrales (localhost, 127.0.0.1)
 */
Route::prefix('admin')->middleware('superadmin')->group(function () {

    // Dashboard principal del super admin
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // API endpoints para el dashboard
    Route::prefix('api')->group(function () {
        // KPIs principales
        Route::get('/kpis', [DashboardController::class, 'getKpis'])->name('admin.api.kpis');

        // Gestión de tenants
        Route::get('/tenants', [DashboardController::class, 'getTenants'])->name('admin.api.tenants');
        Route::post('/tenants', [DashboardController::class, 'createTenant'])->name('admin.api.tenants.create');
        Route::get('/tenants/{id}', [DashboardController::class, 'getTenantDetails'])->name('admin.api.tenant.details');
        Route::put('/tenants/{id}', [DashboardController::class, 'updateTenant'])->name('admin.api.tenant.update');
        Route::delete('/tenants/{id}', [DashboardController::class, 'deleteTenant'])->name('admin.api.tenant.delete');

        // Gestión de usuarios de tenants
        Route::get('/tenants/{id}/users', [DashboardController::class, 'getTenantUsers'])->name('admin.api.tenant.users');
        Route::post('/tenants/{tenantId}/users/{userId}/reset-password', [DashboardController::class, 'resetUserPassword'])->name('admin.api.tenant.user.reset-password');

        // Gestión de productos de tenants
        Route::get('/tenants/{id}/products', [DashboardController::class, 'getTenantProducts'])->name('admin.api.tenant.products');

        // Generación de links de registro
        Route::post('/generate-signup-link', [DashboardController::class, 'generateSignupLink'])->name('admin.api.generate-signup-link');

        // Monitor de IA
        Route::get('/ai-usage', [DashboardController::class, 'getAiUsage'])->name('admin.api.ai-usage');
    });
});
