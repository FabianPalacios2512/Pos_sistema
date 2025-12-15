<?php

use App\Models\Tenant;
use Illuminate\Support\Facades\Schema;

$tenantId = 'asasas';
$tenant = Tenant::find($tenantId);

if ($tenant) {
    tenancy()->initialize($tenant);
    echo "Tenant initialized: " . $tenant->id . "\n";
    
    Schema::dropIfExists('web_catalog_configs');
    echo "Table web_catalog_configs dropped if existed.\n";
} else {
    echo "Tenant not found.\n";
}
