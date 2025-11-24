<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\InventoryMovement;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use DB;

class InventoryDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear proveedores realistas
        $suppliers = [
            [
                'name' => 'Distribuidora Central S.A.',
                'contact_name' => 'María González',
                'email' => 'maria@distribuidora.com',
                'phone' => '555-0123',
                'address' => 'Av. Principal 123',
                'city' => 'Ciudad Central',
                'country' => 'Colombia',
                'tax_id' => '12345678901',
                'credit_limit' => 50000.00,
                'active' => true,
            ],
            [
                'name' => 'Proveedora Norte Ltda.',
                'contact_name' => 'Carlos Rodríguez',
                'email' => 'carlos@norte.com',
                'phone' => '555-0456',
                'address' => 'Calle Comercial 456',
                'city' => 'Norte',
                'country' => 'Colombia',
                'tax_id' => '98765432109',
                'credit_limit' => 25000.00,
                'active' => true,
            ],
            [
                'name' => 'Mayorista Sur',
                'contact_name' => 'Ana Martínez',
                'email' => 'ana@sur.com',
                'phone' => '555-0789',
                'address' => 'Zona Industrial Sur 789',
                'city' => 'Sur',
                'country' => 'Colombia',
                'tax_id' => '11223344556',
                'credit_limit' => 75000.00,
                'active' => true,
            ],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::create($supplierData);
        }

        echo "✅ Proveedores creados\n";

        // 2. Actualizar productos existentes con datos de inventario
        $products = Product::all();
        $suppliers = Supplier::all();

        foreach ($products as $product) {
            // Asignar proveedor aleatorio
            $supplier = $suppliers->random();

            // Calcular precios realistas basados en el precio de venta
            $salePrice = $product->sale_price ?? 100;
            $costPrice = round($salePrice * 0.6, 2); // 40% de margen
            $minStock = rand(5, 15);
            $maxStock = $minStock * 4;

            $product->update([
                'cost_price' => $costPrice,
                'min_stock' => $minStock,
                'max_stock' => $maxStock,
                'supplier_id' => $supplier->id,
            ]);
        }

        echo "✅ Productos actualizados con datos de inventario\n";

        // 3. Crear movimientos de inventario históricos (últimos 3 meses)
        $movementTypes = ['purchase', 'sale', 'adjustment', 'return', 'transfer'];

        // Crear 200 movimientos distribuidos en los últimos 90 días
        for ($i = 0; $i < 200; $i++) {
            $product = $products->random();
            $type = $movementTypes[array_rand($movementTypes)];

            // Cantidades según el tipo (positivo para entrada, negativo para salida)
            $quantity = match($type) {
                'purchase' => rand(10, 100), // Entrada positiva
                'return' => rand(5, 20),     // Entrada positiva
                'sale' => -rand(1, 20),      // Salida negativa
                'adjustment' => rand(-10, 10), // Puede ser positivo o negativo
                'transfer' => rand(-15, 15), // Puede ser positivo o negativo
            };

            // Stock anterior simulado
            $previousStock = rand(50, 200);
            $newStock = $previousStock + $quantity; // quantity ya incluye el signo

            // Fechas distribuidas en los últimos 90 días
            $date = Carbon::now()->subDays(rand(1, 90));

            InventoryMovement::create([
                'product_id' => $product->id,
                'type' => $type,
                'quantity' => $quantity,
                'previous_stock' => $previousStock,
                'new_stock' => max(0, $newStock),
                'unit_cost' => $product->cost_price ?? 0,
                'reference' => null,
                'notes' => "Movimiento generado automáticamente - " . ucfirst($type),
                'user_id' => 1, // Usuario admin
                'movement_date' => $date,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }

        echo "✅ Movimientos de inventario creados\n";

        // 4. Actualizar SaleItems existentes con costos
        $saleItems = SaleItem::all();
        foreach ($saleItems as $item) {
            $product = Product::find($item->product_id);
            if ($product && $product->cost_price) {
                $unitCost = $product->cost_price;
                $unitPrice = $item->unit_price ?? $product->sale_price;
                $profitAmount = ($unitPrice - $unitCost) * $item->quantity;
                $profitMargin = $unitPrice > 0 ? (($unitPrice - $unitCost) / $unitPrice) * 100 : 0;

                $item->update([
                    'unit_cost' => $unitCost,
                    'profit_margin' => round($profitMargin, 2),
                    'profit_amount' => round($profitAmount, 2),
                ]);
            }
        }

        echo "✅ Items de venta actualizados con costos\n";

        // 5. Crear algunas ventas adicionales para demostrar análisis
        $this->createSampleSales($products);

        echo "✅ Ventas de muestra creadas\n";

        echo "\n🎉 Seeder de inventario completado exitosamente!\n";
        echo "📊 Datos creados:\n";
        echo "   - " . Supplier::count() . " proveedores\n";
        echo "   - " . Product::count() . " productos con datos de inventario\n";
        echo "   - " . InventoryMovement::count() . " movimientos de inventario\n";
        echo "   - " . SaleItem::count() . " items de venta con costos\n";
    }

    private function createSampleSales($products)
    {
        // Crear 20 ventas adicionales en los últimos 30 días
        for ($i = 0; $i < 20; $i++) {
            $date = Carbon::now()->subDays(rand(1, 30));

            $sale = Sale::create([
                'invoice_number' => 'INV-' . str_pad($i + 1000, 6, '0', STR_PAD_LEFT),
                'customer_id' => 7, // Cliente General
                'user_id' => 1, // Usuario admin
                'subtotal' => 0, // Se calculará después
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0, // Se calculará después
                'payment_method' => ['cash', 'card'][rand(0, 1)],
                'cash_received' => 0,
                'change_given' => 0,
                'status' => 'completed',
                'notes' => 'Venta de muestra generada automáticamente',
                'sale_date' => $date,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            $subtotal = 0;
            $itemCount = rand(1, 5); // 1 a 5 productos por venta

            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                $unitPrice = $product->sale_price ?? 100;
                $totalPrice = $unitPrice * $quantity;
                $subtotal += $totalPrice;
                $unitCost = $product->cost_price ?? 0;
                $profitAmount = ($unitPrice - $unitCost) * $quantity;
                $profitMargin = $unitPrice > 0 ? (($unitPrice - $unitCost) / $unitPrice) * 100 : 0;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'unit_cost' => $unitCost,
                    'profit_margin' => round($profitMargin, 2),
                    'profit_amount' => round($profitAmount, 2),
                    'discount_amount' => 0,
                ]);

                // Crear movimiento de salida correspondiente
                $previousStock = rand(50, 200);
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'type' => 'sale',
                    'quantity' => -$quantity, // Negativo para salida
                    'previous_stock' => $previousStock,
                    'new_stock' => max(0, $previousStock - $quantity),
                    'unit_cost' => $unitCost,
                    'reference' => "Sale#{$sale->id}",
                    'notes' => "Venta #{$sale->id}",
                    'user_id' => 1,
                    'movement_date' => $date,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }

            // Calcular totales de la venta
            $taxAmount = $subtotal * 0.19; // IVA 19%
            $totalAmount = $subtotal + $taxAmount;

            $sale->update([
                'subtotal' => $subtotal,
                'tax_amount' => round($taxAmount, 2),
                'total_amount' => round($totalAmount, 2),
                'cash_received' => round($totalAmount, 2),
            ]);
        }
    }
}
