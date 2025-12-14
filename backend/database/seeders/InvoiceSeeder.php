<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();
        $products = Product::all();

        if ($customers->count() === 0 || $products->count() === 0) {
            $this->command->warn('No hay clientes o productos para crear facturas. Ejecuta primero los seeders de clientes y productos.');
            return;
        }

        // Crear facturas de ejemplo
        $invoices = [
            [
                'number' => 'FV-000001',
                'type' => 'invoice',
                'customer_id' => $customers->random()->id,
                'date' => now()->subDays(10),
                'due_date' => now()->addDays(20),
                'status' => 'paid',
                'notes' => 'Factura pagada a tiempo',
                'items' => [
                    [
                        'product_id' => $products->random()->id,
                        'name' => 'Café Colombiano Premium 500g',
                        'quantity' => 2,
                        'unit_price' => 15000,
                        'total' => 30000
                    ],
                    [
                        'product_id' => $products->random()->id,
                        'name' => 'Azúcar Morena 1kg',
                        'quantity' => 1,
                        'unit_price' => 8000,
                        'total' => 8000
                    ]
                ]
            ],
            [
                'number' => 'FV-000002',
                'type' => 'invoice',
                'customer_id' => $customers->random()->id,
                'date' => now()->subDays(5),
                'due_date' => now()->addDays(25),
                'status' => 'sent',
                'notes' => 'Factura enviada al cliente',
                'items' => [
                    [
                        'product_id' => $products->random()->id,
                        'name' => 'Leche Deslactosada 1L',
                        'quantity' => 3,
                        'unit_price' => 5500,
                        'total' => 16500
                    ]
                ]
            ],
            [
                'number' => 'COT-000001',
                'type' => 'quote',
                'customer_id' => $customers->random()->id,
                'date' => now()->subDays(3),
                'due_date' => now()->addDays(7),
                'status' => 'draft',
                'notes' => 'Cotización pendiente de aprobación',
                'items' => [
                    [
                        'product_id' => $products->random()->id,
                        'name' => 'Aceite de Oliva Extra Virgen 500ml',
                        'quantity' => 2,
                        'unit_price' => 25000,
                        'total' => 50000
                    ],
                    [
                        'product_id' => $products->random()->id,
                        'name' => 'Vinagre Balsámico 250ml',
                        'quantity' => 1,
                        'unit_price' => 18000,
                        'total' => 18000
                    ]
                ]
            ],
            [
                'number' => 'FV-000003',
                'type' => 'invoice',
                'customer_id' => $customers->random()->id,
                'date' => now()->subDays(20),
                'due_date' => now()->subDays(5),
                'status' => 'overdue',
                'notes' => 'Factura vencida - pendiente de pago',
                'items' => [
                    [
                        'product_id' => $products->random()->id,
                        'name' => 'Pan Integral 500g',
                        'quantity' => 4,
                        'unit_price' => 6000,
                        'total' => 24000
                    ]
                ]
            ],
            [
                'number' => 'NC-000001',
                'type' => 'credit_note',
                'customer_id' => $customers->random()->id,
                'date' => now()->subDays(2),
                'due_date' => null,
                'status' => 'draft',
                'notes' => 'Nota crédito por devolución de productos',
                'items' => [
                    [
                        'product_id' => $products->random()->id,
                        'name' => 'Yogur Natural 150g',
                        'quantity' => -2,
                        'unit_price' => 3500,
                        'total' => -7000
                    ]
                ]
            ]
        ];

        foreach ($invoices as $invoiceData) {
            // Calcular totales
            $subtotal = 0;
            foreach ($invoiceData['items'] as $item) {
                $subtotal += $item['total'];
            }

            $tax_amount = $subtotal * 0.19; // IVA del 19%
            $total = $subtotal + $tax_amount;

            Invoice::create([
                'number' => $invoiceData['number'],
                'type' => $invoiceData['type'],
                'customer_id' => $invoiceData['customer_id'],
                'date' => $invoiceData['date'],
                'due_date' => $invoiceData['due_date'],
                'subtotal' => $subtotal,
                'tax_amount' => $tax_amount,
                'total' => $total,
                'status' => $invoiceData['status'],
                'notes' => $invoiceData['notes'],
                'items' => $invoiceData['items']
            ]);
        }

        $this->command->info('Se crearon ' . count($invoices) . ' facturas de ejemplo.');
    }
}
