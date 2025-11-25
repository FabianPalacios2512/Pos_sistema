<?php

/**
 * Script de prueba para verificar el funcionamiento del AIController
 * Ejecutar desde la raíz del proyecto backend:
 * php test-ai-backend.php
 */

require __DIR__ . '/backend/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Invoice;
use Carbon\Carbon;

// Cargar Laravel
$app = require_once __DIR__ . '/backend/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 DIAGNÓSTICO DEL SISTEMA DE IA\n";
echo "================================\n\n";

// 1. Verificar conexión a base de datos
echo "1️⃣  Verificando conexión a base de datos...\n";
try {
    DB::connection()->getPdo();
    echo "   ✅ Conexión a base de datos OK\n\n";
} catch (\Exception $e) {
    echo "   ❌ Error de conexión: " . $e->getMessage() . "\n\n";
    exit(1);
}

// 2. Verificar tablas necesarias
echo "2️⃣  Verificando tablas necesarias...\n";
$tables = ['products', 'customers', 'invoices', 'invoice_items', 'categories', 'suppliers', 'cash_sessions'];
foreach ($tables as $table) {
    try {
        $count = DB::table($table)->count();
        echo "   ✅ Tabla '$table': $count registros\n";
    } catch (\Exception $e) {
        echo "   ❌ Error en tabla '$table': " . $e->getMessage() . "\n";
    }
}
echo "\n";

// 3. Verificar estructura de invoice_items
echo "3️⃣  Verificando columnas de invoice_items...\n";
try {
    $columns = DB::select("SHOW COLUMNS FROM invoice_items");
    $columnNames = array_column($columns, 'Field');
    
    $requiredColumns = ['product_name', 'quantity', 'unit_price'];
    foreach ($requiredColumns as $col) {
        if (in_array($col, $columnNames)) {
            echo "   ✅ Columna '$col' existe\n";
        } else {
            echo "   ❌ Columna '$col' NO existe\n";
        }
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 4. Verificar API Key de Groq
echo "4️⃣  Verificando configuración de Groq...\n";
$apiKey = env('GROQ_API_KEY');
if ($apiKey) {
    $maskedKey = substr($apiKey, 0, 10) . '...' . substr($apiKey, -10);
    echo "   ✅ GROQ_API_KEY configurada: $maskedKey\n";
} else {
    echo "   ❌ GROQ_API_KEY NO configurada\n";
}
echo "\n";

// 5. Simular contexto que se enviaría a la IA
echo "5️⃣  Simulando contexto de datos...\n";
try {
    $today = Carbon::today();
    
    // Productos
    $totalProducts = Product::count();
    $activeProducts = Product::where('active', true)->count();
    echo "   📦 Productos: $totalProducts total, $activeProducts activos\n";
    
    // Clientes
    $totalCustomers = Customer::count();
    $recentCustomers = Customer::orderBy('created_at', 'desc')->limit(5)->get(['name', 'phone']);
    echo "   👥 Clientes: $totalCustomers total\n";
    foreach ($recentCustomers as $customer) {
        echo "      • {$customer->name}" . ($customer->phone ? " (Tel: {$customer->phone})" : "") . "\n";
    }
    
    // Ventas de hoy
    $salesToday = Invoice::where('type', 'invoice')
        ->where('status', '!=', 'cancelled')
        ->whereDate('date', $today)
        ->sum('total');
    $countToday = Invoice::where('type', 'invoice')
        ->where('status', '!=', 'cancelled')
        ->whereDate('date', $today)
        ->count();
    echo "   💰 Ventas hoy: $" . number_format($salesToday, 2) . " en $countToday transacciones\n";
    
    echo "   ✅ Contexto generado correctamente\n";
} catch (\Exception $e) {
    echo "   ❌ Error generando contexto: " . $e->getMessage() . "\n";
}
echo "\n";

// 6. Probar consulta de top productos
echo "6️⃣  Probando consulta de top productos vendidos hoy...\n";
try {
    $topProducts = DB::table('invoice_items')
        ->whereExists(function($query) use ($today) {
            $query->select(DB::raw(1))
                ->from('invoices')
                ->whereColumn('invoice_items.invoice_id', 'invoices.id')
                ->where('invoices.type', 'invoice')
                ->whereDate('invoices.date', $today)
                ->where('invoices.status', '!=', 'cancelled');
        })
        ->selectRaw('product_name, sum(quantity) as total_qty, sum(unit_price * quantity) as total_revenue')
        ->groupBy('product_name')
        ->orderByDesc('total_qty')
        ->limit(5)
        ->get();
    
    echo "   ✅ Consulta ejecutada correctamente\n";
    echo "   📊 Top productos hoy:\n";
    foreach ($topProducts as $product) {
        echo "      • {$product->product_name}: {$product->total_qty} unidades (\$" . number_format($product->total_revenue, 2) . ")\n";
    }
    
    if ($topProducts->isEmpty()) {
        echo "      ⚠️  No hay ventas hoy\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error en consulta: " . $e->getMessage() . "\n";
}
echo "\n";

// Resumen
echo "================================\n";
echo "✅ DIAGNÓSTICO COMPLETADO\n";
echo "================================\n\n";

echo "📝 PRÓXIMOS PASOS:\n";
echo "1. Si todo está OK, prueba la IA en el navegador\n";
echo "2. Si hay errores, revisa los mensajes arriba\n";
echo "3. Verifica que el backend esté corriendo: php artisan serve\n";
echo "4. Verifica que el frontend esté corriendo: npm run dev\n";
echo "\n";
