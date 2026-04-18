<?php
require '/var/www/105pos/backend/vendor/autoload.php';
$app = require_once '/var/www/105pos/backend/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

tenancy()->initialize(App\Models\Tenant::find('test'));

echo "=== PRODUCTS TABLE ===" . PHP_EOL;
$products = DB::table('products')->where('active', true)->get(['id','name','product_type','current_stock','cost_price','sale_price','min_stock']);
foreach($products as $p) {
    echo "ID:{$p->id} | {$p->name} | type:{$p->product_type} | stock:{$p->current_stock} | cost:{$p->cost_price} | sale:{$p->sale_price} | min:{$p->min_stock}" . PHP_EOL;
}

echo PHP_EOL . "=== PRODUCT VARIANTS ===" . PHP_EOL;
$variants = DB::table('product_variants as pv')
    ->join('products as p','pv.product_id','=','p.id')
    ->where('p.active',true)
    ->get(['pv.id','pv.product_id','pv.sku','pv.stock','pv.price','pv.cost_price','pv.active']);
foreach($variants as $v) {
    echo "VarID:{$v->id} | ProdID:{$v->product_id} | sku:{$v->sku} | stock:{$v->stock} | price:{$v->price} | cost:{$v->cost_price} | active:{$v->active}" . PHP_EOL;
}

echo PHP_EOL . "=== INVOICES THIS MONTH ===" . PHP_EOL;
$invoices = DB::table('invoices')
    ->whereMonth('date', now()->month)->whereYear('date', now()->year)
    ->get(['id','total','status','type','date']);
foreach($invoices as $i) {
    echo "InvID:{$i->id} | total:{$i->total} | status:{$i->status} | type:{$i->type} | date:{$i->date}" . PHP_EOL;
}

echo PHP_EOL . "=== INVOICE ITEMS ===" . PHP_EOL;
$items = DB::table('invoice_items as ii')
    ->join('invoices as i','ii.invoice_id','=','i.id')
    ->whereMonth('i.date', now()->month)->whereYear('i.date', now()->year)
    ->get(['ii.invoice_id','ii.product_id','ii.product_variant_id','ii.quantity','ii.unit_price','ii.cost_price','ii.total']);
foreach($items as $it) {
    echo "Inv:{$it->invoice_id} | prod:{$it->product_id} | var:{$it->product_variant_id} | qty:{$it->quantity} | price:{$it->unit_price} | cost:{$it->cost_price} | total:{$it->total}" . PHP_EOL;
}

echo PHP_EOL . "=== PRODUCT WAREHOUSE ===" . PHP_EOL;
$pw = DB::table('product_warehouse as pw')
    ->join('products as p','pw.product_id','=','p.id')
    ->where('p.active',true)
    ->get(['pw.product_id','pw.product_variant_id','pw.warehouse_id','pw.stock']);
foreach($pw as $w) {
    echo "Prod:{$w->product_id} | Var:{$w->product_variant_id} | WH:{$w->warehouse_id} | stock:{$w->stock}" . PHP_EOL;
}

echo PHP_EOL . "=== WAREHOUSES ===" . PHP_EOL;
$whs = DB::table('warehouses')->get(['id','name','is_default']);
foreach($whs as $wh) {
    echo "ID:{$wh->id} | {$wh->name} | default:{$wh->is_default}" . PHP_EOL;
}

echo PHP_EOL . "=== EXPENSES THIS MONTH ===" . PHP_EOL;
$expenses = DB::table('expenses')
    ->whereMonth('date', now()->month)->whereYear('date', now()->year)
    ->selectRaw('COALESCE(SUM(amount),0) as total')
    ->value('total');
echo "Total expenses: {$expenses}" . PHP_EOL;

echo PHP_EOL . "=== BI DASHBOARD INVENTORY VALUE CALC ===" . PHP_EOL;
$simple = (float) DB::table('products')
    ->where('active', true)
    ->where(function ($q) { $q->whereNull('product_type')->orWhere('product_type', 'simple'); })
    ->selectRaw('COALESCE(SUM(current_stock * cost_price), 0) as val')
    ->value('val');
echo "Simple products inventory (cost): {$simple}" . PHP_EOL;

$variable = (float) DB::table('product_variants as pv')
    ->join('products as p', 'pv.product_id', '=', 'p.id')
    ->where('p.active', true)
    ->where('p.product_type', 'variable')
    ->where('pv.active', true)
    ->selectRaw('COALESCE(SUM(pv.stock * COALESCE(pv.cost_price, p.cost_price, 0)), 0) as val')
    ->value('val');
echo "Variable products inventory (cost): {$variable}" . PHP_EOL;
echo "Total BI inventory value: " . ($simple + $variable) . PHP_EOL;

// Also compute sale-price based values
$simpleSale = (float) DB::table('products')
    ->where('active', true)
    ->where(function ($q) { $q->whereNull('product_type')->orWhere('product_type', 'simple'); })
    ->selectRaw('COALESCE(SUM(current_stock * sale_price), 0) as val')
    ->value('val');
echo "Simple products sale value: {$simpleSale}" . PHP_EOL;

$variableSale = (float) DB::table('product_variants as pv')
    ->join('products as p', 'pv.product_id', '=', 'p.id')
    ->where('p.active', true)
    ->where('p.product_type', 'variable')
    ->where('pv.active', true)
    ->selectRaw('COALESCE(SUM(pv.stock * COALESCE(pv.price, p.sale_price, 0)), 0) as val')
    ->value('val');
echo "Variable products sale value: {$variableSale}" . PHP_EOL;
echo "Total sale value (potential): " . ($simpleSale + $variableSale) . PHP_EOL;

// Naive calculation (what broken endpoints do)
$naive = (float) DB::table('products')
    ->where('active', true)
    ->selectRaw('COALESCE(SUM(current_stock * cost_price), 0) as cost_val, COALESCE(SUM(current_stock * sale_price), 0) as sale_val')
    ->first();
// Actually get both
$naiveRow = DB::table('products')
    ->where('active', true)
    ->selectRaw('COALESCE(SUM(current_stock * cost_price), 0) as cost_val, COALESCE(SUM(current_stock * sale_price), 0) as sale_val')
    ->first();
echo "Naive (all products, no variant join) cost_val: {$naiveRow->cost_val}" . PHP_EOL;
echo "Naive (all products, no variant join) sale_val: {$naiveRow->sale_val}" . PHP_EOL;
