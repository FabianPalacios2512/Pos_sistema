<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CentralLoginController;
use App\Http\Controllers\Api\SuperAdminController;
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
});

// ==================== SUPER ADMIN (GOD MODE) - Prefijo /admin/api/* ====================
// Duplicar rutas para compatibilidad con frontend (usa ambos prefijos)
Route::prefix('admin/api')->middleware(\App\Http\Middleware\PreventTenancyInit::class)->group(function () {
    Route::delete('/tenants/{id}', [SuperAdminController::class, 'deleteTenant']);
    Route::put('/tenants/{id}', [SuperAdminController::class, 'updateTenantSubscription']); // Para actualizar plan
    Route::get('/tenants/{id}/users', [SuperAdminController::class, 'getTenantUsers']);
    Route::get('/tenants/{id}/products', [SuperAdminController::class, 'getTenantProducts']);
    Route::post('/tenants/{id}/users/{userId}/reset-password', [SuperAdminController::class, 'resetUserPassword']);
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

// ==================== SUPER ADMIN - AI MONITORING ====================
// Rutas para monitoreo global de IA (todos los tenants)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/ai-monitoring/dashboard', [\App\Http\Controllers\Api\SuperAdminAIMonitoringController::class, 'dashboard']);
});

// ==================== 🧪 TEST ROUTES - EXCEL IMPORT AI ====================
// ⚠️ SOLO PARA DESARROLLO - Eliminar en producción
if (config('app.env') !== 'production') {
    require __DIR__.'/test_excel.php';
}
