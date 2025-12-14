<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

echo "🔍 Investigando por qué 'sofia' no aparece en la API\n";
echo "==================================================\n\n";

// Buscar el cliente 'sofia'
$sofia = Customer::where('name', 'sofia')->first();

if (!$sofia) {
    echo "❌ No se encontró el cliente 'sofia'\n";
    exit;
}

echo "📋 Información del cliente 'sofia':\n";
echo "   ID: {$sofia->id}\n";
echo "   Nombre: {$sofia->name}\n";
echo "   Email: " . ($sofia->email ?: 'Sin email') . "\n";
echo "   Creado: {$sofia->created_at}\n\n";

// Verificar facturas de sofia
echo "💰 Facturas de sofia:\n";
$sofiaInvoices = Invoice::where('customer_id', $sofia->id)->get();

if ($sofiaInvoices->count() == 0) {
    echo "   ❌ Sofia NO tiene facturas - esto explica por qué no aparece\n\n";

    echo "🎯 SOLUCIÓN: Crear una factura para sofia\n";
    echo "¿Deseas que cree una factura de prueba para sofia? (Esto la hará aparecer en la API)\n\n";

} else {
    echo "   ✅ Sofia tiene {$sofiaInvoices->count()} factura(s)\n";

    foreach ($sofiaInvoices as $invoice) {
        echo "      - Factura {$invoice->number}: $" . number_format($invoice->total, 2) . " (Estado: {$invoice->status})\n";
    }
    echo "\n";

    // Verificar por qué no aparece en la consulta de la API
    echo "🔍 Ejecutando la misma consulta que usa la API:\n";

    $apiQuery = DB::table('customers')
        ->leftJoin('invoices', function($join) {
            $join->on('customers.id', '=', 'invoices.customer_id')
                 ->where('invoices.status', '=', 'paid'); // ← AQUÍ ESTÁ EL PROBLEMA
        })
        ->leftJoin('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
        ->leftJoin('applied_discounts', 'invoices.id', '=', 'applied_discounts.invoice_id')
        ->where('customers.id', $sofia->id)
        ->groupBy('customers.id', 'customers.name', 'customers.email', 'customers.phone',
                 'customers.city', 'customers.document_type', 'customers.document_number',
                 'customers.credit_limit', 'customers.current_debt', 'customers.has_discount',
                 'customers.discount_type', 'customers.discount_value')
        ->selectRaw('
            customers.id,
            customers.name,
            COALESCE(COUNT(DISTINCT invoices.id), 0) as total_orders,
            COALESCE(SUM(invoices.total), 0) as total_spent
        ')
        ->first();

    if ($apiQuery) {
        echo "   ✅ Sofia aparece en la consulta API:\n";
        echo "      - Órdenes: {$apiQuery->total_orders}\n";
        echo "      - Total gastado: $" . number_format($apiQuery->total_spent, 2) . "\n";
    } else {
        echo "   ❌ Sofia NO aparece en la consulta API\n";
        echo "   🔍 Revisando estado de sus facturas...\n";

        $sofiaInvoicesStatus = Invoice::where('customer_id', $sofia->id)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        foreach ($sofiaInvoicesStatus as $status) {
            echo "      - Estado '{$status->status}': {$status->count} factura(s)\n";
        }
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
