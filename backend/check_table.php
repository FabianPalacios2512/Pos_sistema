<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $count = \Illuminate\Support\Facades\DB::table('cash_sessions')->count();
    echo "Table exists. Count: " . $count . "\n";

    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('cash_sessions');
    echo "Columns: " . implode(', ', $columns) . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
