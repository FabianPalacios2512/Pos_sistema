<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\InvoiceItem;
use App\Models\AppliedDiscount;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

echo "🎯 ANÁLISIS DE DESCUENTOS Y CÓDIGOS PROMOCIONALES\n";
echo "================================================\n\n";

// 1. Análisis total de descuentos
echo "📊 1. RESUMEN DE DESCUENTOS TOTALES:\n";

$totalDiscountsItems = InvoiceItem::sum('discount_amount');
$totalDiscountsGeneral = AppliedDiscount::sum('discount_amount');
$totalDiscounts = $totalDiscountsItems + $totalDiscountsGeneral;

echo "   Descuentos en items individuales: $" . number_format($totalDiscountsItems, 2) . "\n";
echo "   Descuentos generales aplicados: $" . number_format($totalDiscountsGeneral, 2) . "\n";
echo "   TOTAL DESCUENTOS: $" . number_format($totalDiscounts, 2) . "\n\n";

// 2. Verificar si $71,200 coincide con descuentos
$diff71200 = abs(71200 - $totalDiscounts);
echo "   ¿$71,200 = Total descuentos? Diferencia: $" . number_format($diff71200, 2) .
     ($diff71200 < 1000 ? " 🎯 POSIBLE MATCH!" : "") . "\n\n";

// 3. Análisis de facturas con descuentos grandes
echo "📊 2. FACTURAS CON DESCUENTOS SIGNIFICATIVOS:\n";

$bigDiscountInvoices = DB::select("
    SELECT
        i.id,
        i.number,
        i.total as invoice_total,
        i.subtotal,
        (i.subtotal - i.total) as discount_difference,
        COALESCE(SUM(ii.discount_amount), 0) as item_discounts,
        COALESCE(SUM(ad.discount_amount), 0) as general_discounts
    FROM invoices i
    LEFT JOIN invoice_items ii ON i.id = ii.invoice_id
    LEFT JOIN applied_discounts ad ON i.id = ad.invoice_id
    GROUP BY i.id, i.number, i.total, i.subtotal
    HAVING (i.subtotal - i.total) > 1000 OR SUM(COALESCE(ii.discount_amount, 0)) > 1000 OR SUM(COALESCE(ad.discount_amount, 0)) > 1000
    ORDER BY (i.subtotal - i.total) DESC
    LIMIT 10
");

if (empty($bigDiscountInvoices)) {
    echo "   ✅ No se encontraron facturas con descuentos grandes\n\n";
} else {
    foreach ($bigDiscountInvoices as $invoice) {
        $totalInvoiceDiscount = $invoice->item_discounts + $invoice->general_discounts;
        echo "   📄 Factura {$invoice->number}:\n";
        echo "      Total factura: $" . number_format($invoice->invoice_total, 2) . "\n";
        echo "      Subtotal: $" . number_format($invoice->subtotal, 2) . "\n";
        echo "      Diferencia (descuento): $" . number_format($invoice->discount_difference, 2) . "\n";
        echo "      Descuentos en items: $" . number_format($invoice->item_discounts, 2) . "\n";
        echo "      Descuentos generales: $" . number_format($invoice->general_discounts, 2) . "\n";
        echo "      Total descuentos: $" . number_format($totalInvoiceDiscount, 2) . "\n\n";
    }
}

// 4. Buscar específicamente facturas cerca de $71,200
echo "📊 3. BUSCANDO FACTURAS RELACIONADAS CON $71,200:\n";

$searches = [
    'Facturas con total ~71200' => Invoice::whereBetween('total', [70000, 72000])->get(['number', 'total', 'subtotal']),
    'Facturas con subtotal ~71200' => Invoice::whereBetween('subtotal', [70000, 72000])->get(['number', 'total', 'subtotal']),
    'Descuentos individuales ~71200' => AppliedDiscount::whereBetween('discount_amount', [70000, 72000])->get(['invoice_id', 'discount_amount', 'discount_reason'])
];

foreach ($searches as $desc => $results) {
    echo "   $desc:\n";
    if ($results->isEmpty()) {
        echo "      - No se encontraron resultados\n";
    } else {
        foreach ($results as $result) {
            if (isset($result->number)) {
                echo "      - Factura {$result->number}: Total $" . number_format($result->total, 2) .
                     ", Subtotal $" . number_format($result->subtotal, 2) . "\n";
            } else {
                echo "      - Descuento: $" . number_format($result->discount_amount, 2) .
                     " ({$result->discount_reason})\n";
            }
        }
    }
    echo "\n";
}

// 5. Verificar valor original antes de descuentos
echo "📊 4. ANÁLISIS DE VALOR ANTES VS DESPUÉS DE DESCUENTOS:\n";

$totalSubtotal = Invoice::sum('subtotal');
$totalFinal = Invoice::sum('total');
$totalDiscountDifference = $totalSubtotal - $totalFinal;

echo "   Total subtotales (antes descuentos): $" . number_format($totalSubtotal, 2) . "\n";
echo "   Total finales (después descuentos): $" . number_format($totalFinal, 2) . "\n";
echo "   Diferencia por descuentos: $" . number_format($totalDiscountDifference, 2) . "\n";

$diff71200_2 = abs(71200 - $totalDiscountDifference);
echo "   ¿$71,200 = Diferencia descuentos? Diferencia: $" . number_format($diff71200_2, 2) .
     ($diff71200_2 < 1000 ? " 🎯 POSIBLE MATCH!" : "") . "\n\n";

// 6. Códigos promocionales específicos
echo "📊 5. CÓDIGOS PROMOCIONALES ENCONTRADOS:\n";

$promoDiscounts = AppliedDiscount::where('discount_reason', 'like', '%promocion%')
    ->orWhere('discount_reason', 'like', '%codigo%')
    ->orWhere('discount_reason', 'like', '%cupon%')
    ->orWhere('discount_type', 'like', '%percentage%')
    ->get();

if ($promoDiscounts->isEmpty()) {
    echo "   ⚠️ No se encontraron códigos promocionales específicos\n";
} else {
    $totalPromoDiscounts = $promoDiscounts->sum('discount_amount');
    echo "   📋 Total descuentos promocionales: $" . number_format($totalPromoDiscounts, 2) . "\n";

    foreach ($promoDiscounts as $promo) {
        echo "      - $" . number_format($promo->discount_amount, 2) .
             " ({$promo->discount_type}) - {$promo->discount_reason}\n";
    }

    $diff71200_3 = abs(71200 - $totalPromoDiscounts);
    echo "   ¿$71,200 = Descuentos promocionales? Diferencia: $" . number_format($diff71200_3, 2) .
         ($diff71200_3 < 1000 ? " 🎯 MATCH!" : "") . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "🎯 HIPÓTESIS: Los $71,200 representan descuentos aplicados que el sistema\n";
echo "   no está restando correctamente del valor de inventario.\n";
