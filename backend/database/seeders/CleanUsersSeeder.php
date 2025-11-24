<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class CleanUsersSeeder extends Seeder
{
    /**
     * Limpiar usuarios existentes y crear solo los usuarios demo
     */
    public function run(): void
    {
        // Deshabilitar verificaciones de foreign key temporalmente
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Limpiar tokens de acceso
        DB::table('personal_access_tokens')->truncate();

        // Limpiar movimientos de inventario que referencian usuarios
        DB::table('inventory_movements')->truncate();

        // Limpiar usuarios
        DB::table('users')->truncate();

        // Rehabilitar verificaciones de foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Obtener roles existentes
        $adminRole = Role::where('name', 'Administrador')->first();
        $vendedorRole = Role::where('name', 'Vendedor')->first();

        if (!$adminRole || !$vendedorRole) {
            $this->command->error('Los roles Administrador y Vendedor deben existir antes de ejecutar este seeder.');
            return;
        }

        // Crear usuarios demo
        $users = [
            [
                'name' => 'Administrador Sistema',
                'email' => 'admin@pos.com',
                'cc' => '12345678',
                'password' => Hash::make('admin123'),
                'phone' => '+57 300 123 4567',
                'active' => true,
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Vendedor Principal',
                'email' => 'vendedor@pos.com',
                'cc' => '11223344',
                'password' => Hash::make('vendedor123'),
                'phone' => '+57 300 987 6543',
                'active' => true,
                'role_id' => $vendedorRole->id,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        $this->command->info('✅ Base de datos limpiada y usuarios demo creados:');
        $this->command->info('🔑 Admin: CC=12345678 / Password=admin123');
        $this->command->info('🛒 Vendedor: CC=11223344 / Password=vendedor123');
    }
}
