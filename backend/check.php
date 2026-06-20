<?php
$tenant = App\Models\Tenant::find('test_pureba_online');
$tenant->run(function () {
    $warehouses = \DB::table('warehouses')->get();
    echo "Warehouses dump:\n";
    print_r($warehouses->toArray());
});
