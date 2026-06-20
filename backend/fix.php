<?php
$tenants = App\Models\Tenant::all();
foreach ($tenants as $tenant) {
    $tenant->run(function () use ($tenant) {
        $brokenPath = '/storage/tenants/' . $tenant->id . '/';
        $brokenPath2 = '/storage/tenants/' . $tenant->id;
        
        \DB::table('products')->where('image_url', $brokenPath)->update(['image_url' => null]);
        \DB::table('products')->where('image_url', $brokenPath2)->update(['image_url' => null]);
        
        \DB::table('product_images')->where('image_url', $brokenPath)->delete();
        \DB::table('product_images')->where('image_url', $brokenPath2)->delete();
    });
}
echo "Imagenes rotas limpiadas correctamente.\n";
