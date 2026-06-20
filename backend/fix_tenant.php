<?php
$tenant = App\Models\Tenant::find('test_pureba_online');
if ($tenant) {
    $tenant->run(function () {
        $existingWarehouse = \DB::table('warehouses')->count();
        if ($existingWarehouse == 0) {
            \DB::table('warehouses')->insert([
                'name' => 'Sede Principal',
                'address' => 'Dirección principal',
                'is_default' => true,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "Warehouse created for test_pureba_online.\n";
        } else {
            echo "Warehouse already exists.\n";
        }
    });
}
