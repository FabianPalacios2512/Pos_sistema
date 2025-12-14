<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Discount;
use Carbon\Carbon;

class DiscountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $discounts = [
            [
                'name' => 'Descuento de Bienvenida',
                'description' => 'Descuento del 10% para nuevos clientes',
                'code' => 'BIENVENIDA10',
                'type' => 'percentage',
                'value' => 10.00,
                'applies_to' => 'all_products',
                'applicable_items' => null,
                'minimum_amount' => 50000,
                'maximum_discount' => 20000,
                'usage_limit' => 100,
                'used_count' => 0,
                'starts_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addMonths(3),
                'active' => true,
                'stackable' => false,
            ],
            [
                'name' => 'Super Descuento',
                'description' => 'Descuento fijo de $15.000',
                'code' => 'SUPER15',
                'type' => 'fixed_amount',
                'value' => 15000.00,
                'applies_to' => 'all_products',
                'applicable_items' => null,
                'minimum_amount' => 75000,
                'maximum_discount' => null,
                'usage_limit' => 50,
                'used_count' => 0,
                'starts_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addMonths(1),
                'active' => true,
                'stackable' => false,
            ],
            [
                'name' => 'Descuento Productos de Electrónica',
                'description' => 'Descuento del 15% en productos de electrónica',
                'code' => 'ELECTRO15',
                'type' => 'percentage',
                'value' => 15.00,
                'applies_to' => 'categories',
                'applicable_items' => [1], // ID de categoría electrónica
                'minimum_amount' => 30000,
                'maximum_discount' => 50000,
                'usage_limit' => null,
                'used_count' => 0,
                'starts_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addWeeks(2),
                'active' => true,
                'stackable' => true,
            ],
            [
                'name' => 'Black Friday',
                'description' => 'Mega descuento Black Friday - 25%',
                'code' => 'BLACKFRIDAY25',
                'type' => 'percentage',
                'value' => 25.00,
                'applies_to' => 'all_products',
                'applicable_items' => null,
                'minimum_amount' => 100000,
                'maximum_discount' => 75000,
                'usage_limit' => 200,
                'used_count' => 0,
                'starts_at' => Carbon::now()->addDays(30),
                'expires_at' => Carbon::now()->addDays(32),
                'active' => true,
                'stackable' => false,
            ],
            [
                'name' => 'Descuento Cliente VIP',
                'description' => 'Descuento exclusivo para clientes VIP',
                'code' => 'VIP20',
                'type' => 'percentage',
                'value' => 20.00,
                'applies_to' => 'customers',
                'applicable_items' => [1, 2], // IDs de clientes VIP
                'minimum_amount' => 0,
                'maximum_discount' => null,
                'usage_limit' => null,
                'used_count' => 0,
                'starts_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addYear(),
                'active' => true,
                'stackable' => true,
            ]
        ];

        foreach ($discounts as $discount) {
            Discount::create($discount);
        }
    }
}
