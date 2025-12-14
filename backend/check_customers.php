<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Customer;

echo "🔍 Investigando discrepancia de clientes\n";
echo "=========================================\n\n";

// Contar total de clientes
$totalCustomers = Customer::count();
echo "📊 Total clientes en base de datos: $totalCustomers\n\n";

// Listar todos los clientes
echo "📋 Listado completo de clientes:\n";
$customers = Customer::orderBy('id')->get(['id', 'name', 'email', 'created_at']);

foreach ($customers as $index => $customer) {
    echo sprintf(
        "%2d. ID: %2d | %-25s | %-30s | Creado: %s\n",
        $index + 1,
        $customer->id,
        $customer->name,
        $customer->email ?: 'Sin email',
        $customer->created_at->format('Y-m-d H:i:s')
    );
}

echo "\n" . str_repeat("-", 80) . "\n";
echo "Total mostrado: " . $customers->count() . " clientes\n";

// Verificar si hay clientes sin facturas
echo "\n🔍 Verificando clientes sin facturas:\n";
$customersWithoutInvoices = Customer::leftJoin('invoices', 'customers.id', '=', 'invoices.customer_id')
    ->whereNull('invoices.id')
    ->select('customers.id', 'customers.name', 'customers.email')
    ->get();

if ($customersWithoutInvoices->count() > 0) {
    echo "⚠️  Clientes sin facturas (" . $customersWithoutInvoices->count() . "):\n";
    foreach ($customersWithoutInvoices as $customer) {
        echo "   - ID: {$customer->id} | {$customer->name} | {$customer->email}\n";
    }
} else {
    echo "✅ Todos los clientes tienen al menos una factura.\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
