<?php

/**
 * 🧪 Script de prueba para sistema de emails
 *
 * Ejecutar: php backend/test_email_system.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\EmailService;

echo "🧪 PRUEBA DE SISTEMA DE EMAILS - 105 POS\n";
echo "==========================================\n\n";

// Test 1: Email de Bienvenida
echo "📧 Test 1: Email de Bienvenida\n";
echo "-------------------------------\n";

$result1 = EmailService::sendWelcomeEmail([
    'email' => '105pos@gmail.com', // Tu email de prueba
    'name' => 'Usuario de Prueba',
    'business_name' => 'Mi Tienda Demo',
    'subdomain' => 'mitienda',
    'password' => 'temporal123',
    'plan' => 'professional'
]);

if ($result1) {
    echo "✅ Email de bienvenida enviado exitosamente\n";
} else {
    echo "❌ Error enviando email de bienvenida\n";
}

echo "\n";

// Test 2: Email de Password Reset
echo "🔐 Test 2: Email de Password Reset\n";
echo "-----------------------------------\n";

$result2 = EmailService::sendPasswordResetEmail([
    'email' => '105pos@gmail.com',
    'name' => 'Usuario de Prueba',
    'token' => 'test-token-12345',
    'expires_at' => now()->addHour()
]);

if ($result2) {
    echo "✅ Email de password reset enviado exitosamente\n";
} else {
    echo "❌ Error enviando email de password reset\n";
}

echo "\n";

// Test 3: Email de Password Changed
echo "✅ Test 3: Email de Password Changed\n";
echo "-------------------------------------\n";

$result3 = EmailService::sendPasswordChangedEmail([
    'email' => '105pos@gmail.com',
    'name' => 'Usuario de Prueba',
    'changed_at' => now()->format('Y-m-d H:i:s')
]);

if ($result3) {
    echo "✅ Email de confirmación enviado exitosamente\n";
} else {
    echo "❌ Error enviando email de confirmación\n";
}

echo "\n==========================================\n";
echo "🎯 Revisa tu email: 105pos@gmail.com\n";
echo "📁 Revisa logs: backend/storage/logs/laravel.log\n";
