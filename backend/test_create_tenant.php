<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Intentando crear tenant...\n";
    
    $tenant = \App\Models\Tenant::create([
        'id' => 'testlocal1', 
        'business_name' => 'Test Local', 
        'plan' => 'free_trial', 
        'subscription_ends_at' => now()->addDays(7)
    ]);
    
    echo "Tenant creado OK! ID: " . $tenant->id . "\n";
    
    // Crear dominio
    $tenant->domains()->create([
        'domain' => 'testlocal1.localhost'
    ]);
    
    echo "Dominio creado OK!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
