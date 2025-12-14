<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'Efectivo',
                'code' => 'efectivo',
                'description' => 'Pago en efectivo',
                'icon' => '💵',
                'active' => true,
                'requires_reference' => false,
                'sort_order' => 1,
                'config' => [
                    'allow_change' => true,
                    'currency' => 'COP'
                ]
            ],
            [
                'name' => 'Tarjeta de Crédito/Débito',
                'code' => 'tarjeta',
                'description' => 'Pago con tarjeta de crédito o débito',
                'icon' => '💳',
                'active' => true,
                'requires_reference' => true,
                'sort_order' => 2,
                'config' => [
                    'require_authorization' => true,
                    'fee_percentage' => 0
                ]
            ],
            [
                'name' => 'Transferencia Bancaria',
                'code' => 'transferencia',
                'description' => 'Transferencia bancaria, Nequi, Daviplata u otras plataformas',
                'icon' => '🏦',
                'active' => true,
                'requires_reference' => true,
                'sort_order' => 3,
                'config' => [
                    'require_reference' => true,
                    'platforms' => ['Bancolombia', 'Nequi', 'Daviplata', 'Banco de Bogotá', 'PSE']
                ]
            ]
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }
    }
}
