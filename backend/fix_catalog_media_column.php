<?php

/**
 * Agrega la columna catalog_media a todos los tenants
 * Uso: php fix_catalog_media_column.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$tenants = App\Models\Tenant::all();
echo "Found " . $tenants->count() . " tenants\n\n";

foreach ($tenants as $tenant) {
    $tenant->run(function () use ($tenant) {
        try {
            if (!Schema::hasColumn('web_catalog_configs', 'catalog_media')) {
                Schema::table('web_catalog_configs', function ($table) {
                    $table->longText('catalog_media')->nullable();
                });
                echo "Tenant [{$tenant->id}]: catalog_media column ADDED\n";
            } else {
                echo "Tenant [{$tenant->id}]: catalog_media column already exists\n";
            }
        } catch (Exception $e) {
            echo "Tenant [{$tenant->id}]: ERROR - " . $e->getMessage() . "\n";
        }
    });
}

echo "\nDone.\n";
