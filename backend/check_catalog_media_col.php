<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$tenant = App\Models\Tenant::find('las_marcas');
$tenant->run(function () {
    // Check columns
    $cols = Schema::getColumnListing('web_catalog_configs');
    echo "Columns: " . implode(', ', $cols) . "\n\n";
    
    // Check existing record
    $config = DB::table('web_catalog_configs')->first();
    if ($config) {
        echo "Record exists. catalog_media field: ";
        $cm = $config->catalog_media ?? 'NULL';
        echo (strlen($cm) > 100 ? substr($cm, 0, 100) . '...' : $cm) . "\n";
    } else {
        echo "No record found in web_catalog_configs\n";
    }
});
