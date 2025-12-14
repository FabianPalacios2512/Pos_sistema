<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use Carbon\Carbon;

// Cambiar el tenant ID aquí
$tenantId = 'las_manchas';

$tenant = Tenant::find($tenantId);

if (!$tenant) {
    echo "❌ Tenant '$tenantId' no encontrado\n";
    exit(1);
}

echo "🔍 Tenant: {$tenant->id}\n";
echo "📊 Plan actual: {$tenant->plan}\n\n";

// Crear suscripción de 12 meses desde HOY
$now = Carbon::now();
$subscriptionEnd = $now->copy()->addMonths(12);

$tenant->update([
    'plan' => 'premium', // o 'enterprise' si quieres
    'subscription_ends_at' => $subscriptionEnd,
    'plan_pending' => false
]);

echo "✅ Plan actualizado a 12 MESES\n\n";
echo "📅 Fecha inicio: {$now->format('Y-m-d H:i:s')}\n";
echo "⏰ Fecha expiración: {$subscriptionEnd->format('Y-m-d H:i:s')}\n";
echo "📆 Días totales: " . $now->diffInDays($subscriptionEnd) . " días\n";
echo "📆 Meses: 12 meses\n\n";

echo "🎯 Recarga la página: http://{$tenant->id}.localhost:3000/settings\n";
