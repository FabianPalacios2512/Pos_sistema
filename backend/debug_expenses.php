<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DIAGNÓSTICO DE EXPENSES ===\n\n";

// 1. Verificar bases de datos de tenants
echo "1. TENANTS DISPONIBLES:\n";
$tenants = DB::connection('mysql')->table('tenants')->get(['id', 'business_name']);
foreach ($tenants as $tenant) {
    echo "   - {$tenant->id} ({$tenant->business_name})\n";
}

// 2. Verificar tablas en un tenant específico
echo "\n2. VERIFICANDO TABLAS EN tenant_asasasa:\n";
try {
    $tables = DB::connection('mysql')->select("
        SELECT TABLE_NAME
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = 'tenant_asasasa'
        AND TABLE_NAME LIKE 'expense%'
    ");

    if (empty($tables)) {
        echo "   ❌ NO HAY TABLAS expense_* en tenant_asasasa\n";
        echo "   SOLUCIÓN: Ejecutar migraciones para tenants\n";
    } else {
        echo "   ✅ Tablas encontradas:\n";
        foreach ($tables as $table) {
            echo "      - {$table->TABLE_NAME}\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 3. Verificar rutas registradas
echo "\n3. RUTAS DE EXPENSES REGISTRADAS:\n";
$routes = collect(\Illuminate\Support\Facades\Route::getRoutes())
    ->filter(fn($route) => str_contains($route->uri, 'expense'))
    ->map(fn($route) => [
        'method' => implode('|', $route->methods),
        'uri' => $route->uri,
        'action' => $route->getActionName()
    ]);

if ($routes->isEmpty()) {
    echo "   ❌ NO HAY RUTAS de expenses registradas\n";
} else {
    foreach ($routes as $route) {
        echo "   [{$route['method']}] /{$route['uri']}\n";
        echo "      → {$route['action']}\n";
    }
}

// 4. Verificar que los controladores existen
echo "\n4. VERIFICANDO CONTROLADORES:\n";
$controllers = [
    'ExpenseController' => 'App\\Http\\Controllers\\Api\\ExpenseController',
    'ExpenseCategoryController' => 'App\\Http\\Controllers\\Api\\ExpenseCategoryController',
];

foreach ($controllers as $name => $class) {
    if (class_exists($class)) {
        echo "   ✅ {$name} existe\n";
        $methods = get_class_methods($class);
        echo "      Métodos: " . implode(', ', array_filter($methods, fn($m) => !str_starts_with($m, '__'))) . "\n";
    } else {
        echo "   ❌ {$name} NO EXISTE\n";
    }
}

echo "\n=== FIN DEL DIAGNÓSTICO ===\n";
