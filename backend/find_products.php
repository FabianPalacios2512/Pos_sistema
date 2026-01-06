<?php
$tenants = App\Models\Tenant::all();
foreach($tenants as $tenant) {
    try {
        tenancy()->initialize($tenant);
        $count = App\Models\Product::count();
        if ($count > 0 && $count < 10) {
            echo "Tenant: " . $tenant->id . " has " . $count . " products.\n";
            $products = App\Models\Product::select('id', 'name', 'store_type')->get();
            foreach($products as $p) {
                echo " - " . $p->id . ": " . $p->name . " (Type: " . ($p->store_type ?? 'NULL') . ")\n";
            }
        }
        tenancy()->end();
    } catch (\Exception $e) {
        // ignore
    }
}
