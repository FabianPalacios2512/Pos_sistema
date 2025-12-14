<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap de Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Discount;
use App\Models\DiscountUsage;

echo "🔍 Verificando descuento TEST2025...\n\n";

// Buscar el descuento TEST2025
$discount = Discount::where('code', 'TEST2025')->first();

if (!$discount) {
    echo "❌ ERROR: Descuento TEST2025 no encontrado\n";
    exit;
}

echo "✅ Descuento encontrado:\n";
echo "   - ID: {$discount->id}\n";
echo "   - Código: {$discount->code}\n";
echo "   - Nombre: {$discount->name}\n";
echo "   - Límite de uso: {$discount->usage_limit}\n";
echo "   - Usos actuales: {$discount->used_count}\n";
echo "   - Múltiples usos por usuario: " . ($discount->allow_multiple_uses_per_user ? 'Sí' : 'No') . "\n\n";

// Verificar usos registrados
$usages = DiscountUsage::where('discount_id', $discount->id)->get();

echo "📊 Usos registrados ({$usages->count()}):\n";
foreach ($usages as $usage) {
    echo "   - Usuario: {$usage->user_identifier}\n";
    echo "   - Email: " . ($usage->customer_email ?? 'N/A') . "\n";
    echo "   - Teléfono: " . ($usage->customer_phone ?? 'N/A') . "\n";
    echo "   - Factura: " . ($usage->invoice_number ?? 'N/A') . "\n";
    echo "   - Monto: $" . number_format($usage->discount_amount, 2) . "\n";
    echo "   - Fecha: {$usage->used_at}\n";
    echo "   ---\n";
}

if ($usages->isEmpty()) {
    echo "   📝 No hay usos registrados aún.\n\n";
}

// Simular validación de uso
$testUserIdentifier = 'pos_session_test_123';
$testCustomerEmail = 'test@example.com';
$testCustomerPhone = '3001234567';

echo "🧪 Simulando validación de uso para usuario de prueba:\n";
echo "   - Identificador: {$testUserIdentifier}\n";
echo "   - Email: {$testCustomerEmail}\n";
echo "   - Teléfono: {$testCustomerPhone}\n\n";

$canUse = $discount->canBeUsedBy($testUserIdentifier, $testCustomerEmail, $testCustomerPhone);
$usageCount = $discount->getUsageCountBy($testUserIdentifier, $testCustomerEmail, $testCustomerPhone);

echo "   ✅ ¿Puede usar el descuento?: " . ($canUse ? 'SÍ' : 'NO') . "\n";
echo "   📊 Veces que ha usado: {$usageCount}\n\n";

// Mostrar límites restantes
if ($discount->usage_limit) {
    $remaining = $discount->usage_limit - $discount->used_count;
    echo "📈 Límites globales:\n";
    echo "   - Usos restantes globales: {$remaining}/{$discount->usage_limit}\n";

    if ($remaining <= 0) {
        echo "   ⚠️  AGOTADO: No quedan usos disponibles\n";
    } elseif ($remaining <= 2) {
        echo "   ⚠️  CRÍTICO: Solo quedan {$remaining} usos\n";
    } else {
        echo "   ✅ DISPONIBLE: {$remaining} usos restantes\n";
    }
}

echo "\n";

// Verificar datos de test que se crearon previamente
echo "🔍 Verificando descuentos de prueba creados:\n";
$testDiscounts = Discount::whereIn('code', ['TEST2025', 'MULTI2025'])->get();

foreach ($testDiscounts as $testDiscount) {
    echo "   - {$testDiscount->code}: {$testDiscount->used_count}/{$testDiscount->usage_limit} usos\n";
}

echo "\n✅ Verificación completa!\n";

?>
