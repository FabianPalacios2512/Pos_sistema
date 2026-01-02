<?php
/**
 * Consultar estado de transacción en ePayco
 */

require __DIR__ . '/vendor/autoload.php';

echo "🔍 CONSULTAR TRANSACCIÓN EPAYCO\n";
echo "================================\n\n";

// Referencia de una transacción rechazada (cambia por la última que probaste)
$reference = $argv[1] ?? 'upgrade_fabiana_1767297611588';

echo "📋 Referencia a consultar: {$reference}\n\n";

// Credenciales
$publicKey = '2943652c673afffaa5b7b67829f00a0c';
$privateKey = 'e893ca6c08e3caeab2da3634a25de91c';

$client = new \GuzzleHttp\Client([
    'base_uri' => 'https://secure.epayco.co/',
    'timeout' => 30,
    'verify' => false
]);

try {
    echo "🚀 Consultando transacción...\n";
    
    // API de validación de transacciones
    $response = $client->request('GET', "validation/v1/reference/{$reference}", [
        'headers' => [
            'Authorization' => 'Bearer ' . $publicKey,
            'Content-Type' => 'application/json'
        ]
    ]);
    
    $body = json_decode($response->getBody()->getContents(), true);
    
    echo "\n✅ Respuesta de ePayco:\n";
    echo json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    
    if (isset($body['data'])) {
        $data = $body['data'];
        
        echo "📊 DETALLES DE LA TRANSACCIÓN:\n";
        echo "Estado: " . ($data['x_transaction_state'] ?? 'N/A') . "\n";
        echo "Respuesta: " . ($data['x_response'] ?? 'N/A') . "\n";
        echo "Código respuesta: " . ($data['x_cod_response'] ?? 'N/A') . "\n";
        echo "Motivo rechazo: " . ($data['x_response_reason_text'] ?? 'N/A') . "\n";
        echo "Monto: $" . number_format($data['x_amount'] ?? 0, 0) . " COP\n";
        echo "Banco: " . ($data['x_bank_name'] ?? 'N/A') . "\n";
        echo "Franquicia: " . ($data['x_franchise'] ?? 'N/A') . "\n";
        
        // Análisis del rechazo
        echo "\n🔍 ANÁLISIS DEL RECHAZO:\n";
        $codResponse = $data['x_cod_response'] ?? 0;
        
        if ($codResponse == 3) {
            echo "❌ Transacción RECHAZADA\n";
            echo "Razón: " . ($data['x_response_reason_text'] ?? 'Tarjeta restringida') . "\n";
            echo "\n💡 POSIBLES CAUSAS:\n";
            echo "1. Monto no permitido en modo test para tu cuenta\n";
            echo "2. Tarjeta de prueba no compatible con tu configuración de ePayco\n";
            echo "3. Límites de transacción excedidos\n";
            echo "4. Necesitas contactar a soporte de ePayco para habilitar estos montos\n";
        }
    }
    
} catch (\Exception $e) {
    echo "\n❌ ERROR:\n";
    echo $e->getMessage() . "\n";
}

echo "\n================================\n";
echo "🏁 CONSULTA FINALIZADA\n";
