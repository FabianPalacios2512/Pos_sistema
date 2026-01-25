<?php
/**
 * Test directo de ePayco - Crear transacción de prueba
 * Este script prueba directamente con la API de ePayco sin pasar por el frontend
 */

require __DIR__ . '/vendor/autoload.php';

echo "🧪 TEST DIRECTO EPAYCO - CREANDO TRANSACCIÓN\n";
echo "============================================\n\n";

// ✅ Credenciales de PRODUCCIÓN - Actualizadas 24/01/2026
$publicKey = 'de4263d3e7094669c4d837ad7dadb69e';
$privateKey = 'd189d75b9ab72d6d2541ecbf97051ed8'; // PRIVATE_KEY de producción

echo "📋 Configuración:\n";
echo "Public Key: {$publicKey}\n";
echo "Private Key: " . substr($privateKey, 0, 10) . "...\n\n";

// Datos de la transacción de prueba
$data = [
    // Datos del comercio
    'public_key' => $publicKey,
    
    // Datos de la transacción
    'name' => 'Test Plan Premium',
    'description' => 'Prueba de pago - Plan Premium',
    'invoice' => 'TEST_' . time(),
    'currency' => 'cop',
    'amount' => '60000', // Probamos con Premium primero
    'tax_base' => '0',
    'tax' => '0',
    'country' => 'co',
    'lang' => 'es',
    
    // URLs de respuesta
    'external' => 'true',
    'response' => 'http://localhost:3000/payment/success',
    'confirmation' => 'https://105pos.pro/api/epayco/webhook',
    
    // Datos del cliente
    'name_billing' => 'Test Cliente',
    'address_billing' => 'Calle 123',
    'type_doc_billing' => 'cc',
    'mobilephone_billing' => '3000000000',
    'number_doc_billing' => '1000000000',
    
    // Test mode
    'test' => 'true',
    
    // Extras
    'extra1' => 'fabiana',
    'extra2' => 'premium',
    'extra3' => 'test_direct'
];

echo "💳 Datos de la transacción:\n";
echo "Monto: \$" . number_format($data['amount'], 0) . " COP\n";
echo "Invoice: {$data['invoice']}\n";
echo "Plan: {$data['extra2']}\n\n";

// Crear cliente HTTP
$client = new \GuzzleHttp\Client([
    'base_uri' => 'https://secure.epayco.co/',
    'timeout' => 30,
    'verify' => false
]);

try {
    echo "🚀 Enviando petición a ePayco...\n";
    
    // Endpoint para crear link de pago (Payment Link API)
    $response = $client->request('POST', 'paymentlink/create', [
        'json' => $data,
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]
    ]);
    
    $statusCode = $response->getStatusCode();
    $body = json_decode($response->getBody()->getContents(), true);
    
    echo "\n✅ Respuesta recibida (HTTP {$statusCode}):\n";
    echo json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    
    if (isset($body['success']) && $body['success']) {
        if (isset($body['data']['urlbanco'])) {
            echo "🎉 ¡LINK DE PAGO CREADO EXITOSAMENTE!\n";
            echo "URL: {$body['data']['urlbanco']}\n\n";
            echo "👉 Abre esta URL en tu navegador para completar el pago de prueba.\n";
        } else {
            echo "⚠️ Respuesta exitosa pero sin URL de pago.\n";
        }
    } else {
        echo "❌ Error en la respuesta de ePayco:\n";
        echo isset($body['message']) ? "Mensaje: {$body['message']}\n" : "Sin mensaje de error\n";
    }
    
} catch (\GuzzleHttp\Exception\RequestException $e) {
    echo "\n❌ ERROR EN LA PETICIÓN HTTP:\n";
    echo "Mensaje: " . $e->getMessage() . "\n\n";
    
    if ($e->hasResponse()) {
        $response = $e->getResponse();
        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        
        echo "HTTP Status: {$statusCode}\n";
        echo "Respuesta:\n{$body}\n";
    }
    
} catch (\Exception $e) {
    echo "\n❌ ERROR GENERAL:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n============================================\n";
echo "🏁 TEST FINALIZADO\n";
