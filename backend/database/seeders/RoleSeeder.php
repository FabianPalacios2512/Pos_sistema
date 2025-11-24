<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrador',
                'description' => 'Acceso completo a todo el sistema',
                'permissions' => [
                    'dashboard.view', 'dashboard.analytics',
                    'products.view', 'products.create', 'products.edit', 'products.delete', 'products.import', 'products.export',
                    'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
                    'inventory.view', 'inventory.movements', 'inventory.adjust', 'inventory.reports',
                    'sales.view', 'sales.create', 'sales.edit', 'sales.delete', 'sales.refund', 'sales.reports',
                    'customers.view', 'customers.create', 'customers.edit', 'customers.delete', 'customers.reports',
                    'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete',
                    'reports.view', 'reports.sales', 'reports.inventory', 'reports.financial',
                    'users.view', 'users.create', 'users.edit', 'users.delete',
                    'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
                    'settings.view', 'settings.edit', 'settings.backup'
                ],
                'active' => true
            ],
            [
                'name' => 'Vendedor',
                'description' => 'Acceso a ventas y consulta de productos',
                'permissions' => [
                    'dashboard.view',
                    'products.view',
                    'inventory.view',
                    'sales.view', 'sales.create',
                    'customers.view', 'customers.create',
                    'reports.view', 'reports.sales'
                ],
                'active' => true
            ],
            [
                'name' => 'Cajero',
                'description' => 'Acceso limitado solo para ventas',
                'permissions' => [
                    'dashboard.view',
                    'products.view',
                    'sales.view', 'sales.create',
                    'customers.view'
                ],
                'active' => true
            ],
            [
                'name' => 'Gerente',
                'description' => 'Acceso a gestión y reportes avanzados',
                'permissions' => [
                    'dashboard.view', 'dashboard.analytics',
                    'products.view', 'products.create', 'products.edit', 'products.export',
                    'categories.view', 'categories.create', 'categories.edit',
                    'inventory.view', 'inventory.movements', 'inventory.adjust', 'inventory.reports',
                    'sales.view', 'sales.create', 'sales.edit', 'sales.refund', 'sales.reports',
                    'customers.view', 'customers.create', 'customers.edit', 'customers.reports',
                    'suppliers.view', 'suppliers.create', 'suppliers.edit',
                    'reports.view', 'reports.sales', 'reports.inventory', 'reports.financial',
                    'users.view'
                ],
                'active' => true
            ]
        ];

        foreach ($roles as $roleData) {
            Role::create($roleData);
        }
    }
}
