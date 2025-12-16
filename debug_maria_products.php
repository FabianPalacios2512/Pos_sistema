<?php

/**
 * Script para verificar y habilitar productos públicos en el tenant maria
 * 
 * Uso: php backend/debug_maria_products.php
 */

require __DIR__ . '/backend/vendor/autoload.php';

$app = require_once __DIR__ . '/backend/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

echo "🔍 Buscando tenant con dominio maria.105pos.pro...\n\n";

// Buscar el tenant por dominio
$domain = DB::table('domains')->where('domain', 'maria.105pos.pro')->first();

if (!$domain) {
    echo "❌ No se encontró el dominio 'maria.105pos.pro'\n";
    echo "📋 Dominios disponibles:\n";
    DB::table('domains')->get()->each(function($d) {
        echo "   - {$d->domain} (tenant: {$d->tenant_id})\n";
    });
    exit(1);
}

$tenant = Tenant::find($domain->tenant_id);

if (!$tenant) {
    echo "❌ No se encontró el tenant con ID: {$domain->tenant_id}\n";
    exit(1);
}

echo "✅ Tenant encontrado: {$tenant->id}\n";
echo "📧 Email: {$tenant->email}\n";
echo "📊 Plan: {$tenant->plan}\n\n";

// Cambiar a la base de datos del tenant
$tenant->run(function() use ($tenant) {
    echo "🔄 Conectado a la base de datos del tenant...\n\n";
    
    // Verificar total de productos
    $totalProducts = DB::table('products')->count();
    echo "📦 Total de productos: {$totalProducts}\n";
    
    // Productos activos
    $activeProducts = DB::table('products')->where('active', true)->count();
    echo "✅ Productos activos: {$activeProducts}\n";
    
    // Productos públicos
    $publicProducts = DB::table('products')->where('is_public', true)->count();
    echo "🌐 Productos públicos (is_public=true): {$publicProducts}\n";
    
    // Productos con stock
    $productsWithStock = DB::table('products')->where('current_stock', '>', 0)->count();
    echo "📊 Productos con stock > 0: {$productsWithStock}\n";
    
    // Productos disponibles para catálogo online
    $availableOnline = DB::table('products')
        ->where('is_public', true)
        ->where('active', true)
        ->where('current_stock', '>', 0)
        ->count();
    echo "🛒 Productos disponibles online: {$availableOnline}\n\n";
    
    // Mostrar algunos productos de ejemplo
    echo "📋 Primeros 5 productos:\n";
    $sampleProducts = DB::table('products')
        ->select('id', 'name', 'active', 'is_public', 'current_stock', 'sale_price')
        ->limit(5)
        ->get();
    
    foreach ($sampleProducts as $product) {
        $status = [];
        $status[] = $product->active ? '✅ Activo' : '❌ Inactivo';
        $status[] = $product->is_public ? '🌐 Público' : '🔒 Privado';
        $status[] = $product->current_stock > 0 ? "📊 Stock: {$product->current_stock}" : '📭 Sin stock';
        
        echo sprintf(
            "   [%d] %s - \$%s - %s\n",
            $product->id,
            $product->name,
            number_format($product->sale_price, 2),
            implode(' | ', $status)
        );
    }
    
    // Si no hay productos disponibles online, ofrecer solucionarlo
    if ($availableOnline === 0 && $totalProducts > 0) {
        echo "\n⚠️  NO hay productos disponibles para el catálogo online\n";
        echo "\n💡 Soluciones posibles:\n";
        echo "   1. Asegúrate que is_public = true\n";
        echo "   2. Asegúrate que active = true\n";
        echo "   3. Asegúrate que current_stock > 0\n\n";
        
        echo "🔧 ¿Quieres habilitar TODOS los productos activos como públicos? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        $response = trim($line);
        
        if (strtolower($response) === 'y') {
            echo "\n🔄 Actualizando productos...\n";
            
            $updated = DB::table('products')
                ->where('active', true)
                ->update(['is_public' => true]);
            
            echo "✅ Se actualizaron {$updated} productos\n";
            
            // Verificar de nuevo
            $newAvailable = DB::table('products')
                ->where('is_public', true)
                ->where('active', true)
                ->where('current_stock', '>', 0)
                ->count();
            
            echo "🛒 Productos ahora disponibles online: {$newAvailable}\n";
            
            if ($newAvailable === 0) {
                echo "\n⚠️  Aún no hay productos con stock > 0\n";
                echo "💡 Agrega stock a tus productos desde el módulo de Inventario\n";
            }
        } else {
            echo "❌ Operación cancelada\n";
        }
    }
});

echo "\n✅ Verificación completada\n";
