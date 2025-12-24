<?php

// 🔍 Debug: Ver por qué algunos productos no tienen options_summary en el POS

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simular tenant
$tenantId = 'asasas';
\Illuminate\Support\Facades\DB::purge('tenant');
config(['database.connections.tenant.database' => $tenantId . '_db']);
\Illuminate\Support\Facades\DB::reconnect('tenant');
\Illuminate\Support\Facades\DB::setDefaultConnection('tenant');

echo "🔍 Verificando productos con variantes en POS...\n\n";

// Simular la query del endpoint forPos
$products = \App\Models\Product::select([
        'id', 'name', 'sku', 'barcode', 'sale_price as price',
        'current_stock as stock', 'category_id', 'image_url as image',
        'active', 'manage_stock', 'product_type',
        'measurement_unit', 'allow_decimal'
    ])
    ->with([
        'category:id,name,color',
        'variants' => function($query) {
            $query->select('product_variants.*')
                  ->where('active', true)
                  ->with(['optionValues.option']);
        }
    ])
    ->where('active', true)
    ->whereIn('name', ['camisa nike', 'camisa polo', 'pantalones polo'])
    ->get();

echo "📦 Total productos encontrados: " . $products->count() . "\n\n";

foreach ($products as $product) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📦 Producto: {$product->name}\n";
    echo "   - ID: {$product->id}\n";
    echo "   - Tipo: {$product->product_type}\n";
    echo "   - Total variantes: " . $product->variants->count() . "\n\n";

    if ($product->variants->count() > 0) {
        foreach ($product->variants as $index => $variant) {
            $variantNumber = $index + 1;
            echo "   📌 Variante #{$variantNumber}: {$variant->sku}\n";
            echo "      - ID: {$variant->id}\n";
            echo "      - Activa: " . ($variant->active ? 'Sí' : 'No') . "\n";
            echo "      - optionValues cargados: " . $variant->optionValues->count() . "\n";

            if ($variant->optionValues->count() > 0) {
                foreach ($variant->optionValues as $ov) {
                    echo "         • {$ov->option->name}: {$ov->value}\n";
                }
            } else {
                echo "         ⚠️ NO HAY OPTION_VALUES\n";

                // Verificar directamente en la base de datos
                $directCount = \Illuminate\Support\Facades\DB::connection('tenant')
                    ->table('product_option_values')
                    ->where('product_variant_id', $variant->id)
                    ->count();

                echo "         🔍 Conteo directo en DB: {$directCount}\n";

                if ($directCount > 0) {
                    $directValues = \Illuminate\Support\Facades\DB::connection('tenant')
                        ->table('product_option_values as pov')
                        ->join('product_options as po', 'pov.product_option_id', '=', 'po.id')
                        ->where('pov.product_variant_id', $variant->id)
                        ->select('po.name', 'pov.value')
                        ->get();

                    echo "         📋 Valores directos:\n";
                    foreach ($directValues as $dv) {
                        echo "            - {$dv->name}: {$dv->value}\n";
                    }
                }
            }

            // Generar options_summary
            $optionsSummary = $variant->optionValues->map(function($optionValue) {
                return [
                    'name' => $optionValue->option->name,
                    'value' => $optionValue->value
                ];
            });

            echo "      - options_summary generado: " . json_encode($optionsSummary) . "\n\n";
        }
    }

    echo "\n";
}

echo "✅ Verificación completada\n";
