<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use Carbon\Carbon;

$tenant = Tenant::where('id', 'javiera')->orWhere('business_name', 'LIKE', '%javiera%')->first();

if (!$tenant) {
    echo "❌ Tenant javiera no encontrado\n";
    echo "📊 Tenants disponibles:\n";
    foreach (Tenant::all() as $t) {
        echo "  - {$t->id} ({$t->business_name})\n";
    }
    exit(1);
}

echo "🔍 CUENTA: javiera\n";
echo str_repeat('=', 70) . "\n";
echo "ID: {$tenant->id}\n";
echo "Nombre: {$tenant->business_name}\n";
echo "Plan: {$tenant->plan}\n";
echo "Creación: {$tenant->created_at}\n";
echo "Expiración: {$tenant->subscription_ends_at}\n\n";

$createdAt = Carbon::parse($tenant->created_at);
$expiresAt = Carbon::parse($tenant->subscription_ends_at);
$now = Carbon::now();

$totalDays = $createdAt->diffInDays($expiresAt);
$remainingDays = $now->diffInDays($expiresAt);

echo "⏰ ANÁLISIS:\n";
echo "Duración total: {$totalDays} días (" . round($totalDays / 30, 1) . " meses)\n";
echo "Días restantes: {$remainingDays} días\n\n";

if ($totalDays < 60) {
    echo "❌ Esta cuenta tiene una duración de SOLO 1 MES\n";
    echo "⚠️  NO es una cuenta de 24 meses\n\n";
    echo "💡 Para convertirla a 24 meses, ejecuta:\n";
    echo "   php backend/create_12_month_plan.php\n";
    echo "   (y cambia el tenant ID a 'javiera')\n";
} else {
    echo "✅ Esta es una cuenta de larga duración\n";
}
