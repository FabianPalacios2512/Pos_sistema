<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CentralLoginController;
use App\Http\Controllers\Api\SuperAdminController;
use App\Http\Controllers\Api\SecurityController;
use App\Http\Controllers\Api\TenantRegisterController;
use App\Http\Controllers\Api\PlanUpgradeController;
use App\Http\Controllers\Api\PaymentHistoryController;
use App\Http\Controllers\Api\WebCatalogConfigController;
use App\Http\Controllers\WompiPaymentController;
use App\Http\Controllers\EPaycoPaymentController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\RadioProxyController;
use App\Http\Controllers\PublicRadioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Central)
|--------------------------------------------------------------------------
*/

// ==================== RADIO 105 FM - API PÚBLICA ====================
// Servicio gratuito de streaming de radio colombiana
// Documentación: https://105pos.pro/docs/radio-api
Route::prefix('public/radio')->group(function () {
    Route::get('/info', [PublicRadioController::class, 'info']);
    Route::get('/stations', [PublicRadioController::class, 'stations']);
    Route::get('/stations/{id}', [PublicRadioController::class, 'station']);
    Route::get('/search', [PublicRadioController::class, 'search']);
    Route::get('/categories', [PublicRadioController::class, 'categories']);
    Route::get('/cities', [PublicRadioController::class, 'cities']);
    Route::get('/widget', [PublicRadioController::class, 'widget']);      // Sirve JS
    Route::get('/styles', [PublicRadioController::class, 'widgetCSS']);   // Sirve CSS
});

// Rutas públicas (sin autenticación)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin/login', [AuthController::class, 'adminLogin'])
    ->middleware(\App\Http\Middleware\PreventTenancyInit::class)
    ->name('api.admin.login'); // Login específico para super admin
Route::post('/register', [AuthController::class, 'register']);
Route::post('/register-tenant', [TenantRegisterController::class, 'register']);
Route::post('/update-tenant-plan', [\App\Http\Controllers\Api\TenantPlanController::class, 'updatePlan']);
Route::post('/process-upgrade', [PlanUpgradeController::class, 'processUpgrade']);  // 🔥 Upgrade post-pago (público)
Route::get('/process-upgrade', [PlanUpgradeController::class, 'processUpgrade']);   // 🔥 TAMBIÉN ACCEPT GET (Wompi podría usar GET)
// Historial de pagos - PÚBLICO (información de lectura, sin datos sensibles)
Route::get('/payment-history/{tenantId}', [PaymentHistoryController::class, 'getPaymentHistory']);
// 🔍 TEMPORAL - Endpoints de DEBUG para saber qué está pasando
Route::get('/debug/ping', function () {
    \Log::info('DEBUG: /api/debug/ping - Request received', [
        'timestamp' => now()->toIso8601String(),
        'ip' => request()->ip(),
    ]);
    return response()->json([
        'success' => true,
        'message' => 'API is reachable',
        'timestamp' => now()->toIso8601String(),
    ]);
});
Route::get('/debug/payment-success', function () {
    \Log::info('DEBUG: /api/debug/payment-success - GET Request detected (Wompi)', [
        'all_params' => request()->all(),
        'query_string' => request()->getQueryString(),
        'timestamp' => now()->toIso8601String(),
    ]);
    return response()->json([
        'success' => true,
        'message' => 'Payment success endpoint works',
        'received_params' => request()->all(),
    ]);
});
Route::post('/check-domain', [TenantRegisterController::class, 'checkDomain']);
Route::post('/auth/validate-admin', [AuthController::class, 'validateAdmin']);

// ==================== RADIO PROXY (Sin autenticación) ====================
Route::get('/radio/search', [RadioProxyController::class, 'search']);

// ==================== CENTRAL LOGIN (Smart Login) ====================
Route::post('/central/login', [CentralLoginController::class, 'centralLogin']);
Route::get('/central/login-session', [CentralLoginController::class, 'getCentralLoginSession']);
Route::get('/central/check-email', [CentralLoginController::class, 'checkEmailExists']);
Route::get('/central/check-document', [CentralLoginController::class, 'checkDocumentExists']);

// ==================== SUPER ADMIN (GOD MODE) ====================
// TODAS las rutas de admin DEBEN evitar la inicialización de tenancy
Route::middleware(\App\Http\Middleware\PreventTenancyInit::class)->group(function () {
    // Rutas con prefijo /api/admin/*
    Route::get('/admin/kpis', [SuperAdminController::class, 'getKPIs']);
    Route::get('/admin/tenants', [SuperAdminController::class, 'getTenants']);
    Route::get('/admin/tenants/{id}', [SuperAdminController::class, 'getTenantDetails']);
    Route::post('/admin/tenants', [SuperAdminController::class, 'createTenant']);
    Route::put('/admin/tenants/{id}/subscription', [SuperAdminController::class, 'updateTenantSubscription']);
    Route::put('/admin/tenants/{id}/status', [SuperAdminController::class, 'updateTenantStatus']);
    Route::delete('/admin/tenants/{id}', [SuperAdminController::class, 'deleteTenant']);
    Route::get('/check-domain/{domain}', [SuperAdminController::class, 'checkDomainAvailability']);
    Route::get('/check-cedula/{cedula}', [SuperAdminController::class, 'checkCedulaAvailability']);
    Route::get('/check-email/{email}', [SuperAdminController::class, 'checkEmailAvailability']);
    
    // AI Monitoring para Super Admin (todos los tenants)
    Route::get('/admin/ai-monitoring/dashboard', [\App\Http\Controllers\Api\SuperAdminAIMonitoringController::class, 'dashboard']);
    Route::get('/admin/ai-monitoring/key/{keyIndex}', [\App\Http\Controllers\Api\SuperAdminAIMonitoringController::class, 'testKey']);

    // System Tools (GOD MODE)
    Route::get('/admin/system/health', [\App\Http\Controllers\Api\SystemToolsController::class, 'health']);
    Route::get('/admin/system/logs', [\App\Http\Controllers\Api\SystemToolsController::class, 'getLogs']);
    Route::delete('/admin/system/logs', [\App\Http\Controllers\Api\SystemToolsController::class, 'clearLogs']);
    Route::post('/admin/system/maintenance', [\App\Http\Controllers\Api\SystemToolsController::class, 'maintenance']);
    Route::get('/admin/system/environment', [\App\Http\Controllers\Api\SystemToolsController::class, 'environment']);
    Route::get('/admin/system/tenant-db/{tenantId}', [\App\Http\Controllers\Api\SystemToolsController::class, 'tenantDatabaseInfo']);

    // Security Dashboard (GOD MODE)
    Route::get('/admin/security/dashboard', [SecurityController::class, 'dashboard']);
    Route::post('/admin/security/unblock-user', [SecurityController::class, 'unblockUser']);
    Route::post('/admin/security/unblock-ip', [SecurityController::class, 'unblockIp']);
    Route::post('/admin/security/block-ip', [SecurityController::class, 'blockIp']);
    Route::post('/admin/security/cleanup', [SecurityController::class, 'cleanup']);
    Route::post('/admin/security/resolve-event', [SecurityController::class, 'resolveEvent']);

    // Tenant Error Logs (GOD MODE)
    Route::get('/admin/tenants/{id}/errors', [SuperAdminController::class, 'getTenantErrors']);
    Route::post('/admin/tenants/{id}/errors/{errorId}/resolve', [SuperAdminController::class, 'resolveError']);
    Route::post('/admin/tenants/{id}/errors/{errorId}/analyze', [SuperAdminController::class, 'analyzeError']);
    Route::post('/admin/tenants/{id}/errors/analyze-all', [SuperAdminController::class, 'analyzeAllErrors']);
});

// ==================== SUPER ADMIN (GOD MODE) - Prefijo /admin/api/* ====================
// Duplicar rutas para compatibilidad con frontend (usa ambos prefijos)
Route::prefix('admin/api')->middleware(\App\Http\Middleware\PreventTenancyInit::class)->group(function () {
    Route::delete('/tenants/{id}', [SuperAdminController::class, 'deleteTenant']);
    Route::put('/tenants/{id}', [SuperAdminController::class, 'updateTenant']);
    Route::put('/tenants/{id}/subscription', [SuperAdminController::class, 'updateTenantSubscription']);
    Route::get('/tenants/{id}/users', [SuperAdminController::class, 'getTenantUsers']);
    Route::get('/tenants/{id}/products', [SuperAdminController::class, 'getTenantProducts']);
    Route::post('/tenants/{id}/users/{userId}/reset-password', [SuperAdminController::class, 'resetUserPassword']);
    Route::put('/tenants/{id}/users/{userId}', [SuperAdminController::class, 'updateTenantUser']);
    Route::put('/tenants/{id}/products/{productId}', [SuperAdminController::class, 'updateTenantProduct']);
    Route::delete('/tenants/{id}/products/{productId}', [SuperAdminController::class, 'deleteTenantProduct']);
    
    // Tenant Error Logs
    Route::get('/tenants/{id}/errors', [SuperAdminController::class, 'getTenantErrors']);
    Route::post('/tenants/{id}/errors/{errorId}/resolve', [SuperAdminController::class, 'resolveError']);
    Route::post('/tenants/{id}/errors/{errorId}/analyze', [SuperAdminController::class, 'analyzeError']);
    Route::post('/tenants/{id}/errors/analyze-all', [SuperAdminController::class, 'analyzeAllErrors']);

    // AI Monitoring para Super Admin (frontend usa /admin/api/*)
    Route::get('/ai-monitoring/dashboard', [\App\Http\Controllers\Api\SuperAdminAIMonitoringController::class, 'dashboard']);
});


// ==================== PASSWORD RESET ROUTES ====================
Route::post('/password/forgot', [PasswordResetController::class, 'forgotPassword']);

// ==================== RUTAS AUTENTICADAS ====================
Route::middleware(['auth:sanctum'])->group(function () {
    // ✅ Health check - Solo verificar que el tenant existe en la tabla central
    // NO intenta acceder a la DB del tenant (eso requiere middleware de tenancy)
    Route::get('/health-check', function () {
        try {
            $user = request()->user();

            // Verificar que el usuario tiene tenant_id
            if (!$user || !$user->tenant_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tenant associated with user'
                ], 404);
            }

            // Verificar que el tenant existe en la base de datos central
            $tenant = \DB::connection('mysql')->table('tenants')
                ->where('id', $user->tenant_id)
                ->first();

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Tenant not found in central database'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'tenant' => $user->tenant_id,
                'plan' => $tenant->plan ?? 'unknown',
                'timestamp' => now()->toIso8601String()
            ]);
        } catch (\Exception $e) {
            \Log::error('❌ Health check failed', [
                'error' => $e->getMessage(),
                'user' => request()->user()?->email
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Health check failed'
            ], 500);
        }
    });

    // Plan upgrades para tenants existentes (AUTENTICADO)
    Route::post('/upgrade-plan', [PlanUpgradeController::class, 'upgrade']);
});

// ==================== REPORTE DE ERRORES DEL CLIENTE (FRONTEND) ====================
// Fuera de auth:sanctum porque en contexto de subdominio de tenant, Sanctum no puede
// validar el token del tenant (la DB cambia según el dominio, causando 401).
// En su lugar validamos el token manualmente contra la DB del tenant.
Route::middleware(\App\Http\Middleware\PreventTenancyInit::class)
    ->post('/errors/report', function (\Illuminate\Http\Request $request) {
        try {
            $bearerToken = $request->bearerToken();
            if (!$bearerToken) {
                return response()->json(['success' => false], 422);
            }

            // Determinar tenant desde el Host header (ej: las-marcas.localhost -> las_marcas)
            $host = $request->getHost();
            $parts = explode('.', $host);
            if (count($parts) < 2) {
                return response()->json(['success' => false], 422);
            }
            $tenantId = str_replace('-', '_', $parts[0]);

            // Verificar que el tenant existe en la DB central
            $tenantExists = \DB::connection('mysql')->table('tenants')->where('id', $tenantId)->exists();
            if (!$tenantExists) {
                return response()->json(['success' => false], 422);
            }

            // Validar el token manualmente contra la DB del tenant.
            // Sanctum guarda el token como: sha256(rawToken) donde el bearer token
            // tiene formato "{id}|{rawToken}" — hay que hashear solo la parte del rawToken.
            $tokenParts = explode('|', $bearerToken, 2);
            $rawToken   = count($tokenParts) === 2 ? $tokenParts[1] : $bearerToken;
            $tokenId    = count($tokenParts) === 2 ? (int) $tokenParts[0] : null;
            $tokenHash  = hash('sha256', $rawToken);
            $tenantDbName = 'tenant' . $tenantId;
            $tokenQuery = \DB::connection('mysql')
                ->table($tenantDbName . '.personal_access_tokens')
                ->where('token', $tokenHash);
            if ($tokenId) {
                $tokenQuery->where('id', $tokenId);
            }
            $tokenRecord = $tokenQuery->first();

            if (!$tokenRecord) {
                return response()->json(['success' => false], 401);
            }

            $request->validate([
                'type'     => 'required|string|max:100',
                'message'  => 'required|string|max:2000',
                'severity' => 'nullable|string|in:warning,error,critical',
                'url'      => 'nullable|string|max:500',
                'context'  => 'nullable|array',
            ]);

            \App\Services\TenantErrorLoggerService::logEvent(
                $tenantId,
                $request->input('severity', 'warning'),
                $request->input('type'),
                $request->input('message'),
                array_filter([
                    'url'        => $request->input('url'),
                    'user_agent' => $request->userAgent(),
                    'extra'      => $request->input('context'),
                ])
            );

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false], 500);
        }
    });

// Historial de pagos (PUBLIC - validación por tenant_id)
Route::get('/payment-history/{tenantId}', [PaymentHistoryController::class, 'getPaymentHistory']);

Route::post('/password/validate-code', [PasswordResetController::class, 'validateCode']);
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);
Route::get('/password/cleanup-tokens', [PasswordResetController::class, 'cleanupExpiredTokens']);

// 🧪 RUTA DE TEST: Activar plan sin Wompi (solo para desarrollo)
Route::post('/test-activate-plan', function(\Illuminate\Http\Request $request) {
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
        'tenant_id' => 'required|string|exists:tenants,id',
        'plan' => 'required|string|in:free_trial,basic,premium,enterprise',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }

    // Simular pago exitoso y actualizar plan
    $response = \Illuminate\Support\Facades\Http::post(url('/api/update-tenant-plan'), [
        'tenant_id' => $request->tenant_id,
        'plan' => $request->plan,
    ]);

    return response()->json([
        'success' => true,
        'message' => '✅ Plan activado (TEST MODE)',
        'plan' => $request->plan,
        'update_response' => $response->json()
    ]);
});

// ==================== GOOGLE OAUTH ROUTES ====================
Route::post('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
Route::get('/auth/google/user-data', [GoogleAuthController::class, 'getGoogleUserData']);
Route::get('/auth/google/login-session', [GoogleAuthController::class, 'getGoogleLoginSession']);
Route::post('/auth/google/login', [GoogleAuthController::class, 'loginWithGoogle']);

// Ruta de prueba
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Central funcionando correctamente',
        'timestamp' => now(),
        'version' => '1.1.0'
    ]);
});

// Ruta para verificación de conexión (offline sync)
Route::get('/ping', function () {
    return response()->json([
        'success' => true,
        'message' => 'pong',
        'timestamp' => now()->timestamp
    ], 200);
});

// ==================== EPAYCO - PAYMENT ROUTES ====================
// 🚀 Checkout 2.0 (Smart Checkout) - Nueva implementación
Route::post('/epayco/create-session', [EPaycoPaymentController::class, 'createCheckoutSession']);

// Legacy v1 (mantener para compatibilidad)
Route::post('/epayco/init-transaction', [EPaycoPaymentController::class, 'initTransaction']);
Route::post('/epayco/webhook', [EPaycoPaymentController::class, 'webhook']);
Route::get('/epayco/check-payment-status', [EPaycoPaymentController::class, 'checkPaymentStatus']); // Acepta query params: ?reference=xxx o ?ref_payco=xxx

// 🔒 ENDPOINT SEGURO: Verificación de pago con token (público pero protegido)
Route::post('/epayco/verify-payment', [EPaycoPaymentController::class, 'verifyPaymentWithToken']);

// 🔧 DEV ONLY: Aprobar pago manualmente (cuando webhook no llega en localhost)
Route::post('/epayco/manual-approve/{reference}', [EPaycoPaymentController::class, 'manualApprove']);

// ==================== WOMPI - PAYMENT ROUTES (DEPRECATED) ====================
// Rutas de pago (públicas - no requieren auth)
Route::post('/create-payment-transaction', [WompiPaymentController::class, 'createTransaction']);
Route::post('/create-payment-link', [WompiPaymentController::class, 'createPaymentLink']);
Route::post('/wompi/webhook', [WompiPaymentController::class, 'webhook']);
Route::get('/transaction-status/{transactionId}', [WompiPaymentController::class, 'getTransactionStatus']);
Route::get('/payment-methods', [WompiPaymentController::class, 'getAcceptedPaymentMethods']);
// 🔧 DEV ONLY: Simular webhook para testing en localhost
Route::post('/dev/simulate-payment-success/{tenantId}', [WompiPaymentController::class, 'simulatePaymentSuccess']);

// ==================== WEB CATALOG CONFIGURATION ====================
// 🔍 Endpoint de debug para verificar que las rutas están cargadas
Route::get('/web-catalog/debug-test', function() {
    return response()->json([
        'success' => true,
        'message' => 'Web Catalog routes loaded in api.php (Central)',
        'tenant_id' => tenant('id') ?? 'no-tenant',
        'controller_exists' => class_exists(\App\Http\Controllers\Api\WebCatalogConfigController::class),
        'timestamp' => now()->toDateTimeString(),
        'route_type' => 'central'
    ]);
});

// ✅ Rutas de Web Catalog con autenticación (Central API)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/web-catalog/config', [\App\Http\Controllers\Api\WebCatalogConfigController::class, 'getConfig']);
    Route::post('/web-catalog/config', [\App\Http\Controllers\Api\WebCatalogConfigController::class, 'saveConfig']);
});
// ==================== FIN WEB CATALOG CONFIGURATION ====================

// ==================== 🧪 TEST ROUTES - EXCEL IMPORT AI ====================
// ⚠️ SOLO PARA DESARROLLO - Eliminar en producción
if (config('app.env') !== 'production') {
    require __DIR__.'/test_excel.php';
}
