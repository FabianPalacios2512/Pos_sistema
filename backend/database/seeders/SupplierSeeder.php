<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Distribuidora La Central',
                'contact_name' => 'Carlos Mendoza',
                'phone' => '+57 300 123 4567',
                'email' => 'ventas@lacentral.com.co',
                'address' => 'Calle 45 #23-15, Zona Industrial',
                'city' => 'Bogotá',
                'country' => 'Colombia',
                'tax_id' => '900123456-1',
                'credit_limit' => 5000000.00,
                'current_debt' => 0.00,
                'active' => true
            ],
            [
                'name' => 'Alimentos del Valle S.A.S',
                'contact_name' => 'María García',
                'phone' => '+57 311 987 6543',
                'email' => 'compras@alimentosvalle.com',
                'address' => 'Carrera 12 #78-90',
                'city' => 'Cali',
                'country' => 'Colombia',
                'tax_id' => '800987654-2',
                'credit_limit' => 3000000.00,
                'current_debt' => 450000.00,
                'active' => true
            ],
            [
                'name' => 'Proveedora Norte Ltda',
                'contact_name' => 'Luis Rodríguez',
                'phone' => '+57 312 555 7890',
                'email' => 'info@proveedoranorte.com',
                'address' => 'Avenida 80 #145-67',
                'city' => 'Medellín',
                'country' => 'Colombia',
                'tax_id' => '890555123-3',
                'credit_limit' => 2500000.00,
                'current_debt' => 125000.00,
                'active' => true
            ],
            [
                'name' => 'Bebidas y Refrescos Colombia',
                'contact_name' => 'Ana López',
                'phone' => '+57 320 444 1122',
                'email' => 'ventas@bebidasrefrescos.co',
                'address' => 'Zona Franca, Bodega 15',
                'city' => 'Barranquilla',
                'country' => 'Colombia',
                'tax_id' => '901444567-4',
                'credit_limit' => 4000000.00,
                'current_debt' => 750000.00,
                'active' => true
            ],
            [
                'name' => 'Lácteos Premium',
                'contact_name' => 'Jorge Silva',
                'phone' => '+57 315 333 9988',
                'email' => 'comercial@lacteospremium.com',
                'address' => 'Km 5 Vía Sabana Centro',
                'city' => 'Chía',
                'country' => 'Colombia',
                'tax_id' => '800333777-5',
                'credit_limit' => 1800000.00,
                'current_debt' => 0.00,
                'active' => true
            ],
            [
                'name' => 'Frutas Frescas del Campo',
                'contact_name' => 'Isabel Morales',
                'phone' => '+57 318 222 5566',
                'email' => 'pedidos@frutasdelcampo.com',
                'address' => 'Plaza de Mercado Local 23',
                'city' => 'Villavicencio',
                'country' => 'Colombia',
                'tax_id' => '890222999-6',
                'credit_limit' => 800000.00,
                'current_debt' => 95000.00,
                'active' => true
            ]
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::create($supplierData);
        }
    }
}
