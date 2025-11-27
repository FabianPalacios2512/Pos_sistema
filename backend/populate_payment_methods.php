<?php

use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Populating Payment Methods...\n";

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
        'description' => 'Pago con tarjeta',
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
        'description' => 'Transferencia electrónica',
        'icon' => '🏦',
        'active' => true,
        'requires_reference' => true,
        'sort_order' => 3,
        'config' => [
            'banks' => ['Bancolombia', 'Nequi', 'Daviplata', 'BBVA']
        ]
    ],
    [
        'name' => 'Nequi',
        'code' => 'nequi',
        'description' => 'Pago por Nequi',
        'icon' => '📱',
        'active' => true,
        'requires_reference' => true,
        'sort_order' => 4,
        'config' => []
    ],
    [
        'name' => 'Daviplata',
        'code' => 'daviplata',
        'description' => 'Pago por Daviplata',
        'icon' => '📱',
        'active' => true,
        'requires_reference' => true,
        'sort_order' => 5,
        'config' => []
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
