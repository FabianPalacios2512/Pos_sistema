<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrador Sistema',
                'email' => 'admin@pos.com',
                'password' => Hash::make('admin123'),
                'role_id' => 1, // Administrador
                'phone' => '+57 300 000 0001',
                'active' => true
            ],
            [
                'name' => 'Roberto Vargas',
                'email' => 'gerente@pos.com',
                'password' => Hash::make('gerente123'),
                'role_id' => 4, // Gerente
                'phone' => '+57 300 000 0002',
                'active' => true
            ],
            [
                'name' => 'Sandra Morales',
                'email' => 'vendedora1@pos.com',
                'password' => Hash::make('vendedor123'),
                'role_id' => 2, // Vendedor
                'phone' => '+57 300 000 0003',
                'active' => true
            ],
            [
                'name' => 'Diego Hernández',
                'email' => 'vendedor2@pos.com',
                'password' => Hash::make('vendedor123'),
                'role_id' => 2, // Vendedor
                'phone' => '+57 300 000 0004',
                'active' => true
            ],
            [
                'name' => 'Patricia Luna',
                'email' => 'cajera1@pos.com',
                'password' => Hash::make('cajero123'),
                'role_id' => 3, // Cajero
                'phone' => '+57 300 000 0005',
                'active' => true
            ],
            [
                'name' => 'Miguel Torres',
                'email' => 'cajero2@pos.com',
                'password' => Hash::make('cajero123'),
                'role_id' => 3, // Cajero
                'phone' => '+57 300 000 0006',
                'active' => true
            ]
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
