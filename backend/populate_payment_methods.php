<?php

use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Populating Payment Methods...\n";

// Solo 3 métodos de pago por defecto
// Creditienda se agrega automáticamente cuando está activo
$paymentMethods = [
    [
        'name' => 'Efectivo',
        'code' => 'efectivo',
        'description' => 'Pago en efectivo',
        'icon' => '💵',
        'active' => true,
        'requires_reference' => false,
        'sort_order' => 1,
        'config' => [
            'allow_change' => true,
            'currency' => 'COP'
        ]
    ],
    [
        'name' => 'Tarjeta de Crédito/Débito',
        'code' => 'tarjeta',
        'description' => 'Pago con tarjeta de crédito o débito',
        'icon' => '💳',
        'active' => true,
        'requires_reference' => true,
        'sort_order' => 2,
        'config' => [
            'require_authorization' => true,
            'fee_percentage' => 0
        ]
    ],
    [
        'name' => 'Transferencia Bancaria',
        'code' => 'transferencia',
        'description' => 'Transferencia bancaria, Nequi, Daviplata u otras plataformas',
        'icon' => '🏦',
        'active' => true,
        'requires_reference' => true,
        'sort_order' => 3,
        'config' => [
            'platforms' => ['Bancolombia', 'Nequi', 'Daviplata', 'Banco de Bogotá', 'PSE']
        ]
    ]
];

foreach ($paymentMethods as $method) {
    PaymentMethod::updateOrCreate(
        ['code' => $method['code']],
        $method
    );
    echo "Processed: " . $method['name'] . "\n";
}

echo "Done!\n";
