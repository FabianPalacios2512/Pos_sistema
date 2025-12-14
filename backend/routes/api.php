<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CentralLoginController;
use App\Http\Controllers\Api\TenantRegisterController;
use App\Http\Controllers\WompiPaymentController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\PasswordResetController;
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
Route::post('/check-domain', [TenantRegisterController::class, 'checkDomain']);
Route::post('/auth/validate-admin', [AuthController::class, 'validateAdmin']);

// ==================== CENTRAL LOGIN (Smart Login) ====================
Route::post('/central/login', [CentralLoginController::class, 'centralLogin']);
Route::get('/central/check-email', [CentralLoginController::class, 'checkEmailExists']);

// ==================== PASSWORD RESET ROUTES ====================
Route::post('/password/forgot', [PasswordResetController::class, 'forgotPassword']);
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
        'version' => '1.0.0'
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

// ==================== WOMPI - PAYMENT ROUTES ====================
// Rutas de pago (públicas - no requieren auth)
Route::post('/create-payment-transaction', [WompiPaymentController::class, 'createTransaction']);
Route::post('/create-payment-link', [WompiPaymentController::class, 'createPaymentLink']);
Route::post('/wompi/webhook', [WompiPaymentController::class, 'webhook']);
Route::get('/transaction-status/{transactionId}', [WompiPaymentController::class, 'getTransactionStatus']);
Route::get('/payment-methods', [WompiPaymentController::class, 'getAcceptedPaymentMethods']);
// 🔧 DEV ONLY: Simular webhook para testing en localhost
Route::post('/dev/simulate-payment-success/{tenantId}', [WompiPaymentController::class, 'simulatePaymentSuccess']);

// ==================== SUPER ADMIN - AI MONITORING ====================
// Rutas para monitoreo global de IA (todos los tenants)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/ai-monitoring/dashboard', [\App\Http\Controllers\Api\SuperAdminAIMonitoringController::class, 'dashboard']);
});
