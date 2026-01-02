<?php
/**
 * Test completo de ePayco usando el SDK oficial
 * Prueba los 3 planes: Basic ($25k), Premium ($60k), Corporativo ($150k)
 */

require __DIR__ . '/vendor/autoload.php';

use Epayco\Epayco;

echo "🧪 TEST COMPLETO EPAYCO - SDK OFICIAL\n";
echo "====================================\n\n";

// Credenciales CORRECTAS
$apiKey = '1569644'; // P_CUST_ID_CLIENTE
$privateKey = 'bbc93c88d4780f0898bbe4e9ed29e6bc8e33ca72'; // P_KEY
$publicKey = '2943652c673afffaa5b7b67829f00a0c'; // PUBLIC_KEY (para checkout)
$test = true;

echo "📋 Configuración:\n";
echo "Public Key: {$publicKey}\n";
echo "Test Mode: " . ($test ? 'SI' : 'NO') . "\n\n";

// Inicializar SDK
try {
    $epayco = new Epayco([
        "apiKey" => $apiKey,
        "privateKey" => $privateKey,
        "lenguage" => "ES",
        "test" => $test
    ]);
    
    echo "✅ SDK inicializado correctamente\n\n";
    
} catch (Exception $e) {
    echo "❌ Error al inicializar SDK: " . $e->getMessage() . "\n";
    exit(1);
}

// Planes a probar
$planes = [
    [
        'nombre' => 'Basic',
        'monto' => '25000',
        'descripcion' => 'Plan Basic - 105 POS'
    ],
    [
        'nombre' => 'Premium',
        'monto' => '60000',
        'descripcion' => 'Plan Premium - 105 POS'
    ],
    [
        'nombre' => 'Corporativo',
        'monto' => '150000',
        'descripcion' => 'Plan Corporativo - 105 POS'
    ]
];

echo "🔄 Probando los 3 planes...\n";
echo "====================================\n\n";

foreach ($planes as $index => $plan) {
    $numero = $index + 1;
    echo "📦 TEST {$numero}/3: {$plan['nombre']} (\${$plan['monto']} COP)\n";
    echo "-----------------------------------\n";
    
    $reference = "test_{$plan['nombre']}_" . time() . "_" . rand(100, 999);
    
    try {
        // Crear link de pago
        $linkData = [
            "name" => $plan['descripcion'],
            "description" => $plan['descripcion'],
            "invoice" => $reference,
            "currency" => "cop",
            "amount" => $plan['monto'],
            "tax_base" => "0",
            "tax" => "0",
            "country" => "co",
            "lang" => "es",
            "external" => "true",
            "extra1" => "fabiana",
            "extra2" => strtolower($plan['nombre']),
            "extra3" => "test_sdk",
            "confirmation" => "https://105pos.pro/api/epayco/webhook",
            "response" => "http://localhost:3000/payment/success",
            "name_billing" => "Test Cliente SDK",
            "address_billing" => "Calle Test 123",
            "type_doc_billing" => "cc",
            "mobilephone_billing" => "3000000000",
            "number_doc_billing" => "1000000000",
            "email_billing" => "test@105pos.pro"
        ];
        
        $response = $epayco->bank->create($linkData);
        
        if ($response->success) {
            echo "✅ LINK CREADO EXITOSAMENTE\n";
            echo "URL de pago: " . ($response->data->urlbanco ?? 'N/A') . "\n";
            echo "Ref: {$reference}\n";
            
            // Guardar para prueba manual
            $filename = __DIR__ . "/epayco_test_{$plan['nombre']}.txt";
            file_put_contents($filename, $response->data->urlbanco ?? 'N/A');
            echo "💾 URL guardada en: {$filename}\n";
            
        } else {
            echo "❌ ERROR al crear link\n";
            echo "Respuesta: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ EXCEPCIÓN: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    sleep(1); // Pausa de 1 segundo entre pruebas
}

echo "====================================\n";
echo "🏁 TEST COMPLETADO\n\n";

echo "📝 INSTRUCCIONES:\n";
echo "1. Revisa los archivos generados: epayco_test_*.txt\n";
echo "2. Abre cada URL en tu navegador\n";
echo "3. Usa la tarjeta de prueba: 4575 6231 0548 2283\n";
echo "4. CVV: 123, Fecha: 12/25\n";
echo "5. Verifica cuál plan acepta el pago\n\n";

echo "💡 Si TODOS fallan:\n";
echo "   → Tu cuenta de ePayco necesita configuración\n";
echo "   → Contacta a soporte: soporte@epayco.co\n";
echo "   → Pide activar modo test con estos montos\n";
