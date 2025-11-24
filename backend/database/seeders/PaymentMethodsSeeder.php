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
                'description' => 'Pago con tarjeta',
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
                'description' => 'Transferencia electrónica',
                'icon' => '🏦',
                'active' => true,
                'requires_reference' => true,
                'sort_order' => 3,
                'config' => [
                    'require_reference' => true
                ]
            ],
            [
                'name' => 'Cheque',
                'code' => 'cheque',
                'description' => 'Pago con cheque',
                'icon' => '📝',
                'active' => false,
                'requires_reference' => true,
                'sort_order' => 4,
                'config' => [
                    'require_bank' => true,
                    'require_check_number' => true
                ]
            ],
            [
                'name' => 'Nequi',
                'code' => 'nequi',
                'description' => 'Pago con Nequi',
                'icon' => '📱',
                'active' => true,
                'requires_reference' => true,
                'sort_order' => 5,
                'config' => [
                    'require_phone' => true
                ]
            ],
            [
                'name' => 'Daviplata',
                'code' => 'daviplata',
                'description' => 'Pago con Daviplata',
                'icon' => '📱',
                'active' => true,
                'requires_reference' => true,
                'sort_order' => 6,
                'config' => [
                    'require_phone' => true
                ]
            ]
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }
    }
}
