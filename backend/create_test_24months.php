<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

echo "🧪 CREANDO CUENTA DE PRUEBA - 24 MESES\n";
echo str_repeat('=', 70) . "\n\n";

// Crear tenant
$tenantId = 'test24meses';
$businessName = 'Test 24 Meses';

// Buscar si existe
$existing = Tenant::find($tenantId);
if ($existing) {
    echo "✅ Tenant ya existe, actualizando...\n";
    $tenant = $existing;
} else {
    echo "✨ Creando nuevo tenant: {$tenantId}\n";
    $tenant = null;
}

$now = Carbon::now();
$subscriptionEnd = $now->copy()->addMonths(24);

if (!$tenant) {
    $tenant = Tenant::create([
        'id' => $tenantId,
        'business_name' => $businessName,
        'plan' => 'enterprise',
        'subscription_ends_at' => $subscriptionEnd,
        'plan_pending' => false
    ]);
    echo "✅ Tenant creado exitosamente\n\n";
} else {
    $tenant->update([
        'plan' => 'enterprise',
        'subscription_ends_at' => $subscriptionEnd,
        'plan_pending' => false
    ]);
    echo "✅ Tenant actualizado a 24 meses\n\n";
}

echo "📊 INFORMACIÓN DEL TENANT\n";
echo str_repeat('=', 70) . "\n";
echo "ID: {$tenant->id}\n";
echo "Nombre: {$tenant->business_name}\n";
echo "Plan: {$tenant->plan}\n";
echo "Estado: " . ($tenant->plan_pending ? '⏳ Pendiente' : '✅ Activo') . "\n\n";

echo "📅 FECHAS\n";
echo str_repeat('=', 70) . "\n";
echo "Creación: {$tenant->created_at}\n";
echo "Expiración: {$tenant->subscription_ends_at}\n\n";

$totalDays = Carbon::parse($tenant->created_at)->diffInDays(Carbon::parse($tenant->subscription_ends_at));
$remainingDays = $now->diffInDays($subscriptionEnd);

echo "⏰ DURACIÓN\n";
echo str_repeat('=', 70) . "\n";
echo "Duración total: {$totalDays} días (" . round($totalDays / 30, 1) . " meses)\n";
echo "Días restantes: {$remainingDays} días\n";
echo "Meses restantes: " . floor($remainingDays / 30) . " meses y " . ($remainingDays % 30) . " días\n\n";

// Crear base de datos y estructura
echo "🗄️  Creando base de datos del tenant...\n";
$tenant->run(function () use ($tenant) {
    // Aquí se crea la base de datos automáticamente
    echo "✅ Base de datos creada\n";
});

// Crear usuario de prueba
echo "👤 Creando usuario administrador...\n";
$tenant->run(function () {
    DB::table('users')->insert([
        'name' => 'Admin Test 24M',
        'email' => 'admin@test24meses.com',
        'password' => bcrypt('password123'),
        'role_id' => 1, // 1 = superadmin
        'active' => 1,
        'tour_completed' => 0,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "✅ Usuario creado: admin@test24meses.com / password123\n";
});

echo "\n" . str_repeat('=', 70) . "\n";
echo "🎯 PRUEBA LA CUENTA AQUÍ:\n";
echo "🌐 URL: http://{$tenant->id}.localhost:3000\n";
echo "📧 Email: admin@test24meses.com\n";
echo "🔑 Password: password123\n";
echo "\n📊 Ve a: Configuración → Mi Plan\n";
echo "✅ Debería mostrar: \"24 meses\"\n";
