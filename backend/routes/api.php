<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CentralLoginController;
use App\Http\Controllers\Api\TenantRegisterController;
use App\Http\Controllers\Api\PlanUpgradeController;
use App\Http\Controllers\Api\PaymentHistoryController;
use App\Http\Controllers\Api\WebCatalogConfigController;
use App\Http\Controllers\WompiPaymentController;
use App\Http\Controllers\EPaycoPaymentController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\RadioProxyController;
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

// ==================== PASSWORD RESET ROUTES ====================
Route::post('/password/forgot', [PasswordResetController::class, 'forgotPassword']);

// ==================== RUTAS AUTENTICADAS ====================
Route::middleware(['auth:sanctum'])->group(function () {
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
Route::get('/epayco/check-payment-status/{reference}', [EPaycoPaymentController::class, 'checkPaymentStatus']);

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
