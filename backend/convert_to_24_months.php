<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use Carbon\Carbon;

$tenantId = 'javiera'; // Cambiar aquí si quieres otro tenant

$tenant = Tenant::find($tenantId);

if (!$tenant) {
    echo "❌ Tenant '{$tenantId}' no encontrado\n";
    exit(1);
}

echo "🔍 Tenant: {$tenant->id}\n";
echo "📊 Plan actual: {$tenant->plan}\n";
echo "⏰ Expiración actual: {$tenant->subscription_ends_at}\n\n";

// Actualizar a 24 meses desde HOY
$now = Carbon::now();
$subscriptionEnd = $now->copy()->addMonths(24);

$tenant->update([
    'plan' => 'enterprise', // o mantén 'premium' si prefieres
    'subscription_ends_at' => $subscriptionEnd,
    'plan_pending' => false
]);

echo "✅ Plan actualizado a 24 MESES\n\n";
echo "📅 Fecha inicio: {$now->format('Y-m-d H:i:s')}\n";
echo "⏰ Nueva expiración: {$subscriptionEnd->format('Y-m-d H:i:s')}\n";
echo "📆 Días totales: " . $now->diffInDays($subscriptionEnd) . " días\n\n";

echo "🎯 Recarga: http://{$tenant->id}.localhost:3000/settings\n";
echo "✅ Ahora debería mostrar: \"24 meses\"\n";
