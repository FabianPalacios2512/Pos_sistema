<?php

/**
 * TEST FINAL DEL AI CHATBOT
 * Prueba las preguntas específicas del usuario
 */

require __DIR__ . '/backend/vendor/autoload.php';

$app = require_once __DIR__ . '/backend/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Carbon\Carbon;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║         TEST FINAL - AI CHATBOT MEJORADO                       ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// Test 1: Verificar datos de productos
echo "📊 TEST 1: Verificación de datos de productos\n";
echo str_repeat("─", 70) . "\n";

$allProducts = Product::where('active', true)
    ->orderBy('name')
    ->get(['id', 'name', 'sale_price', 'current_stock as stock']);

echo "✓ Total productos activos: " . $allProducts->count() . "\n";

if ($allProducts->count() > 0) {
    echo "✓ Primeros 5 productos:\n";
    foreach ($allProducts->take(5) as $product) {
        echo "  - {$product->name}: \${$product->sale_price} (Stock: {$product->stock})\n";
    }
}

// Test 2: Producto más caro
echo "\n📊 TEST 2: Producto más caro\n";
echo str_repeat("─", 70) . "\n";

$mostExpensive = Product::where('active', true)
    ->orderBy('sale_price', 'DESC')
    ->first();

if ($mostExpensive) {
    echo "✓ Producto más caro: {$mostExpensive->name}\n";
    echo "  Precio: \${$mostExpensive->sale_price}\n";
}

// Test 3: Producto más barato
echo "\n📊 TEST 3: Producto más barato\n";
echo str_repeat("─", 70) . "\n";

$cheapest = Product::where('active', true)
    ->where('sale_price', '>', 0)
    ->orderBy('sale_price', 'ASC')
    ->first();

if ($cheapest) {
    echo "✓ Producto más barato: {$cheapest->name}\n";
    echo "  Precio: \${$cheapest->sale_price}\n";
}

// Test 4: Productos más vendidos ayer
echo "\n📊 TEST 4: Productos más vendidos AYER\n";
echo str_repeat("─", 70) . "\n";

$yesterday = Carbon::yesterday()->toDateString();
echo "Fecha ayer: {$yesterday}\n";

$topYesterday = InvoiceItem::join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
    ->join('products', 'invoice_items.product_id', '=', 'products.id')
    ->whereDate('invoices.created_at', $yesterday)
    ->where('invoices.status', 'completed')
    ->select(
        'products.name',
        \DB::raw('sum(invoice_items.quantity) as total_quantity'),
        \DB::raw('sum(invoice_items.unit_price * invoice_items.quantity) as total_sales')
    )
    ->groupBy('products.id', 'products.name')
    ->orderBy('total_quantity', 'DESC')
    ->limit(5)
    ->get();

if ($topYesterday->count() > 0) {
    echo "✓ Top productos vendidos ayer:\n";
    foreach ($topYesterday as $item) {
        echo "  - {$item->name}: {$item->total_quantity} unidades (\${$item->total_sales})\n";
    }
} else {
    echo "⚠ No hay ventas registradas para ayer ({$yesterday})\n";
}

// Test 5: Venta más alta del mes
echo "\n📊 TEST 5: Venta más alta del MES\n";
echo str_repeat("─", 70) . "\n";

$startOfMonth = Carbon::now()->startOfMonth()->toDateString();
echo "Inicio del mes: {$startOfMonth}\n";

$highestSale = Invoice::whereDate('created_at', '>=', $startOfMonth)
    ->where('status', 'completed')
    ->orderBy('total', 'DESC')
    ->first();

if ($highestSale) {
    echo "✓ Venta más alta del mes:\n";
    echo "  Monto: \${$highestSale->total}\n";
    echo "  Fecha: {$highestSale->created_at->format('Y-m-d H:i')}\n";
    echo "  Factura #: {$highestSale->invoice_number}\n";
} else {
    echo "⚠ No hay ventas registradas este mes\n";
}

// Test 6: Buscar producto específico (Combo Gamer)
echo "\n📊 TEST 6: Búsqueda de producto específico\n";
echo str_repeat("─", 70) . "\n";

$search = "combo gamer";
echo "Buscando: '{$search}'\n";

$found = Product::where('active', true)
    ->where('name', 'like', '%' . $search . '%')
    ->first();

if ($found) {
    echo "✓ Producto encontrado: {$found->name}\n";
    echo "  Precio: \${$found->sale_price}\n";
    echo "  Stock: {$found->current_stock}\n";
} else {
    echo "⚠ No se encontró producto con ese nombre\n";
    
    // Mostrar productos similares
    $similar = Product::where('active', true)
        ->where('name', 'like', '%combo%')
        ->orWhere('name', 'like', '%gamer%')
        ->get(['name', 'sale_price']);
    
    if ($similar->count() > 0) {
        echo "  Productos similares:\n";
        foreach ($similar as $prod) {
            echo "  - {$prod->name}: \${$prod->sale_price}\n";
        }
    }
}

// Test 7: Simular llamada al endpoint AI
echo "\n📊 TEST 7: Simulación de endpoint AI\n";
echo str_repeat("─", 70) . "\n";

echo "Verificando que el AIController puede acceder a los datos...\n";

try {
    $controller = new \App\Http\Controllers\Api\AIController();
    echo "✓ AIController instanciado correctamente\n";
} catch (\Exception $e) {
    echo "✗ Error al instanciar AIController: {$e->getMessage()}\n";
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                    TEST COMPLETADO                             ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "🎯 PREGUNTAS QUE AHORA DEBE PODER RESPONDER LA IA:\n";
echo "\n";
echo "1. ¿Cuál es el producto más caro?\n";
echo "2. ¿Cuál es el producto más barato?\n";
echo "3. Dame la lista de los 26 productos\n";
echo "4. ¿Qué precio tiene el combo gamer?\n";
echo "5. ¿Cuál fue el producto más vendido ayer?\n";
echo "6. ¿Cuál fue la venta más alta del mes?\n";
echo "7. ¿Cuántos productos tengo en inventario?\n";
echo "\n";

