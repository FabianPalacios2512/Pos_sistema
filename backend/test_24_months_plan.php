<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use Carbon\Carbon;

// Buscar un tenant para actualizar (usaremos 'nano')
$tenantId = 'nano';

$tenant = Tenant::find($tenantId);

if (!$tenant) {
    echo "❌ Tenant '$tenantId' no encontrado\n";
    exit(1);
}

echo "🔍 Tenant actual: {$tenant->id}\n";
echo "📊 Plan actual: {$tenant->plan}\n";
echo "📅 Fecha creación: {$tenant->created_at}\n";
echo "⏰ Expiración actual: " . ($tenant->subscription_ends_at ?? 'N/A') . "\n\n";

// Simular una suscripción de 24 meses desde HOY
$now = Carbon::now();
$subscriptionEnd = $now->copy()->addMonths(24);

$tenant->update([
    'plan' => 'enterprise', // Plan Enterprise por 24 meses
    'subscription_ends_at' => $subscriptionEnd,
    'plan_pending' => false
]);

echo "✅ Plan actualizado a suscripción de 24 MESES\n\n";
echo "📊 Nuevo Plan: {$tenant->plan}\n";
echo "📅 Fecha actual: {$now->format('Y-m-d H:i:s')}\n";
echo "⏰ Expiración: {$subscriptionEnd->format('Y-m-d H:i:s')}\n";
echo "📆 Días restantes: " . $now->diffInDays($subscriptionEnd) . " días\n";
echo "📆 Meses restantes: " . floor($now->diffInDays($subscriptionEnd) / 30) . " meses\n\n";

echo "🎯 Ahora ve a Settings → Mi Plan para ver el resultado\n";
echo "🌐 URL: http://nano.localhost:3000/settings\n";
