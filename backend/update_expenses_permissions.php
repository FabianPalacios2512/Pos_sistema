<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Cargar configuración de Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔄 Actualizando permisos de gastos operativos en todos los tenants...\n\n";

try {
    // Obtener todas las bases de datos de tenants
    $databases = DB::select("SHOW DATABASES LIKE 'tenant%'");
    $tenantDatabases = array_map(function($db) {
        return reset($db);
    }, $databases);

    // También incluir la base de datos principal
    array_unshift($tenantDatabases, 'pos_sistema');

    foreach ($tenantDatabases as $database) {
        echo "📦 Procesando base de datos: {$database}\n";

        // Cambiar a la base de datos del tenant
        DB::statement("USE `{$database}`");

        // Verificar si existe la tabla roles
        $tables = DB::select("SHOW TABLES LIKE 'roles'");
        if (empty($tables)) {
            echo "   ⏭️  No tiene tabla 'roles', saltando...\n\n";
            continue;
        }

        // Obtener todos los roles
        $roles = DB::table('roles')->get();

        if ($roles->isEmpty()) {
            echo "   ⚠️  No hay roles en esta base de datos\n\n";
            continue;
        }

        foreach ($roles as $role) {
            $permissions = json_decode($role->permissions, true);

            if (!is_array($permissions)) {
                $permissions = [];
            }

            // Verificar si ya tiene permisos de expenses
            $hasExpenses = false;
            foreach ($permissions as $perm) {
                if (strpos($perm, 'expenses') !== false) {
                    $hasExpenses = true;
                    break;
                }
            }

            if ($hasExpenses) {
                echo "   ✓ Rol '{$role->name}' ya tiene permisos de expenses\n";
                continue;
            }

            // Agregar permisos de expenses según el rol
            if ($role->name === 'Administrador') {
                $expensesPermissions = [
                    'expenses.view',
                    'expenses.create',
                    'expenses.edit',
                    'expenses.delete',
                    'expenses.reports'
                ];

                $permissions = array_unique(array_merge($permissions, $expensesPermissions));

                DB::table('roles')
                    ->where('id', $role->id)
                    ->update(['permissions' => json_encode($permissions)]);

                echo "   ✅ Rol '{$role->name}' actualizado con permisos completos de gastos\n";

            } elseif ($role->name === 'Gerente') {
                $expensesPermissions = [
                    'expenses.view',
                    'expenses.create',
                    'expenses.edit',
                    'expenses.reports'
                ];

                $permissions = array_unique(array_merge($permissions, $expensesPermissions));

                DB::table('roles')
                    ->where('id', $role->id)
                    ->update(['permissions' => json_encode($permissions)]);

                echo "   ✅ Rol '{$role->name}' actualizado con permisos de gastos\n";

            } else {
                echo "   ⏭️  Rol '{$role->name}' no requiere permisos de gastos\n";
            }
        }

        echo "\n";
    }

    echo "✨ ¡Permisos actualizados correctamente en todos los tenants!\n";
    echo "🔍 El menú 'Gastos Operativos' ahora debería ser visible.\n";
    echo "🔄 Recarga la página para ver los cambios.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
