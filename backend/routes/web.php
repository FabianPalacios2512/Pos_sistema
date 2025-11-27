<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Central\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

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

        // Monitor de IA
        Route::get('/ai-usage', [DashboardController::class, 'getAiUsage'])->name('admin.api.ai-usage');
    });
});
