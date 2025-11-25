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
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rutas públicas (sin autenticación)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/auth/validate-admin', [AuthController::class, 'validateAdmin']);

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

// RUTAS OPTIMIZADAS PARA PRODUCCIÓN (sin auth para pruebas)
Route::get('/optimized/dashboard', [\App\Http\Controllers\Api\OptimizedDashboardController::class, 'getDashboardData']);
Route::get('/optimized/recent-transactions', [\App\Http\Controllers\Api\OptimizedDashboardController::class, 'getRecentTransactions']);
Route::get('/optimized/metrics', [\App\Http\Controllers\Api\OptimizedDashboardController::class, 'getMainMetrics']);
Route::post('/optimized/clear-cache', [\App\Http\Controllers\Api\OptimizedDashboardController::class, 'clearCache']);

// Dashboard ventas hoy en hora Colombia (sin auth para pruebas)
Route::get('/dashboard/ventas-hoy', [\App\Http\Controllers\DashboardController::class, 'ventasHoy']);

// Rutas protegidas (requieren autenticación)
// ===== RUTAS AUTENTICADAS =====
Route::middleware(['auth:sanctum'])->group(function () {

    // Autenticación
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Configuración del Sistema
    Route::get('/system-settings', [SystemSettingsController::class, 'index']);
    Route::put('/system-settings', [SystemSettingsController::class, 'update']);
    Route::get('/system-settings/next-invoice-number', [SystemSettingsController::class, 'getNextInvoiceNumber']);
    Route::post('/system-settings/reset', [SystemSettingsController::class, 'reset']);

    // Descuentos y Promociones
    Route::apiResource('discounts', DiscountsController::class);
    Route::get('/discounts-generate-code', [DiscountsController::class, 'generateCode']);

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
    Route::apiResource('products', ProductController::class);

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
    Route::apiResource('customers', CustomerController::class);

    // Proveedores
    Route::apiResource('suppliers', SupplierController::class);
    Route::put('/suppliers/{id}/toggle-status', [SupplierController::class, 'toggleStatus']);

    // Ventas y Cotizaciones
    Route::apiResource('sales', SalesController::class);
    Route::get('/sales/{sale}/items', [SalesController::class, 'items']);
    Route::post('/sales/{sale}/refund', [SalesController::class, 'refund']);

    // Facturas
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

    // ==================== AI CHAT ====================
    Route::post('/ai/chat', [\App\Http\Controllers\Api\AIController::class, 'chat']);

    // ==================== AI ACTIONS (Acciones ejecutables) ====================
    Route::post('/ai/actions/create-discount', [\App\Http\Controllers\Api\AIActionsController::class, 'createDiscount']);
    Route::post('/ai/actions/send-bulk-whatsapp', [\App\Http\Controllers\Api\AIActionsController::class, 'sendBulkWhatsApp']);
    Route::post('/ai/actions/create-campaign', [\App\Http\Controllers\Api\AIActionsController::class, 'createCampaign']);
    // ==================== FIN AI ====================

});

// ==================== RUTAS POS (SIN AUTH) ====================
Route::post('/discounts/validate-code', [DiscountsController::class, 'validateCode']);
Route::post('/discounts/record-usage', [DiscountsController::class, 'recordUsage']);

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
        'version' => '1.0.0'
    ]);
});
