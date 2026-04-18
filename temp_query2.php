<?php
require '/var/www/105pos/backend/vendor/autoload.php';
$app = require_once '/var/www/105pos/backend/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

tenancy()->initialize(App\Models\Tenant::find('test'));

echo "=== INVOICE ITEMS (corrected) ===" . PHP_EOL;
$items = DB::table('invoice_items as ii')
    ->join('invoices as i','ii.invoice_id','=','i.id')
    ->whereMonth('i.date', now()->month)->whereYear('i.date', now()->year)
    ->get(['ii.invoice_id','ii.product_id','ii.product_variant_id','ii.quantity','ii.unit_price','ii.cost_price']);
foreach($items as $it) {
    $subtotal = $it->quantity * $it->unit_price;
    echo "Inv:{$it->invoice_id} | prod:{$it->product_id} | var:{$it->product_variant_id} | qty:{$it->quantity} | price:{$it->unit_price} | cost:{$it->cost_price} | subtotal:{$subtotal}" . PHP_EOL;
}

echo PHP_EOL . "=== REVENUE CALCULATIONS ===" . PHP_EOL;
// BI Dashboard method: SUM(ii.quantity * ii.unit_price) for paid invoices
$biRevenue = DB::table('invoices as i')
    ->join('invoice_items as ii', 'i.id', '=', 'ii.invoice_id')
    ->whereMonth('i.date', now()->month)->whereYear('i.date', now()->year)
    ->whereIn('i.status', ['paid', 'completed'])
    ->selectRaw('COALESCE(SUM(ii.quantity * ii.unit_price), 0) as total_revenue, COALESCE(SUM(ii.quantity * COALESCE(ii.cost_price, 0)), 0) as total_cost, COUNT(DISTINCT i.id) as tx_count')
    ->first();
echo "BI Revenue (items): {$biRevenue->total_revenue}" . PHP_EOL;
echo "BI Cost (items): {$biRevenue->total_cost}" . PHP_EOL;
echo "BI tx_count: {$biRevenue->tx_count}" . PHP_EOL;

// Invoice totals method: SUM(invoice.total) for paid invoices
$invoiceTotal = DB::table('invoices')
    ->whereMonth('date', now()->month)->whereYear('date', now()->year)
    ->whereIn('status', ['paid', 'completed'])
    ->where(function($q) { $q->where('type', 'invoice')->orWhere('type', 'Factura'); })
    ->selectRaw('COALESCE(SUM(total), 0) as total')
    ->value('total');
echo "Invoice totals SUM: {$invoiceTotal}" . PHP_EOL;

echo PHP_EOL . "=== CORRECT INVENTORY CALCULATIONS ===" . PHP_EOL;
// Correct: separate simple and variable
$simple_cost = (float) DB::table('products')
    ->where('active', true)
    ->where(function ($q) { $q->whereNull('product_type')->orWhere('product_type', 'simple'); })
    ->selectRaw('COALESCE(SUM(current_stock * cost_price), 0) as val')
    ->value('val');
echo "Simple products cost inventory: {$simple_cost}" . PHP_EOL;

$variable_cost = (float) DB::table('product_variants as pv')
    ->join('products as p', 'pv.product_id', '=', 'p.id')
    ->where('p.active', true)
    ->where('p.product_type', 'variable')
    ->where('pv.active', true)
    ->selectRaw('COALESCE(SUM(pv.stock * COALESCE(pv.cost_price, p.cost_price, 0)), 0) as val')
    ->value('val');
echo "Variable products cost inventory: {$variable_cost}" . PHP_EOL;
echo "CORRECT total cost inventory: " . ($simple_cost + $variable_cost) . PHP_EOL;

$simple_sale = (float) DB::table('products')
    ->where('active', true)
    ->where(function ($q) { $q->whereNull('product_type')->orWhere('product_type', 'simple'); })
    ->selectRaw('COALESCE(SUM(current_stock * sale_price), 0) as val')
    ->value('val');
echo "Simple products sale inventory: {$simple_sale}" . PHP_EOL;

$variable_sale = (float) DB::table('product_variants as pv')
    ->join('products as p', 'pv.product_id', '=', 'p.id')
    ->where('p.active', true)
    ->where('p.product_type', 'variable')
    ->where('pv.active', true)
    ->selectRaw('COALESCE(SUM(pv.stock * COALESCE(pv.price, p.sale_price, 0)), 0) as val')
    ->value('val');
echo "Variable products sale inventory: {$variable_sale}" . PHP_EOL;
echo "CORRECT total sale inventory: " . ($simple_sale + $variable_sale) . PHP_EOL;

// NAIVE (broken) calculations - what most endpoints do
$naive = DB::table('products')
    ->where('active', true)
    ->selectRaw('COALESCE(SUM(current_stock * cost_price), 0) as cost_val, COALESCE(SUM(current_stock * sale_price), 0) as sale_val')
    ->first();
echo PHP_EOL . "NAIVE (broken) cost inventory: {$naive->cost_val}" . PHP_EOL;
echo "NAIVE (broken) sale inventory: {$naive->sale_val}" . PHP_EOL;

echo PHP_EOL . "=== UNIQUE PRODUCT COUNT ===" . PHP_EOL;
$total = DB::table('products')->where('active', true)->count();
echo "Total active products: {$total}" . PHP_EOL;

$variable = DB::table('products')->where('active', true)->where('product_type', 'variable')->count();
echo "Variable products: {$variable}" . PHP_EOL;

$simple = DB::table('products')->where('active', true)->where(function($q) { $q->whereNull('product_type')->orWhere('product_type', 'simple'); })->count();
echo "Simple products: {$simple}" . PHP_EOL;

$totalVariants = DB::table('product_variants as pv')
    ->join('products as p', 'pv.product_id', '=', 'p.id')
    ->where('p.active', true)
    ->where('pv.active', true)
    ->count();
echo "Active variants: {$totalVariants}" . PHP_EOL;
