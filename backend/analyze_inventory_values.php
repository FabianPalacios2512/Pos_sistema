<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\InvoiceItem;
use App\Models\AppliedDiscount;
use Illuminate\Support\Facades\DB;

echo "🔍 ANÁLISIS DETALLADO DE VALORES DE INVENTARIO\n";
echo "===============================================\n\n";

// 1. Verificar productos activos vs inactivos
echo "📊 1. ESTADO DE PRODUCTOS:\n";
$productsActive = Product::where('active', true)->count();
$productsInactive = Product::where('active', false)->count();
$totalProducts = Product::count();

echo "   Total productos: $totalProducts\n";
echo "   Productos activos: $productsActive\n";
echo "   Productos inactivos: $productsInactive\n\n";

// 2. Calcular valores de costo y venta de productos ACTIVOS
echo "📊 2. VALORES DE PRODUCTOS ACTIVOS (INVENTARIO):\n";
$activeProducts = Product::where('active', true)->get();
$totalCostValue = 0;
$totalSaleValue = 0;

foreach ($activeProducts as $product) {
    $stockCost = $product->current_stock * $product->cost_price;
    $stockSale = $product->current_stock * $product->sale_price;
    $totalCostValue += $stockCost;
    $totalSaleValue += $stockSale;
}

echo "   Valor Costo (productos activos): $" . number_format($totalCostValue, 0) . "\n";
echo "   Valor Venta (productos activos): $" . number_format($totalSaleValue, 0) . "\n";
echo "   Ganancia Potencial: $" . number_format($totalSaleValue - $totalCostValue, 0) . "\n\n";

// 3. Verificar si hay productos vendidos que no están en invoice_items
echo "📊 3. ANÁLISIS DE VENTAS REALES:\n";

// Total vendido según invoice_items
$totalSoldInItems = InvoiceItem::sum(DB::raw('quantity * unit_price'));
echo "   Total vendido (según invoice_items): $" . number_format($totalSoldInItems, 0) . "\n";

// Total de descuentos aplicados
$totalDiscountsItems = InvoiceItem::sum('discount_amount');
$totalDiscountsGeneral = AppliedDiscount::sum('discount_amount');

echo "   Descuentos en items: $" . number_format($totalDiscountsItems, 0) . "\n";
echo "   Descuentos generales: $" . number_format($totalDiscountsGeneral, 0) . "\n";
echo "   Total descuentos: $" . number_format($totalDiscountsItems + $totalDiscountsGeneral, 0) . "\n\n";

// 4. Verificar movimientos de inventario
echo "📊 4. MOVIMIENTOS DE INVENTARIO:\n";
$movements = DB::table('inventory_movements')
    ->select('type', DB::raw('COUNT(*) as count'), DB::raw('SUM(quantity) as total_quantity'))
    ->groupBy('type')
    ->get();

foreach ($movements as $movement) {
    echo "   {$movement->type}: {$movement->count} movimientos, {$movement->total_quantity} unidades\n";
}

// 5. Análisis detallado del problema del $71,200
echo "\n📊 5. INVESTIGACIÓN DEL VALOR $71,200:\n";

// Verificar si coincide con algún cálculo
$allProducts = Product::all();
$totalInventoryCost = $allProducts->sum(function($p) { return $p->current_stock * $p->cost_price; });
$totalInventorySale = $allProducts->sum(function($p) { return $p->current_stock * $p->sale_price; });

echo "   Todos los productos (incluidos inactivos):\n";
echo "     Valor Costo Total: $" . number_format($totalInventoryCost, 0) . "\n";
echo "     Valor Venta Total: $" . number_format($totalInventorySale, 0) . "\n\n";

// Verificar productos con stock > 0
$productsWithStock = Product::where('current_stock', '>', 0)->get();
$costWithStock = $productsWithStock->sum(function($p) { return $p->current_stock * $p->cost_price; });
$saleWithStock = $productsWithStock->sum(function($p) { return $p->current_stock * $p->sale_price; });

echo "   Productos con stock > 0:\n";
echo "     Valor Costo: $" . number_format($costWithStock, 0) . "\n";
echo "     Valor Venta: $" . number_format($saleWithStock, 0) . "\n";
echo "     Cantidad productos: " . $productsWithStock->count() . "\n\n";

// 6. Verificar si $71,200 viene de ventas históricas
echo "📊 6. ANÁLISIS DE $71,200 - POSIBLES FUENTES:\n";

$possibleSources = [
    'Valor venta inventario activo' => $totalSaleValue,
    'Valor venta todo inventario' => $totalInventorySale,
    'Valor venta con stock' => $saleWithStock,
    'Total vendido en items' => $totalSoldInItems,
    'Ventas - descuentos items' => $totalSoldInItems - $totalDiscountsItems,
    'Ventas - todos descuentos' => $totalSoldInItems - $totalDiscountsItems - $totalDiscountsGeneral
];

echo "   Comparando $71,200 con:\n";
foreach ($possibleSources as $source => $value) {
    $diff = abs(71200 - $value);
    $match = $diff < 1000 ? "🎯 POSIBLE MATCH!" : "";
    echo "     $source: $" . number_format($value, 0) . " (diff: $" . number_format($diff, 0) . ") $match\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "CONCLUSIÓN: El valor $71,200 parece venir de: [ANALIZAR RESULTADOS ARRIBA]\n";
