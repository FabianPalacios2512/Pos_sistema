<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Juan Carlos Pérez',
                'email' => 'juan.perez@email.com',
                'phone' => '+57 300 111 2233',
                'address' => 'Carrera 15 #34-56',
                'city' => 'Bogotá',
                'document_type' => 'CC',
                'document_number' => '12345678',
                'birth_date' => '1985-03-15',
                'gender' => 'M',
                'credit_limit' => 500000.00,
                'current_debt' => 0.00,
                'total_purchases' => 1250000.00,
                'total_orders' => 45,
                'active' => true,
                'last_purchase' => now()->subDays(3)
            ],
            [
                'name' => 'María Fernanda García',
                'email' => 'mafe.garcia@gmail.com',
                'phone' => '+57 311 444 5566',
                'address' => 'Calle 67 #12-34',
                'city' => 'Medellín',
                'document_type' => 'CC',
                'document_number' => '87654321',
                'birth_date' => '1990-07-22',
                'gender' => 'F',
                'credit_limit' => 300000.00,
                'current_debt' => 85000.00,
                'total_purchases' => 890000.00,
                'total_orders' => 32,
                'active' => true,
                'last_purchase' => now()->subDays(1)
            ],
            [
                'name' => 'Luis Eduardo Martínez',
                'email' => 'luis.martinez@empresa.com',
                'phone' => '+57 312 777 8899',
                'address' => 'Avenida 68 #145-23',
                'city' => 'Cali',
                'document_type' => 'CC',
                'document_number' => '23456789',
                'birth_date' => '1978-11-08',
                'gender' => 'M',
                'credit_limit' => 800000.00,
                'current_debt' => 125000.00,
                'total_purchases' => 2150000.00,
                'total_orders' => 78,
                'active' => true,
                'last_purchase' => now()->subDays(7)
            ],
            [
                'name' => 'Ana Sofía López',
                'email' => 'ana.lopez@hotmail.com',
                'phone' => '+57 320 999 1122',
                'address' => 'Calle 123 #45-67',
                'city' => 'Barranquilla',
                'document_type' => 'CC',
                'document_number' => '34567890',
                'birth_date' => '1992-05-14',
                'gender' => 'F',
                'credit_limit' => 200000.00,
                'current_debt' => 0.00,
                'total_purchases' => 456000.00,
                'total_orders' => 23,
                'active' => true,
                'last_purchase' => now()->subDays(15)
            ],
            [
                'name' => 'Carlos Andrés Silva',
                'email' => 'carlos.silva@yahoo.com',
                'phone' => '+57 315 555 3344',
                'address' => 'Carrera 45 #78-90',
                'city' => 'Bucaramanga',
                'document_type' => 'CC',
                'document_number' => '45678901',
                'birth_date' => '1987-12-03',
                'gender' => 'M',
                'credit_limit' => 400000.00,
                'current_debt' => 75000.00,
                'total_purchases' => 675000.00,
                'total_orders' => 28,
                'active' => true,
                'last_purchase' => now()->subDays(5)
            ],
            [
                'name' => 'Tienda El Buen Precio',
                'email' => 'ventas@buenprecio.com',
                'phone' => '+57 318 666 7788',
                'address' => 'Calle 23 #67-89',
                'city' => 'Cartagena',
                'document_type' => 'NIT',
                'document_number' => '900123456-1',
                'birth_date' => null,
                'gender' => null,
                'credit_limit' => 2000000.00,
                'current_debt' => 450000.00,
                'total_purchases' => 5600000.00,
                'total_orders' => 156,
                'active' => true,
                'last_purchase' => now()->subDays(2)
            ]
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }
    }
}
