<?php

// Script para poblar las nuevas tablas con datos de prueba
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\AppliedDiscount;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

echo "Poblando las nuevas tablas con datos de facturas existentes...\n\n";

try {
    DB::beginTransaction();

    // Obtener algunas facturas existentes
    $invoices = Invoice::with('customer')->take(10)->get();

    if ($invoices->isEmpty()) {
        echo "No hay facturas existentes para procesar.\n";
        exit(1);
    }

    // Obtener algunos productos para usar como referencia
    $productNames = ['Producto A', 'Producto B', 'Producto C', 'Producto D', 'Producto E'];

    foreach ($invoices as $invoice) {
        // Simular items para cada factura basado en los datos existentes
        $totalItems = rand(1, 4); // 1-4 items por factura
        $invoiceSubtotal = 0;

        echo "Procesando factura {$invoice->number} del cliente {$invoice->customer->name}...\n";

        for ($i = 1; $i <= $totalItems; $i++) {
            // Simular datos realistas de items
            $quantity = rand(1, 5);
            $unitPrice = round(rand(10, 200) + (rand(0, 99) / 100), 2);
            $subtotal = $quantity * $unitPrice;
            $discountAmount = round($subtotal * (rand(0, 15) / 100), 2); // 0-15% descuento
            $productName = $productNames[rand(0, count($productNames) - 1)];

            $invoiceSubtotal += ($subtotal - $discountAmount);

            // Insertar item
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'product_id' => rand(1, 10), // Asumiendo que hay productos 1-10
                'product_name' => $productName,
                'product_sku' => 'SKU-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'cost_price' => round($unitPrice * 0.6, 2), // 60% del precio de venta
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => round($subtotal * 0.16, 2), // 16% de impuesto
                'notes' => 'Item generado automáticamente'
            ]);
        }

        // Simular descuentos aplicados (50% probabilidad)
        if (rand(0, 1)) {
            $discountAmount = round($invoiceSubtotal * (rand(5, 20) / 100), 2);

            AppliedDiscount::create([
                'customer_id' => $invoice->customer_id,
                'invoice_id' => $invoice->id,
                'discount_type' => ['percentage', 'fixed', 'loyalty'][rand(0, 2)],
                'discount_value' => rand(5, 20),
                'discount_amount' => $discountAmount,
                'discount_reason' => ['Cliente frecuente', 'Promoción especial', 'Descuento por volumen', 'Descuento de temporada'][rand(0, 3)],
                'applied_by' => '1', // String para el campo applied_by
                'applied_at' => $invoice->created_at
            ]);

            echo "  - Descuento aplicado: $" . number_format($discountAmount, 2) . "\n";
        }

        echo "  - Items creados: $totalItems\n";
        echo "  - Subtotal: $" . number_format($invoiceSubtotal, 2) . "\n\n";
    }

    DB::commit();

    // Mostrar estadísticas finales
    $itemCount = InvoiceItem::count();
    $discountCount = AppliedDiscount::count();

    echo "✅ Datos poblados exitosamente!\n";
    echo "📊 Estadísticas finales:\n";
    echo "   - Items de facturas: $itemCount\n";
    echo "   - Descuentos aplicados: $discountCount\n";
    echo "   - Facturas procesadas: " . $invoices->count() . "\n\n";

} catch (Exception $e) {
    DB::rollback();
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "🎉 ¡Proceso completado! Las nuevas tablas ahora contienen datos para el análisis de inventario inteligente.\n";
