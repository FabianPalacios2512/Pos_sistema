<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use Carbon\Carbon;

echo "🧪 PROBANDO API /tenant-info\n";
echo str_repeat('=', 70) . "\n\n";

$tenant = Tenant::find('test24meses');

if (!$tenant) {
    echo "❌ Tenant no encontrado\n";
    exit(1);
}

echo "📊 DATOS DE LA BASE DE DATOS\n";
echo str_repeat('-', 70) . "\n";
echo "ID: {$tenant->id}\n";
echo "Nombre: {$tenant->business_name}\n";
echo "Plan: {$tenant->plan}\n";
echo "Creación (created_at): {$tenant->created_at}\n";
echo "Expiración (subscription_ends_at): {$tenant->subscription_ends_at}\n\n";

$now = Carbon::now();
$createdAt = Carbon::parse($tenant->created_at);
$expiresAt = Carbon::parse($tenant->subscription_ends_at);

$totalDays = $createdAt->diffInDays($expiresAt);
$remainingDays = $now->diffInDays($expiresAt);

echo "⏰ CÁLCULOS\n";
echo str_repeat('-', 70) . "\n";
echo "Duración total: {$totalDays} días\n";
echo "Días restantes: {$remainingDays} días\n";
echo "Progreso: " . round(($remainingDays / $totalDays) * 100, 2) . "%\n\n";

// Simular lo que hace el computed en Vue
if ($remainingDays < 60) {
    $formatted = "{$remainingDays} " . ($remainingDays === 1 ? 'día' : 'días');
} else {
    $months = floor($remainingDays / 30);
    $days = $remainingDays % 30;
    if ($days === 0) {
        $formatted = "{$months} " . ($months === 1 ? 'mes' : 'meses');
    } else {
        $formatted = "{$months} " . ($months === 1 ? 'mes' : 'meses') . " y {$days} " . ($days === 1 ? 'día' : 'días');
    }
}

echo "🎨 FORMATO QUE SE MOSTRARÁ\n";
echo str_repeat('-', 70) . "\n";
echo "Tiempo restante: {$formatted}\n\n";

echo "✅ El endpoint /tenant-info devolverá:\n";
echo json_encode([
    'success' => true,
    'tenant' => [
        'id' => $tenant->id,
        'business_name' => $tenant->business_name,
        'plan' => $tenant->plan,
        'subscription_ends_at' => $tenant->subscription_ends_at,
        'created_at' => $tenant->created_at,
    ]
], JSON_PRETTY_PRINT) . "\n";
