<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap de Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Discount;
use App\Models\DiscountUsage;

echo "🧪 SIMULANDO USO DE DESCUENTO TEST2025...\n\n";

// Buscar el descuento TEST2025
$discount = Discount::where('code', 'TEST2025')->first();

if (!$discount) {
    echo "❌ ERROR: Descuento TEST2025 no encontrado\n";
    exit;
}

echo "📋 Estado inicial del descuento:\n";
echo "   - Código: {$discount->code}\n";
echo "   - Límite: {$discount->usage_limit}\n";
echo "   - Usado: {$discount->used_count}\n";
echo "   - Restante: " . ($discount->usage_limit - $discount->used_count) . "\n\n";

// Simular datos de una venta POS
$testData = [
    'userIdentifier' => 'pos_session_' . time(),
    'customerEmail' => 'cliente@test.com',
    'customerPhone' => '3001234567',
    'discountAmount' => 50.00,
    'invoiceNumber' => 'FV-TEST-' . time(),
    'metadata' => [
        'user_agent' => 'POS System Test',
        'ip_address' => '127.0.0.1',
        'timestamp' => now()->toISOString()
    ]
];

echo "🔍 Datos de prueba:\n";
echo "   - Usuario: {$testData['userIdentifier']}\n";
echo "   - Email: {$testData['customerEmail']}\n";
echo "   - Teléfono: {$testData['customerPhone']}\n";
echo "   - Monto: \${$testData['discountAmount']}\n";
echo "   - Factura: {$testData['invoiceNumber']}\n\n";

// Verificar si puede usar el descuento
$canUse = $discount->canBeUsedBy(
    $testData['userIdentifier'],
    $testData['customerEmail'],
    $testData['customerPhone']
);

echo "✅ ¿Puede usar el descuento?: " . ($canUse ? 'SÍ' : 'NO') . "\n";

if (!$canUse) {
    echo "❌ El usuario ya usó este descuento. Abortando prueba.\n";
    exit;
}

echo "\n🚀 Registrando uso del descuento...\n";

try {
    // Registrar el uso del descuento (igual que en el controlador)
    $discount->recordUsage(
        $testData['discountAmount'],
        $testData['userIdentifier'],
        $testData['customerEmail'],
        $testData['customerPhone'],
        $testData['invoiceNumber'],
        $testData['metadata']
    );

    echo "✅ Uso registrado exitosamente!\n\n";

} catch (Exception $e) {
    echo "❌ ERROR al registrar uso: " . $e->getMessage() . "\n";
    echo "📍 Trace: " . $e->getTraceAsString() . "\n";
    exit;
}

// Verificar el estado después del registro
$discount->refresh(); // Refrescar desde la BD

echo "📊 Estado después del registro:\n";
echo "   - Límite: {$discount->usage_limit}\n";
echo "   - Usado: {$discount->used_count}\n";
echo "   - Restante: " . ($discount->usage_limit - $discount->used_count) . "\n\n";

// Verificar usos registrados en la tabla discount_usages
$usages = DiscountUsage::where('discount_id', $discount->id)->get();

echo "📝 Usos registrados en discount_usages ({$usages->count()}):\n";
foreach ($usages as $usage) {
    echo "   - ID: {$usage->id}\n";
    echo "   - Usuario: {$usage->user_identifier}\n";
    echo "   - Monto: \${$usage->discount_amount}\n";
    echo "   - Factura: {$usage->invoice_number}\n";
    echo "   - Fecha: {$usage->used_at}\n";
    echo "   ---\n";
}

// Verificar si ahora no puede usar el descuento de nuevo
$canUseAgain = $discount->canBeUsedBy(
    $testData['userIdentifier'],
    $testData['customerEmail'],
    $testData['customerPhone']
);

echo "\n🔄 ¿Puede usar el descuento de nuevo?: " . ($canUseAgain ? 'SÍ' : 'NO') . "\n";

if (!$canUseAgain) {
    echo "✅ CORRECTO: El sistema está bloqueando usos repetidos\n";
} else {
    echo "⚠️  PROBLEMA: El sistema permite uso repetido cuando no debería\n";
}

echo "\n✅ Prueba completa!\n";

?>
