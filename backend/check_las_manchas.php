<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use Carbon\Carbon;

$tenant = Tenant::where('id', 'las-manchas')->orWhere('business_name', 'LIKE', '%manchas%')->first();

if (!$tenant) {
    echo "❌ Tenant no encontrado\n";
    exit(1);
}

$now = Carbon::now();
$createdAt = Carbon::parse($tenant->created_at);
$expiresAt = $tenant->subscription_ends_at ? Carbon::parse($tenant->subscription_ends_at) : null;

echo "🔍 INFORMACIÓN DEL TENANT\n";
echo str_repeat('=', 50) . "\n";
echo "ID: {$tenant->id}\n";
echo "Nombre: {$tenant->business_name}\n";
echo "Plan: {$tenant->plan}\n";
echo "Estado: " . ($tenant->plan_pending ? '⏳ Pendiente' : '✅ Activo') . "\n";
echo "\n";

echo "📅 FECHAS\n";
echo str_repeat('=', 50) . "\n";
echo "Creación: " . $createdAt->format('Y-m-d H:i:s') . "\n";
echo "Expiración: " . ($expiresAt ? $expiresAt->format('Y-m-d H:i:s') : 'N/A') . "\n";
echo "Fecha actual: " . $now->format('Y-m-d H:i:s') . "\n";
echo "\n";

if ($expiresAt) {
    $totalDays = $createdAt->diffInDays($expiresAt);
    $remainingDays = $now->diffInDays($expiresAt, false);
    $elapsedDays = $totalDays - max(0, $remainingDays);

    echo "⏰ DURACIÓN\n";
    echo str_repeat('=', 50) . "\n";
    echo "Duración total: {$totalDays} días (" . round($totalDays / 30, 1) . " meses)\n";
    echo "Días transcurridos: {$elapsedDays} días\n";
    echo "Días restantes: {$remainingDays} días\n";
    echo "Meses restantes: " . floor($remainingDays / 30) . " meses y " . ($remainingDays % 30) . " días\n";
    echo "\n";

    $percentage = ($remainingDays / $totalDays) * 100;
    echo "📊 PROGRESO\n";
    echo str_repeat('=', 50) . "\n";
    echo "Progreso restante: " . round($percentage, 2) . "%\n";

    if ($totalDays >= 300 && $totalDays <= 400) {
        echo "✅ Esto ES una suscripción de 12 MESES (1 año)\n";
    } elseif ($totalDays >= 28 && $totalDays <= 31) {
        echo "✅ Esto ES una suscripción de 1 MES\n";
    } elseif ($totalDays >= 6 && $totalDays <= 8) {
        echo "✅ Esto ES una prueba gratuita de 7 DÍAS\n";
    } else {
        echo "⚠️  Duración personalizada: {$totalDays} días\n";
    }
}
