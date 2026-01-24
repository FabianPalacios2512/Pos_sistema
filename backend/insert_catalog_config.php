<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🛒 Insertando configuración de catálogo web para tenant: las_manos\n\n";

try {
    // Inicializar tenant
    tenancy()->initialize('las_manos');
    
    echo "✅ Tenant inicializado: las_manos\n";
    
    // Verificar si ya existe
    $exists = DB::table('web_catalog_configs')
        ->where('tenant_id', 'las_manos')
        ->exists();
    
    if ($exists) {
        echo "⚠️  Ya existe configuración para este tenant\n";
        echo "📊 Mostrando configuración actual:\n";
        $config = DB::table('web_catalog_configs')
            ->where('tenant_id', 'las_manos')
            ->first();
        print_r($config);
        exit;
    }
    
    // Insertar configuración por defecto
    DB::table('web_catalog_configs')->insert([
        'tenant_id' => 'las_manos',
        'store_active' => true, // ✅ ACTIVO
        'logo_url' => null,
        'banner_url' => null,
        'primary_color' => '#10B981',
        'template' => 'modern-grid',
        'visible_categories' => null,
        'show_prices' => true,
        'hide_out_of_stock' => false,
        'allow_orders' => true,
        'whatsapp_number' => null,
        'custom_message' => null,
        'delivery_cost' => 0.00,
        'minimum_order' => 0.00,
        'sync_with_cash_register' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "✅ Configuración insertada exitosamente\n";
    echo "📊 Datos insertados:\n";
    $config = DB::table('web_catalog_configs')
        ->where('tenant_id', 'las_manos')
        ->first();
    print_r($config);
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
