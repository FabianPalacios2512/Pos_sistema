<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Only insert if it doesn't exist yet
        $exists = DB::table('roles')->where('name', 'Administrador POS')->exists();

        if (!$exists) {
            $permissions = [
                'dashboard.view',
                'products.view', 'products.create', 'products.edit', 'products.delete',
                'inventory.view', 'inventory.adjust',
                'sales.view', 'sales.create', 'sales.edit', 'sales.delete',
                'customers.view', 'customers.create', 'customers.edit', 'customers.delete',
                'invoices.view', 'invoices.create', 'invoices.print',
                'reports.view', 'reports.export',
                'users.view', 'users.create', 'users.edit',
                'cash.view', 'cash.open', 'cash.close',
                'expenses.view', 'expenses.create', 'expenses.edit', 'expenses.delete',
                'settings.view',
                'warehouses.view',
            ];

            DB::table('roles')->insert([
                'name'         => 'Administrador POS',
                'permissions'  => json_encode($permissions),
                'active'       => 1,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('roles')->where('name', 'Administrador POS')->delete();
    }
};
