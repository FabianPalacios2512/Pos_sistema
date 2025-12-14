<?php

require __DIR__.'/vendor/autoload.php';

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Database\Models\Tenant;

// Inicializar Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔧 Agregando permisos de Gastos Operativos a todos los roles Administrador...\n\n";

// Permisos de expenses que deben agregarse
$expensesPermissions = [
    'expenses.view',
    'expenses.create',
    'expenses.edit',
    'expenses.delete',
    'expenses.reports'
];

// Obtener todos los tenants
$tenants = Tenant::all();

echo "🏢 Tenants encontrados: " . $tenants->count() . "\n\n";

foreach ($tenants as $tenant) {
    echo "═══════════════════════════════════════════\n";
    echo "🏢 Procesando tenant: {$tenant->id}\n";
    echo "═══════════════════════════════════════════\n\n";

    try {
        // Conectar directamente a la base de datos del tenant
        $tenantDbName = 'tenant' . $tenant->id;

        config(['database.connections.tenant' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $tenantDbName,
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
        ]]);

        DB::purge('tenant');

        // Obtener todos los roles de Administrador
        $roles = DB::connection('tenant')->table('roles')
            ->where('name', 'Administrador')
            ->orWhere('name', 'LIKE', '%Admin%')
            ->get();

        echo "📋 Roles encontrados: " . $roles->count() . "\n\n";

        foreach ($roles as $role) {
            echo "🔍 Procesando rol: {$role->name} (ID: {$role->id})\n";

            // Decodificar permisos actuales
            $currentPermissions = json_decode($role->permissions, true);

            if (!is_array($currentPermissions)) {
                $currentPermissions = [];
            }

            echo "   Permisos actuales: " . count($currentPermissions) . "\n";

            // Verificar si ya tiene permisos de expenses
            $hasExpenses = false;
            foreach ($currentPermissions as $perm) {
                if (strpos($perm, 'expenses.') === 0) {
                    $hasExpenses = true;
                    break;
                }
            }

            if ($hasExpenses) {
                echo "   ✅ Ya tiene permisos de expenses\n\n";
                continue;
            }

            // Agregar permisos de expenses
            $updatedPermissions = array_merge($currentPermissions, $expensesPermissions);
            $updatedPermissions = array_unique($updatedPermissions);

            // Actualizar en la base de datos
            DB::connection('tenant')->table('roles')
                ->where('id', $role->id)
                ->update([
                    'permissions' => json_encode(array_values($updatedPermissions))
                ]);

            echo "   ✅ Permisos agregados: " . count($expensesPermissions) . "\n";
            echo "   📊 Total permisos ahora: " . count($updatedPermissions) . "\n\n";
        }

        // Obtener usuario actual (ID 1)
        $user = DB::connection('tenant')->table('users')->where('id', 1)->first();

        if ($user) {
            echo "👤 Usuario encontrado: {$user->name} (email: {$user->email})\n";
            echo "   Rol ID: {$user->role_id}\n";

            $userRole = DB::connection('tenant')->table('roles')->where('id', $user->role_id)->first();

            if ($userRole) {
                echo "   Rol: {$userRole->name}\n";
                $perms = json_decode($userRole->permissions, true);
                echo "   Total permisos: " . count($perms) . "\n";

                // Verificar permisos de expenses
                $expensesPerms = array_filter($perms, function($p) {
                    return strpos($p, 'expenses.') === 0;
                });

                if (count($expensesPerms) > 0) {
                    echo "   ✅ Permisos de expenses: " . implode(', ', $expensesPerms) . "\n";
                } else {
                    echo "   ❌ NO tiene permisos de expenses\n";
                }
            }
        }

        echo "\n";

    } catch (\Exception $e) {
        echo "❌ Error procesando tenant {$tenant->id}: " . $e->getMessage() . "\n\n";
    }
}

echo "\n✅ Proceso completado en todos los tenants!\n";
echo "\n🔄 IMPORTANTE: Cierra sesión y vuelve a iniciar sesión para que los cambios surtan efecto.\n";
