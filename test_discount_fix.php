<?php
require_once 'backend/vendor/autoload.php';

// Configuración de Laravel sin HTTP
$app = require_once 'backend/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 PRUEBA DE CORRECCIÓN DE DESCUENTOS\n";
echo "=====================================\n\n";

// 1. Verificar estado actual
echo "📊 Estado actual de descuentos:\n";
$discounts = \App\Models\Discount::with('usages')->get();
foreach ($discounts as $discount) {
    $usage_count = $discount->usages->count();
    echo "  - {$discount->code}: {$usage_count}/{$discount->usage_limit}\n";
}
echo "\n";

// 2. Simular una venta con descuento usando datos que el frontend enviaría
echo "💰 Simulando venta POS con descuento BIENVENIDA10...\n";

$testData = [
    'type' => 'invoice',
    'customer_id' => 7,
    'date' => '2025-11-16',
    'due_date' => '2025-12-16',
    'subtotal' => 10000.00,
    'tax_amount' => 0.00,
    'total' => 9000.00,  // Con descuento aplicado
    'payment_method' => 'efectivo',
    'notes' => 'Venta POS - Prueba de descuento',
    'items' => [
        [
            'product_id' => 1,
            'product_name' => 'Producto de Prueba',
            'quantity' => 1,
            'unit_price' => 10000.00
        ]
    ],
    'applied_discount' => [
        'code' => 'BIENVENIDA10',
        'discount_id' => 1,  // ID del descuento BIENVENIDA10
        'amount' => 1000.00
    ],
    'user_identifier' => 'test_session_' . time(),
    'customer_email' => null,
    'customer_phone' => null
];

// 3. Hacer petición HTTP al endpoint del POS
try {
    $url = 'http://localhost:8000/api/pos-invoices';
    $postdata = json_encode($testData);
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            'content' => $postdata
        ]
    ]);
    
    $result = file_get_contents($url, false, $context);
    
    if ($result === false) {
        throw new Exception('Error al conectar con el API');
    }
    
    $response = json_decode($result, true);
    
    if (isset($response['success']) && $response['success']) {
        echo "✅ Factura creada exitosamente\n";
        echo "   ID: {$response['data']['id']}\n";
        echo "   Número: {$response['data']['number']}\n";
        echo "\n";
    } else {
        echo "❌ Error al crear factura: " . json_encode($response) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// 4. Verificar si se registró el uso del descuento
echo "🔍 Verificando registro de uso de descuentos:\n";
$discounts = \App\Models\Discount::with('usages')->get();
foreach ($discounts as $discount) {
    $usage_count = $discount->usages->count();
    echo "  - {$discount->code}: {$usage_count}/{$discount->usage_limit}";
    
    if ($discount->code === 'BIENVENIDA10') {
        $latestUsage = $discount->usages->last();
        if ($latestUsage) {
            echo " (último uso: {$latestUsage->created_at->format('H:i:s')})";
        }
    }
    echo "\n";
}

echo "\n🏁 Prueba completada!\n";
?>