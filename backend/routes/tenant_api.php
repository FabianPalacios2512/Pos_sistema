<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\SalesController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\SystemSettingsController;
use App\Http\Controllers\Api\DiscountsController;
use App\Http\Controllers\Api\PaymentMethodsController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\InventoryTestController;
use App\Http\Controllers\Api\CashSessionController;
use App\Http\Controllers\Api\ReturnsController;
use App\Http\Controllers\Api\ProductAnalyticsController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\ExpenseCategoryController;
use App\Http\Controllers\Api\ExcelImportController;
use App\Http\Controllers\Api\WebCatalogConfigController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Tenant\AiUsageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rutas públicas (sin autenticación)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
// Route::post('/register-tenant', [\App\Http\Controllers\Api\TenantRegisterController::class, 'register']); // Removed from tenant routes
Route::post('/auth/validate-admin', [AuthController::class, 'validateAdmin']);

// 🔥 Endpoint especial para obtener tenant_id incluso con suscripción expirada
// NO requiere autenticación, solo identifica el tenant por el subdominio
Route::get('/tenant-info', function (Illuminate\Http\Request $request) {
    // Obtener el subdominio del request
    $host = $request->getHost();
    $subdomain = explode('.', $host)[0];

    // Buscar el tenant por subdominio
    $tenant = \App\Models\Tenant::where('subdomain', $subdomain)->first();

    if (!$tenant) {
        return response()->json([
            'success' => false,
            'message' => 'No tenant found for subdomain: ' . $subdomain
        ], 404);
    }

    return response()->json([
        'success' => true,
        'tenant_id' => $tenant->id,
        'business_name' => $tenant->business_name ?? 'Mi Negocio',
        'subscription_status' => $tenant->subscription_ends_at && now()->isAfter($tenant->subscription_ends_at) ? 'expired' : 'active',
        'subdomain' => $tenant->subdomain
    ]);
}); // Sin middleware - completamente público

// Verificar estado de suscripción (sin auth, solo para mostrar alertas)
Route::get('/check-subscription', function () {
    $tenant = tenant();

    if (!$tenant || !$tenant->subscription_ends_at) {
        return response()->json([
            'success' => true,
            'has_subscription' => false,
            'status' => 'no_subscription'
        ]);
    }

    $now = now();
    $expiresAt = \Carbon\Carbon::parse($tenant->subscription_ends_at);
    $daysRemaining = $now->diffInDays($expiresAt, false);

    $isExpired = $now->isAfter($expiresAt);

    return response()->json([
        'success' => true,
        'has_subscription' => true,
        'status' => $isExpired ? 'expired' : ($daysRemaining <= 7 ? 'expiring_soon' : 'active'),
        'plan' => $tenant->plan,
        'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        'days_remaining' => $isExpired ? 0 : max(0, ceil($daysRemaining)),
        'days_expired' => $isExpired ? abs($daysRemaining) : 0,
        'is_expired' => $isExpired,
        'is_expiring_soon' => !$isExpired && $daysRemaining <= 7,
        'tenant_id' => $tenant->id, // 🔥 AGREGAR tenant_id para el modal
        '_subscription_expired' => $isExpired, // 🔥 Campo que detecta apiClient
    ]);
});

/**
 * 🔄 Endpoint para verificación automática de estado de suscripción (polling)
 * Usado por el frontend para detectar cuando un pago ha sido procesado
 * sin necesidad de recargar la página o cerrar sesión
 */
Route::get('/subscription/status', function () {
    $tenant = tenant();

    if (!$tenant) {
        return response()->json([
            'success' => false,
            'active' => false,
            'message' => 'Tenant no encontrado'
        ]);
    }

    // Verificar en la columna subscription_ends_at (legacy)
    $expiresAt = $tenant->subscription_ends_at;

    // También verificar en el campo data JSON (nuevo sistema)
    $subscriptionEnd = $tenant->subscription_end ?? null;
    $status = $tenant->status ?? 'active';

    // Usar la fecha más reciente entre ambas fuentes
    if ($subscriptionEnd) {
        $endDate = \Carbon\Carbon::parse($subscriptionEnd);
    } elseif ($expiresAt) {
        $endDate = \Carbon\Carbon::parse($expiresAt);
    } else {
        // Sin fecha de expiración = activo por defecto (free trial?)
        $endDate = now()->addDays(30);
    }

    $now = now();
    $isExpired = $now->isAfter($endDate);

    // Si el status está en paused/suspended, considerar como no activo
    if (in_array($status, ['paused', 'suspended'])) {
        $isActive = false;
    } else {
        $isActive = !$isExpired;
    }

    return response()->json([
        'success' => true,
        'active' => $isActive,
        'status' => $isActive ? 'active' : ($status === 'paused' ? 'paused' : ($isExpired ? 'expired' : $status)),
        'tenant' => [
            'id' => $tenant->id,
            'plan_type' => $tenant->plan ?? 'free_trial',
            'subscription_end_date' => $endDate->format('Y-m-d'),
            'days_remaining' => $isExpired ? 0 : max(0, ceil($now->diffInDays($endDate, false)))
        ]
    ]);
});

// RUTA TEMPORAL PARA QR - Búsqueda de cotizaciones sin autenticación
Route::get('/quotes/search/{code}', [SalesController::class, 'searchQuotePublic']);

// RUTAS DE PRUEBA PARA REPORTES DE CAJA
Route::get('/cash-reports/test-db', function() { require_once __DIR__ . '/cash-reports-test.php'; return testDatabaseConnection(); });
Route::get('/cash-reports/test-simple', function() { require_once __DIR__ . '/cash-reports-test.php'; return getSimpleCashData(); });

// RUTAS TEMPORALES PARA REPORTES DE CAJA (sin autenticación, solo para desarrollo)
Route::get('/cash-reports/dashboard', function() { require_once __DIR__ . '/cash-reports-real-fixed.php'; return getCashDashboardData(); });
Route::get('/cash-reports/cashiers', function() { require_once __DIR__ . '/cash-reports-real-fixed.php'; return getCashierComparison(); });
Route::get('/cash-reports/hourly', function() { require_once __DIR__ . '/cash-reports-real-fixed.php'; return getHourlyEfficiency(); });
Route::get('/cash-reports/trend-chart', function() { require_once __DIR__ . '/cash-reports-real-fixed.php'; return getTrendChartData(); });
Route::get('/cash-reports/payment-methods', function() { require_once __DIR__ . '/cash-reports-real-fixed.php'; return getPaymentMethodsAnalysis(); });
Route::get('/cash-reports/top-sessions', function() { require_once __DIR__ . '/cash-reports-real-fixed.php'; return getTopSessions(); });
Route::get('/cash-reports/alerts', function() { require_once __DIR__ . '/cash-reports-real-fixed.php'; return getCashAlerts(); });

// RUTAS TEMPORALES DE PRUEBA (sin autenticación)
Route::get('/inventory/test/overview', [InventoryTestController::class, 'overview']);
Route::get('/inventory/test/dashboard', [InventoryTestController::class, 'dashboard']);
Route::get('/inventory/test/products', [InventoryTestController::class, 'products']);
Route::get('/inventory/test/movements', [InventoryTestController::class, 'movements']);
Route::get('/inventory/test/customers', [InventoryTestController::class, 'customers']);
Route::get('/inventory/test/alerts', [InventoryTestController::class, 'alerts']);
Route::post('/inventory/test/alerts/dismiss', [InventoryTestController::class, 'dismissAlert']);
Route::get('/inventory/test/predictions', [InventoryTestController::class, 'predictions']);
Route::get('/data/analysis/october-sales', [\App\Http\Controllers\Api\DataAnalysisController::class, 'compareOctoberSales']);
Route::get('/data/analysis/inventory-value', [\App\Http\Controllers\Api\DataAnalysisController::class, 'analyzeInventoryValue']);

// RUTAS DE ANALYTICS (sin autenticación temporal para desarrollo)
Route::get('/products/analytics', [ProductAnalyticsController::class, 'getProductsWithMetrics']);
Route::get('/suppliers/analytics', [SupplierController::class, 'getAnalytics']);

// RUTAS DE BODEGAS/TIENDAS
Route::get('/warehouses/active', [WarehouseController::class, 'getActive']);
Route::get('/warehouses/{id}/products', [WarehouseController::class, 'getWarehouseProducts']);

// RUTAS OPTIMIZADAS PARA PRODUCCIÓN (sin auth para pruebas)
Route::get('/optimized/dashboard', [\App\Http\Controllers\Api\OptimizedDashboardController::class, 'getDashboardData']);
Route::get('/optimized/recent-transactions', [\App\Http\Controllers\Api\OptimizedDashboardController::class, 'getRecentTransactions']);
Route::get('/optimized/metrics', [\App\Http\Controllers\Api\OptimizedDashboardController::class, 'getMainMetrics']);
Route::post('/optimized/clear-cache', [\App\Http\Controllers\Api\OptimizedDashboardController::class, 'clearCache']);

// Dashboard ventas hoy en hora Colombia (sin auth para pruebas)
Route::get('/dashboard/ventas-hoy', [\App\Http\Controllers\DashboardController::class, 'ventasHoy']);

// ==================== IMPORTACIÓN DE EXCEL (SIN AUTH para onboarding) ====================
// Importador inteligente de productos desde Excel/CSV
// Disponible sin autenticación para que funcione durante el onboarding
Route::prefix('excel-import')->group(function () {
    Route::post('/upload', [ExcelImportController::class, 'upload']);           // Subir y analizar archivo
    Route::post('/preview', [ExcelImportController::class, 'preview']);         // Generar preview con mapeo
    Route::post('/import', [ExcelImportController::class, 'import']);           // Importar productos a BD
    Route::post('/cancel', [ExcelImportController::class, 'cancel']);           // Cancelar importación
    Route::get('/template', [ExcelImportController::class, 'downloadTemplate']); // Descargar plantilla
});

// ===== AI USAGE - Endpoints públicos para monitoreo =====
Route::get('/ai/usage-status', [AiUsageController::class, 'getUsageStatus']);
Route::post('/ai/chat-with-file', [\App\Http\Controllers\Api\AIController::class, 'chatWithFile']); // Public for file upload compatibility
Route::get('/ai/check-limit', [AiUsageController::class, 'checkLimit']);

// ===== TRIAL STATUS - Endpoint público =====
Route::get('/check-trial-status', function() {
    if (!tenancy()->initialized) {
        return response()->json(['error' => 'No tenant context'], 400);
    }

    $tenant = tenant();
    $now = now();
    $expiresAt = $tenant->subscription_ends_at ? \Carbon\Carbon::parse($tenant->subscription_ends_at) : null;

    $isTrialExpired = false;
    $daysRemaining = null;

    if ($tenant->plan === 'trial_express' && $expiresAt) {
        $isTrialExpired = $now->isAfter($expiresAt);
        $daysRemaining = $isTrialExpired ? 0 : max(0, ceil($now->diffInDays($expiresAt, false)));
    }

    return response()->json([
        'plan' => $tenant->plan,
        'is_trial' => $tenant->plan === 'trial_express',
        'trial_expired' => $isTrialExpired,
        'days_remaining' => $daysRemaining,
        'subscription_ends_at' => $expiresAt ? $expiresAt->toDateTimeString() : null,
    ]);
});

// Rutas protegidas (requieren autenticación)
// ===== RUTAS AUTENTICADAS (con verificación de trial) =====
Route::middleware(['auth:sanctum', 'trial'])->group(function () {

    // Autenticación
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Información del Tenant (Plan y Suscripción)
    Route::get('/tenant-info', function() {
        $tenant = tenant();
        return response()->json([
            'success' => true,
            'tenant' => [
                'id' => $tenant->id,
                'business_name' => $tenant->business_name,
                'plan' => $tenant->plan,
                'subscription_ends_at' => $tenant->subscription_ends_at,
                'created_at' => $tenant->created_at,
            ]
        ]);
    });

    // Configuración del Sistema
    Route::get('/system-settings', [SystemSettingsController::class, 'index']);
    Route::put('/system-settings', [SystemSettingsController::class, 'update']);
    Route::get('/system-settings/next-invoice-number', [SystemSettingsController::class, 'getNextInvoiceNumber']);
    Route::post('/system-settings/reset', [SystemSettingsController::class, 'reset']);
    Route::post('/settings/initial-onboarding', [SystemSettingsController::class, 'saveOnboarding']);

    // Alias para compatibilidad con frontend (rutas con /tenant/)
    Route::get('/tenant/system-settings', [SystemSettingsController::class, 'index']);
    Route::put('/tenant/system-settings', [SystemSettingsController::class, 'update']);

    // Descuentos y Promociones - Rutas específicas ANTES del apiResource
    Route::post('/discounts/validate-code', [DiscountsController::class, 'validateCode']);
    Route::post('/discounts/record-usage', [DiscountsController::class, 'recordUsage']);
    Route::get('/discounts-generate-code', [DiscountsController::class, 'generateCode']);
    Route::apiResource('discounts', DiscountsController::class);

    // Métodos de Pago
    Route::apiResource('payment-methods', PaymentMethodsController::class);
    Route::post('/payment-methods/reorder', [PaymentMethodsController::class, 'reorder']);
    Route::patch('/payment-methods/{paymentMethod}/toggle-status', [PaymentMethodsController::class, 'toggleStatus']);
    Route::post('/payment-methods/{paymentMethod}/calculate-fee', [PaymentMethodsController::class, 'calculateFee']);
    Route::get('/payment-methods-pos', [PaymentMethodsController::class, 'forPos']);

    // Productos
    Route::get('/products-pos', [ProductController::class, 'forPos']); // Endpoint optimizado para POS
    Route::get('/products/low-stock', [ProductController::class, 'lowStock']);
    Route::post('/products/{product}/update-stock', [ProductController::class, 'updateStock']);
    Route::put('/products/variants/bulk-update', [ProductController::class, 'bulkUpdateVariants']); // Actualización masiva de variantes
    Route::apiResource('products', ProductController::class);

    // NOTA: Las rutas de excel-import están fuera del middleware auth para funcionar en onboarding

    // Categorías
    Route::apiResource('categories', CategoryController::class);
    Route::get('/categories-pos', [CategoryController::class, 'forPos']); // Endpoint optimizado para POS

    // Roles
    Route::apiResource('roles', RoleController::class);
    Route::get('/roles/{id}/users', [RoleController::class, 'getUsersByRole']);

    // Usuarios
    Route::apiResource('users', UserController::class);
    Route::post('/users/{id}/change-password', [UserController::class, 'changePassword']);
    Route::patch('/users/{id}/toggle-active', [UserController::class, 'toggleActive']);

    // Permisos
    Route::apiResource('permissions', PermissionController::class);

    // Clientes
    Route::post('/customers/check-document', [CustomerController::class, 'checkDocument']); // 🎯 CreditiTenda: Validar documento
    Route::apiResource('customers', CustomerController::class);

    // Proveedores
    Route::apiResource('suppliers', SupplierController::class);
    Route::put('/suppliers/{id}/toggle-status', [SupplierController::class, 'toggleStatus']);

    // ==================== ÓRDENES DE COMPRA ====================
    Route::get('/purchase-orders', [\App\Http\Controllers\Api\PurchaseOrderController::class, 'index']);
    Route::post('/purchase-orders', [\App\Http\Controllers\Api\PurchaseOrderController::class, 'store']);
    Route::get('/purchase-orders/{id}', [\App\Http\Controllers\Api\PurchaseOrderController::class, 'show']);
    Route::put('/purchase-orders/{id}', [\App\Http\Controllers\Api\PurchaseOrderController::class, 'update']);
    Route::delete('/purchase-orders/{id}', [\App\Http\Controllers\Api\PurchaseOrderController::class, 'destroy']);
    Route::post('/purchase-orders/{id}/status', [\App\Http\Controllers\Api\PurchaseOrderController::class, 'updateStatus']);
    Route::post('/purchase-orders/{id}/receive', [\App\Http\Controllers\Api\PurchaseOrderController::class, 'receive']);
    // ==================== FIN ÓRDENES DE COMPRA ====================

    // ==================== BODEGAS/SEDES MULTITIENDA ====================
    Route::get('/warehouses', [App\Http\Controllers\WarehouseController::class, 'index']);
    Route::get('/warehouses/default', [App\Http\Controllers\WarehouseController::class, 'getDefault']);
    Route::get('/warehouses/{id}', [App\Http\Controllers\WarehouseController::class, 'show']);
    Route::post('/warehouses', [App\Http\Controllers\WarehouseController::class, 'store']);
    Route::put('/warehouses/{id}', [App\Http\Controllers\WarehouseController::class, 'update']);
    Route::delete('/warehouses/{id}', [App\Http\Controllers\WarehouseController::class, 'destroy']);
    Route::get('/warehouses/{id}/inventory', [App\Http\Controllers\WarehouseController::class, 'inventory']);
    Route::post('/warehouses/{id}/update-stock', [App\Http\Controllers\WarehouseController::class, 'updateStock']);

    // ==================== TRASLADOS DE MERCANCÍA ====================
    Route::get('/stock-transfers', [App\Http\Controllers\StockTransferController::class, 'index']);
    Route::get('/stock-transfers/{id}', [App\Http\Controllers\StockTransferController::class, 'show']);
    Route::post('/stock-transfers', [App\Http\Controllers\StockTransferController::class, 'store']);
    Route::post('/stock-transfers/{id}/complete', [App\Http\Controllers\StockTransferController::class, 'complete']);
    Route::post('/stock-transfers/{id}/cancel', [App\Http\Controllers\StockTransferController::class, 'cancel']);
    Route::delete('/stock-transfers/{id}', [App\Http\Controllers\StockTransferController::class, 'destroy']);
    // ==================== FIN MULTITIENDA ====================

    // Ventas y Cotizaciones
    Route::apiResource('sales', SalesController::class);
    Route::get('/sales/{sale}/items', [SalesController::class, 'items']);
    Route::post('/sales/{sale}/refund', [SalesController::class, 'refund']);

    // Facturas
    Route::get('/invoices/next-number', [InvoiceController::class, 'getNextNumber']);
    Route::get('/invoices/stats', [InvoiceController::class, 'stats']);
    Route::apiResource('invoices', InvoiceController::class);
    Route::post('/pos/invoices', [InvoiceController::class, 'createPosInvoice']); // Con autenticación

    // PDF generation moved outside auth middleware

    // ==================== SESIONES DE CAJA ====================
    Route::get('/cash-sessions', [CashSessionController::class, 'index']);
    Route::get('/cash-sessions/current', [CashSessionController::class, 'getCurrentSession']);
    Route::get('/cash-sessions/check', [CashSessionController::class, 'checkSession']);
    Route::get('/cash-sessions/user/{userId}/current', [CashSessionController::class, 'getUserSession']);
    Route::post('/cash-sessions/open', [CashSessionController::class, 'openSession']);
    Route::post('/cash-sessions/close', [CashSessionController::class, 'closeSession']);
    Route::post('/cash-sessions/{sessionId}/close', [CashSessionController::class, 'closeSessionById']);
    Route::get('/cash-sessions/{sessionId}/audit', [CashSessionController::class, 'getSessionAudit']); // Nuevo endpoint de auditoría
    Route::get('/cash-sessions/stats', [CashSessionController::class, 'getSessionStats']);
    Route::post('/cash-sessions/update-totals', [CashSessionController::class, 'updateTotals']);
    Route::get('/cash-sessions/daily-summary', [CashSessionController::class, 'getDailySummary']);
    Route::get('/cash-sessions/history', [CashSessionController::class, 'getHistory']);

    // ==================== INVENTARIO INTELIGENTE ====================
    // 1. Vista General del Inventario
    Route::get('/inventory/overview', [InventoryController::class, 'overview']);

    // 2. Vista de Productos con análisis
    Route::get('/inventory/products', [InventoryController::class, 'products']);

    // 3. Movimientos de Inventario
    Route::get('/inventory/movements', [InventoryController::class, 'movements']);
    Route::post('/inventory/movements', [InventoryController::class, 'recordMovement']);

    // 4. Vista de Clientes con análisis de compra
    Route::get('/inventory/customers', [InventoryController::class, 'customers']);

    // 5. Proveedores con análisis de rendimiento
    Route::get('/inventory/suppliers', [InventoryController::class, 'suppliers']);

    // 6. Sistema de Alertas
    Route::get('/inventory/alerts', [InventoryController::class, 'alerts']);

    // 7. Predicciones y Análisis Predictivo
    Route::get('/inventory/predictions', [InventoryController::class, 'predictions']);
    // ==================== FIN INVENTARIO INTELIGENTE ====================

    // ==================== NOTIFICACIONES ====================
    Route::get('/notifications/counts', [App\Http\Controllers\Api\NotificationController::class, 'getCounts']);
    Route::post('/notifications/mark-movements-viewed', [App\Http\Controllers\Api\NotificationController::class, 'markMovementsAsViewed']);
    Route::post('/notifications/mark-alerts-viewed', [App\Http\Controllers\Api\NotificationController::class, 'markAlertsAsViewed']);
    // ==================== FIN NOTIFICACIONES ====================

    // ==================== DEVOLUCIONES ====================
    Route::get('/returns', [App\Http\Controllers\Api\ReturnsController::class, 'index']);
    Route::post('/returns/search-invoice', [App\Http\Controllers\Api\ReturnsController::class, 'searchInvoice']);
    Route::post('/returns', [App\Http\Controllers\Api\ReturnsController::class, 'store']);
    Route::get('/returns/{id}', [App\Http\Controllers\Api\ReturnsController::class, 'show']);
    Route::put('/returns/{id}/cancel', [App\Http\Controllers\Api\ReturnsController::class, 'cancel']);

    // Métricas de devoluciones para reportes
    Route::get('/returns/metrics/{period}', [App\Http\Controllers\Api\ReturnsController::class, 'getMetrics']);
    // ==================== FIN DEVOLUCIONES ====================

    // ==================== GASTOS OPERATIVOS ====================
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::get('/expenses/statistics', [ExpenseController::class, 'statistics']);
    Route::get('/expenses/categories', [ExpenseController::class, 'getCategories']);
    Route::get('/expenses/check-cash-session', [ExpenseController::class, 'checkCashSession']);
    Route::get('/expenses/{id}', [ExpenseController::class, 'show']);
    Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);

    // Gestión de Categorías de Gastos
    Route::get('/expense-categories', [ExpenseCategoryController::class, 'index']);
    Route::post('/expense-categories', [ExpenseCategoryController::class, 'store']);
    Route::get('/expense-categories/statistics', [ExpenseCategoryController::class, 'statistics']);
    Route::get('/expense-categories/{id}', [ExpenseCategoryController::class, 'show']);
    Route::put('/expense-categories/{id}', [ExpenseCategoryController::class, 'update']);
    Route::patch('/expense-categories/{id}/toggle', [ExpenseCategoryController::class, 'toggleActive']);
    Route::delete('/expense-categories/{id}', [ExpenseCategoryController::class, 'destroy']);
    // ==================== FIN GASTOS OPERATIVOS ====================

    // ==================== CRÉDITOS Y CUENTAS POR COBRAR ====================
    Route::get('/credit-payments', [\App\Http\Controllers\Api\CreditPaymentController::class, 'index']);
    Route::post('/credit-payments', [\App\Http\Controllers\Api\CreditPaymentController::class, 'store']);
    Route::post('/credit-reminders', [\App\Http\Controllers\Api\CreditPaymentController::class, 'sendReminder']);
    Route::get('/credit-reminder-settings', [\App\Http\Controllers\Api\CreditPaymentController::class, 'getReminderSettings']);
    Route::post('/credit-reminder-settings', [\App\Http\Controllers\Api\CreditPaymentController::class, 'saveReminderSettings']);
    // ==================== FIN CRÉDITOS ====================

    // ==================== LOYALTY POINTS (PUNTOS DE FIDELIZACIÓN) ====================
    Route::get('/loyalty/settings', [\App\Http\Controllers\LoyaltyController::class, 'getSettings']);
    Route::post('/loyalty/calculate-points', [\App\Http\Controllers\LoyaltyController::class, 'calculatePointsToEarn']);
    Route::post('/loyalty/calculate-value', [\App\Http\Controllers\LoyaltyController::class, 'calculatePointsValue']);
    Route::get('/loyalty/customer/{customerId}/points', [\App\Http\Controllers\LoyaltyController::class, 'getCustomerPoints']);
    Route::get('/loyalty/customer/{customerId}/transactions', [\App\Http\Controllers\LoyaltyController::class, 'getCustomerTransactions']);
    Route::post('/loyalty/validate-redemption', [\App\Http\Controllers\LoyaltyController::class, 'validateRedemption']);
    Route::post('/loyalty/adjust-points', [\App\Http\Controllers\LoyaltyController::class, 'adjustPoints']);
    // ==================== FIN LOYALTY POINTS ====================

    // ==================== AI CHAT (con límites de uso) ====================
    Route::middleware(['ai.limit'])->group(function () {
        Route::post('/ai/chat', [\App\Http\Controllers\Api\AIController::class, 'chat']);
        // Route::post('/ai/chat-with-file', [\App\Http\Controllers\Api\AIController::class, 'chatWithFile']); // Moved to public routes

        // AI ACTIONS (Acciones ejecutables con límites)
        Route::post('/ai/actions/create-discount', [\App\Http\Controllers\Api\AIActionsController::class, 'createDiscount']);
        Route::post('/ai/actions/send-bulk-whatsapp', [\App\Http\Controllers\Api\AIActionsController::class, 'sendBulkWhatsApp']);
        Route::post('/ai/actions/create-campaign', [\App\Http\Controllers\Api\AIActionsController::class, 'createCampaign']);
        Route::post('/ai/actions/create-product', [\App\Http\Controllers\Api\AIActionsController::class, 'createProduct']);
    });

    // Rutas de IA sin límite (no consumen tokens)
    Route::post('/ai/clear-history', [\App\Http\Controllers\Api\AIController::class, 'clearHistory']);
    Route::get('/ai/usage-stats', [\App\Http\Controllers\Api\AIController::class, 'getUsageStats']);
    Route::get('/ai/provider-config', [\App\Http\Controllers\Api\AIController::class, 'getProviderConfig']);
    Route::post('/ai/actions/create-category', [\App\Http\Controllers\Api\AIActionsController::class, 'createCategory']);

});

// ==================== WEB CATALOG CONFIGURATION ====================
// 🔍 TEMPORAL - Endpoint de debug ANTES de auth para verificar routing
Route::get('/web-catalog/debug-test', function() {
    return response()->json([
        'success' => true,
        'message' => 'Route works! tenant_api.php is loaded',
        'tenant_id' => tenant('id'),
        'controller_exists' => class_exists(\App\Http\Controllers\Api\WebCatalogConfigController::class),
        'timestamp' => now()->toDateTimeString()
    ]);
});

// ✅ Rutas de Web Catalog SIN autenticación (temporal para debug)
Route::get('/web-catalog/config', [\App\Http\Controllers\Api\WebCatalogConfigController::class, 'getConfig']);
Route::post('/web-catalog/config', [\App\Http\Controllers\Api\WebCatalogConfigController::class, 'saveConfig']);

// 🔐 Rutas de Web Catalog con autenticación (comentadas temporalmente)
// Route::middleware(['auth:sanctum'])->group(function () {
//     Route::get('/web-catalog/config', [\App\Http\Controllers\Api\WebCatalogConfigController::class, 'getConfig']);
//     Route::post('/web-catalog/config', [\App\Http\Controllers\Api\WebCatalogConfigController::class, 'saveConfig']);
// });
// ==================== FIN WEB CATALOG CONFIGURATION ====================

// ==================== AI MONITORING (Sin Tenancy - Para Super Admin) ====================
// Estas rutas funcionan tanto para tenants como para super admin
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/admin/ai-monitoring/dashboard', [\App\Http\Controllers\Api\AdminAIMonitoringController::class, 'dashboard']);
    Route::get('/admin/ai-monitoring/key/{keyIndex}', [\App\Http\Controllers\Api\AdminAIMonitoringController::class, 'keyDetails']);
});
// ==================== FIN AI ====================

// ==================== RUTAS WHATSAPP Y PDF (SIN AUTH) ====================
Route::post('/whatsapp/send-invoice', [InvoiceController::class, 'sendWhatsApp']);
Route::post('/whatsapp/send-pdf', [InvoiceController::class, 'sendWhatsAppPDF']);
Route::post('/whatsapp/send-quotation', [InvoiceController::class, 'sendQuotationWhatsApp']);
Route::post('/whatsapp/send-quotation-pdf', [InvoiceController::class, 'sendQuotationWhatsAppPDF']);
Route::get('/whatsapp/status', [InvoiceController::class, 'getWhatsAppStatus']);
Route::get('/whatsapp/qr', [InvoiceController::class, 'getWhatsAppQR']);
Route::post('/whatsapp/initialize', [InvoiceController::class, 'initializeWhatsApp']);
Route::post('/whatsapp/disconnect', [InvoiceController::class, 'disconnectWhatsApp']);
Route::post('/whatsapp/clean-session', [InvoiceController::class, 'cleanWhatsAppSession']);
Route::post('/invoices/generate-pdf', [InvoiceController::class, 'generatePDF']);
Route::post('/email/send-invoice', [InvoiceController::class, 'sendEmail']);
Route::post('/pos-invoices', [InvoiceController::class, 'createPosInvoice']); // POS sin autenticación

// ==================== RUTAS DE PRUEBA NOTIFICACIONES (SIN AUTH) ====================
Route::get('/notifications/test-counts', [App\Http\Controllers\Api\NotificationController::class, 'getCounts']);
Route::post('/notifications/test-mark-movements', [App\Http\Controllers\Api\NotificationController::class, 'markMovementsAsViewed']);
Route::post('/notifications/test-mark-alerts', [App\Http\Controllers\Api\NotificationController::class, 'markAlertsAsViewed']);

// Ruta de prueba
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API funcionando correctamente',
        'timestamp' => now(),
        'version' => '1.1.0'
    ]);
});
