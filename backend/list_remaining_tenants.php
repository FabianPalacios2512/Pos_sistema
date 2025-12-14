<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;

echo "📊 TENANTS RESTANTES EN EL SISTEMA\n";
echo str_repeat('=', 70) . "\n\n";

$tenants = Tenant::all();

foreach ($tenants as $tenant) {
    echo "🏢 ID: {$tenant->id}\n";
    echo "   Nombre: {$tenant->business_name}\n";
    echo "   Plan: {$tenant->plan}\n";
    echo "   Creado: {$tenant->created_at}\n";

    // Intentar obtener usuarios
    try {
        $tenant->run(function () use ($tenant) {
            $users = \DB::table('users')->get();
            echo "   👥 Usuarios: {$users->count()}\n";
            foreach ($users as $user) {
                echo "      - {$user->name} ({$user->email})\n";
            }
        });
    } catch (\Exception $e) {
        echo "   ⚠️  Base de datos no existe\n";
    }

    echo "\n";
}

echo str_repeat('=', 70) . "\n";
echo "Total: " . $tenants->count() . " tenants\n";
