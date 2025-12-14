<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "\n🚀 Creando Super Admin en base de datos central...\n\n";

// Datos del super admin
$name = 'Super Admin';
$email = 'superadmin@pos.com';
$password = 'superadmin123';
$role = 'superadmin';

try {
    // Verificar si ya existe
    $existing = DB::connection('mysql')->table('central_users')
        ->where('email', $email)
        ->first();

    if ($existing) {
        echo "⚠️  Ya existe un super admin con el email: {$email}\n";
        echo "📝 Actualizando contraseña...\n\n";

        DB::connection('mysql')->table('central_users')
            ->where('email', $email)
            ->update([
                'password' => Hash::make($password),
                'is_active' => true,
                'role' => $role,
                'updated_at' => now()
            ]);

        echo "✅ Contraseña actualizada exitosamente!\n\n";
    } else {
        // Crear nuevo super admin
        DB::connection('mysql')->table('central_users')->insert([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        echo "✅ Super Admin creado exitosamente!\n\n";
    }

    echo "📋 Credenciales de acceso:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📧 Email:    {$email}\n";
    echo "🔑 Password: {$password}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "🌐 Accede a: http://localhost:3000/login\n";
    echo "🎯 Después serás redirigido a: /admin/god-mode\n\n";

} catch (\Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n\n";
    echo "🔧 Verifica que:\n";
    echo "   1. La migración de central_users esté ejecutada\n";
    echo "   2. La conexión 'mysql' esté configurada en config/database.php\n";
    echo "   3. Las credenciales de la base de datos sean correctas\n\n";
}
