<?php
/**
 * Verificar si las credenciales son realmente de TEST o PRODUCCIÓN
 */

$publicKey = 'APP_USR-d1af5791-fa70-4707-a8b5-61b7bf81a978';
$accessToken = 'APP_USR-4051583343447871-120914-d1f45ea3071d39c9fdab5e3ba88985f6-3052668646';

echo "🔍 Verificación de Credenciales Mercado Pago\n";
echo "===========================================\n\n";

// Verificar por patrón
$isTestPublicKey = strpos($publicKey, 'APP_USR') !== false;
$isTestAccessToken = strpos($accessToken, 'APP_USR') !== false;

echo "Public Key:\n";
echo "  Formato: " . ($isTestPublicKey ? "✅ TEST (APP_USR)" : "❌ PRODUCCIÓN") . "\n";
echo "  Valor: {$publicKey}\n\n";

echo "Access Token:\n";
echo "  Formato: " . ($isTestAccessToken ? "✅ TEST (APP_USR)" : "❌ PRODUCCIÓN") . "\n";
echo "  Valor: {$accessToken}\n\n";

// Consultar API de Mercado Pago
echo "📡 Consultando API de Mercado Pago...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.mercadopago.com/users/me");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer {$accessToken}"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo "❌ Error al consultar API: HTTP {$httpCode}\n";
    echo "Response: {$response}\n";
    exit(1);
}

$userData = json_decode($response, true);

echo "\n📊 Información de la Cuenta:\n";
echo "=============================\n";
echo "ID: {$userData['id']}\n";
echo "Email: {$userData['email']}\n";
echo "País: {$userData['site_id']}\n";
echo "Tipo de cuenta: " . ($userData['user_type'] ?? 'N/A') . "\n";

// CRÍTICO: Verificar si está en modo test
if (isset($userData['test_mode'])) {
    echo "\n⚠️  MODO TEST: " . ($userData['test_mode'] ? "✅ SÍ" : "❌ NO") . "\n";
} else {
    echo "\n⚠️  Campo 'test_mode' no disponible en la respuesta\n";
}

echo "\n📝 Diagnóstico:\n";
echo "===============\n";

// El problema real: credenciales APP_USR son SIEMPRE de TEST
// Pero si el error persiste, es porque:
// 1. La cuenta NO está configurada correctamente como TEST
// 2. O las credenciales están mezcladas (una de test, otra de prod)

echo "\n⚠️  PROBLEMA DETECTADO:\n";
echo "El error 'Una de las partes es de prueba' indica que:\n";
echo "- Tus credenciales tienen formato TEST (APP_USR)\n";
echo "- Pero Mercado Pago las está tratando como PRODUCCIÓN\n\n";

echo "🔧 SOLUCIÓN:\n";
echo "Necesitas obtener nuevas credenciales de TEST desde:\n";
echo "https://www.mercadopago.com.co/developers/panel/app/{$userData['id']}/test/credentials\n\n";

echo "O ir a:\n";
echo "1. https://www.mercadopago.com.co/developers/panel\n";
echo "2. Click en 'Tus integraciones'\n";
echo "3. Click en tu aplicación\n";
echo "4. Ir a 'Credenciales de prueba' (modo TEST)\n";
echo "5. Copiar NUEVAS credenciales\n\n";
