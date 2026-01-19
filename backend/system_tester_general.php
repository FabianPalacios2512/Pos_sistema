<?php
/**
 * 🧪 TESTER PARA TIENDAS TIPO "GENERAL"
 *
 * Este script valida la integridad de datos para tiendas con productos SIMPLES
 * (sin variantes). Ej: papelerías, ferreterías, abarrotes, farmacias, etc.
 *
 * CARACTERÍSTICAS DE TIENDAS GENERAL:
 * - product_type = 'simple'
 * - Stock directo en products.current_stock
 * - product_warehouse usa product_variant_id = NULL
 * - store_type = 'general' en la tabla tenants
 *
 * USO: php system_tester_general.php [tenant_id]
 * Si no se especifica tenant_id, lista los tenants disponibles tipo GENERAL
 */

// Colores para terminal
define('GREEN', "\033[32m");
define('RED', "\033[31m");
define('YELLOW', "\033[33m");
define('BLUE', "\033[34m");
define('CYAN', "\033[36m");
define('RESET', "\033[0m");
define('BOLD', "\033[1m");

class GeneralStoreSystemTester {
    private $db;
    private $results = [];
    private $errors = [];
    private $warnings = [];
    private $tenantId;
    private $tenantName;
    private $tenantDb;

    public function __construct($tenantId = null) {
        $this->tenantId = $tenantId;
        $this->connectCentral();
    }

    private function connectCentral() {
        $this->db = new mysqli('localhost', 'root', '', 'pos_sistema');
        if ($this->db->connect_error) {
            die(RED . "❌ Error de conexión a base central: " . $this->db->connect_error . RESET . "\n");
        }
        $this->db->set_charset('utf8mb4');
    }

    private function connectTenant($dbName) {
        $this->db->close();
        $this->db = new mysqli('localhost', 'root', '', $dbName);
        if ($this->db->connect_error) {
            die(RED . "❌ Error de conexión a tenant: " . $this->db->connect_error . RESET . "\n");
        }
        $this->db->set_charset('utf8mb4');
        $this->tenantDb = $dbName;
    }

    private function query($sql) {
        $result = $this->db->query($sql);
        if (!$result) {
            $this->errors[] = "SQL Error: " . $this->db->error . " - Query: " . substr($sql, 0, 100);
            return false;
        }
        if ($result === true) return true;
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function test($name, $passed, $details = '') {
        $this->results[] = [
            'name' => $name,
            'passed' => $passed,
            'details' => $details
        ];

        $icon = $passed ? GREEN . "✅" : RED . "❌";
        echo "  $icon " . RESET . $name;
        if ($details) echo " " . CYAN . "($details)" . RESET;
        echo "\n";

        return $passed;
    }

    private function warning($message) {
        $this->warnings[] = $message;
        echo "  " . YELLOW . "⚠️  $message" . RESET . "\n";
    }

    private function info($message) {
        echo "  " . BLUE . "ℹ️  $message" . RESET . "\n";
    }

    public function listGeneralTenants() {
        echo "\n" . BOLD . "📋 TENANTS DISPONIBLES:" . RESET . "\n";
        echo str_repeat("─", 60) . "\n";

        $tenants = $this->query("
            SELECT id, business_name, plan, created_at
            FROM tenants
            ORDER BY id
        ");

        if (empty($tenants)) {
            echo YELLOW . "  No hay tenants registrados.\n" . RESET;
            return;
        }

        foreach ($tenants as $t) {
            // Detectar tipo de tienda basándose en productos de la BD del tenant
            $storeType = $this->detectStoreType($t['id']);
            $typeIcon = $storeType === 'fashion' ? '👗' : '📦';
            $typeColor = $storeType === 'fashion' ? MAGENTA : CYAN;

            echo "  $typeIcon " . BOLD . "[{$t['id']}]" . RESET;
            echo " {$t['business_name']}";
            echo " - " . $typeColor . $storeType . RESET;
            echo " - Plan: {$t['plan']}\n";
        }

        echo "\n" . YELLOW . "Uso: php system_tester_general.php [tenant_id]" . RESET . "\n";
        echo CYAN . "Este tester es para tiendas tipo GENERAL (productos simples)." . RESET . "\n";
    }

    private function detectStoreType($tenantId) {
        // Guardar conexión actual
        $currentDb = $this->tenantDb;

        try {
            $tenantDb = "tenant{$tenantId}";
            $testConn = @new mysqli('localhost', 'root', '', $tenantDb);

            if ($testConn->connect_error) {
                return 'unknown';
            }

            // Verificar si hay productos variable
            $result = $testConn->query("SELECT COUNT(*) as c FROM products WHERE product_type = 'variable'");
            if ($result) {
                $row = $result->fetch_assoc();
                $testConn->close();
                return $row['c'] > 0 ? 'fashion' : 'general';
            }

            $testConn->close();
            return 'general';
        } catch (\Exception $e) {
            return 'unknown';
        }
    }

    public function run() {
        if (!$this->tenantId) {
            $this->listGeneralTenants();
            return;
        }

        // Obtener info del tenant
        $tenant = $this->query("SELECT * FROM tenants WHERE id = '{$this->tenantId}'");
        if (empty($tenant)) {
            die(RED . "❌ Tenant no encontrado con ID: {$this->tenantId}" . RESET . "\n");
        }
        $tenant = $tenant[0];
        $this->tenantName = $tenant['business_name'];

        // Verificar que sea tipo general (detectando productos)
        $storeType = $this->detectStoreType($this->tenantId);
        if ($storeType === 'fashion') {
            die(RED . "❌ El tenant '{$this->tenantName}' tiene productos variables (tipo 'fashion').\n" .
                YELLOW . "   Use system_tester_fashion.php para tiendas de moda." . RESET . "\n");
        }

        echo "\n" . BOLD . "═══════════════════════════════════════════════════════════════" . RESET . "\n";
        echo BOLD . "   🧪 TESTER DE SISTEMA - TIENDA GENERAL" . RESET . "\n";
        echo BOLD . "═══════════════════════════════════════════════════════════════" . RESET . "\n";
        echo "   📍 Tenant: " . CYAN . "{$this->tenantName}" . RESET . " (ID: {$this->tenantId})\n";
        echo "   📦 Tipo: " . GREEN . "GENERAL (productos simples)" . RESET . "\n";
        echo "   📅 Fecha: " . date('Y-m-d H:i:s') . "\n";
        echo BOLD . "═══════════════════════════════════════════════════════════════" . RESET . "\n\n";

        // Conectar a la base del tenant
        $this->connectTenant("tenant{$this->tenantId}");

        // Ejecutar tests
        $this->testDatabaseStructure();
        $this->testProductIntegrity();
        $this->testStockIntegrity();
        $this->testWarehouseStock();
        $this->testInvoiceIntegrity();
        $this->testReturnsIntegrity();
        $this->testCustomerIntegrity();
        $this->testSupplierIntegrity();
        $this->testCashSessionIntegrity();
        $this->testInventoryMovements();

        // Mostrar resumen
        $this->showSummary();
    }

    private function testDatabaseStructure() {
        echo BOLD . "\n📊 1. ESTRUCTURA DE BASE DE DATOS\n" . RESET;
        echo str_repeat("─", 50) . "\n";

        $requiredTables = [
            'products', 'categories', 'suppliers', 'customers',
            'invoices', 'invoice_items', 'returns', 'return_items',
            'cash_sessions', 'inventory_movements', 'product_warehouse',
            'warehouses', 'users', 'purchase_orders', 'purchase_order_items'
        ];

        $tables = $this->query("SHOW TABLES");
        $existingTables = array_column($tables, 'Tables_in_' . $this->tenantDb);

        foreach ($requiredTables as $table) {
            $this->test("Tabla '$table' existe", in_array($table, $existingTables));
        }

        // Verificar columnas críticas de products
        $columns = $this->query("DESCRIBE products");
        $columnNames = array_column($columns, 'Field');

        $requiredColumns = ['id', 'name', 'sku', 'current_stock', 'sale_price', 'cost_price', 'category_id', 'active'];
        foreach ($requiredColumns as $col) {
            $this->test("Columna products.$col existe", in_array($col, $columnNames));
        }

        // Verificar que NO existan productos variable (sería inconsistencia)
        $variableProducts = $this->query("SELECT COUNT(*) as c FROM products WHERE product_type = 'variable'");
        $this->test("Sin productos 'variable' (coherente con tipo GENERAL)",
            $variableProducts[0]['c'] == 0,
            $variableProducts[0]['c'] > 0 ? "⚠️ Hay {$variableProducts[0]['c']} productos variable!" : '');
    }

    private function testProductIntegrity() {
        echo BOLD . "\n📦 2. INTEGRIDAD DE PRODUCTOS\n" . RESET;
        echo str_repeat("─", 50) . "\n";

        // Total productos
        $total = $this->query("SELECT COUNT(*) as c FROM products");
        $this->info("Total productos: {$total[0]['c']}");

        // Productos activos
        $active = $this->query("SELECT COUNT(*) as c FROM products WHERE active = 1");
        $this->info("Productos activos: {$active[0]['c']}");

        // SKUs duplicados
        $duplicateSku = $this->query("
            SELECT sku, COUNT(*) as c FROM products
            WHERE sku IS NOT NULL AND sku != ''
            GROUP BY sku HAVING c > 1
        ");
        $this->test("Sin SKUs duplicados", empty($duplicateSku),
            !empty($duplicateSku) ? count($duplicateSku) . " SKUs repetidos" : '');

        // Productos sin categoría
        $noCategory = $this->query("
            SELECT COUNT(*) as c FROM products
            WHERE category_id IS NULL
        ");
        if ($noCategory[0]['c'] > 0) {
            $this->warning("{$noCategory[0]['c']} productos sin categoría");
        } else {
            $this->test("Todos los productos tienen categoría", true);
        }

        // Productos con stock negativo
        $negativeStock = $this->query("SELECT COUNT(*) as c FROM products WHERE current_stock < 0");
        $this->test("Sin stock negativo en productos", $negativeStock[0]['c'] == 0,
            $negativeStock[0]['c'] > 0 ? "{$negativeStock[0]['c']} con stock negativo" : '');

        // Productos sin precio
        $noPrice = $this->query("
            SELECT COUNT(*) as c FROM products
            WHERE sale_price IS NULL OR sale_price <= 0
        ");
        if ($noPrice[0]['c'] > 0) {
            $this->warning("{$noPrice[0]['c']} productos sin precio de venta válido");
        } else {
            $this->test("Todos los productos tienen precio válido", true);
        }

        // Categorías huérfanas (sin productos)
        $orphanCategories = $this->query("
            SELECT c.id, c.name FROM categories c
            LEFT JOIN products p ON p.category_id = c.id
            WHERE p.id IS NULL
        ");
        if (!empty($orphanCategories)) {
            $this->warning(count($orphanCategories) . " categorías sin productos");
        }
    }

    private function testStockIntegrity() {
        echo BOLD . "\n📈 3. INTEGRIDAD DE STOCK (PRODUCTOS SIMPLES)\n" . RESET;
        echo str_repeat("─", 50) . "\n";

        // Verificar que product_warehouse tenga registros solo sin variante
        $warehouseWithVariants = $this->query("
            SELECT COUNT(*) as c FROM product_warehouse
            WHERE product_variant_id IS NOT NULL
        ");
        $this->test("product_warehouse sin variantes (coherente GENERAL)",
            $warehouseWithVariants[0]['c'] == 0,
            $warehouseWithVariants[0]['c'] > 0 ? "⚠️ {$warehouseWithVariants[0]['c']} registros con variante" : '');

        // Stock en products vs suma de product_warehouse
        $stockMismatch = $this->query("
            SELECT p.id, p.name, p.current_stock as stock_producto,
                   COALESCE(SUM(pw.stock), 0) as stock_bodegas
            FROM products p
            LEFT JOIN product_warehouse pw ON pw.product_id = p.id AND pw.product_variant_id IS NULL
            GROUP BY p.id
            HAVING stock_producto != stock_bodegas
        ");

        $this->test("Stock productos = Stock en bodegas", empty($stockMismatch),
            !empty($stockMismatch) ? count($stockMismatch) . " productos con diferencia" : '');

        if (!empty($stockMismatch)) {
            echo "\n    " . YELLOW . "Productos con diferencia de stock:" . RESET . "\n";
            foreach (array_slice($stockMismatch, 0, 5) as $p) {
                echo "      - [{$p['id']}] {$p['name']}: producto={$p['stock_producto']}, bodegas={$p['stock_bodegas']}\n";
            }
            if (count($stockMismatch) > 5) {
                echo "      ... y " . (count($stockMismatch) - 5) . " más\n";
            }
        }

        // Stock negativo en bodegas
        $negativeWarehouse = $this->query("
            SELECT COUNT(*) as c FROM product_warehouse WHERE stock < 0
        ");
        $this->test("Sin stock negativo en bodegas", $negativeWarehouse[0]['c'] == 0,
            $negativeWarehouse[0]['c'] > 0 ? "{$negativeWarehouse[0]['c']} registros negativos" : '');
    }

    private function testWarehouseStock() {
        echo BOLD . "\n🏭 4. INTEGRIDAD MULTI-BODEGA\n" . RESET;
        echo str_repeat("─", 50) . "\n";

        // Contar bodegas
        $warehouses = $this->query("SELECT COUNT(*) as c FROM warehouses");
        $this->info("Total bodegas: {$warehouses[0]['c']}");

        if ($warehouses[0]['c'] == 0) {
            $this->warning("No hay bodegas configuradas - Sistema en modo básico");
            return;
        }

        // Bodegas activas
        $activeWarehouses = $this->query("SELECT COUNT(*) as c FROM warehouses WHERE active = 1");
        $this->info("Bodegas activas: {$activeWarehouses[0]['c']}");

        // Productos sin asignar a ninguna bodega
        $productsNoWarehouse = $this->query("
            SELECT COUNT(*) as c FROM products p
            LEFT JOIN product_warehouse pw ON pw.product_id = p.id
            WHERE pw.id IS NULL AND p.active = 1
        ");
        if ($productsNoWarehouse[0]['c'] > 0) {
            $this->warning("{$productsNoWarehouse[0]['c']} productos activos sin asignar a bodegas");
        } else {
            $this->test("Todos los productos activos en al menos una bodega", true);
        }

        // Duplicados en product_warehouse (mismo producto, misma bodega, sin variante)
        $duplicateWarehouse = $this->query("
            SELECT product_id, warehouse_id, COUNT(*) as c
            FROM product_warehouse
            WHERE product_variant_id IS NULL
            GROUP BY product_id, warehouse_id
            HAVING c > 1
        ");
        $this->test("Sin duplicados producto-bodega", empty($duplicateWarehouse),
            !empty($duplicateWarehouse) ? count($duplicateWarehouse) . " duplicados" : '');
    }

    private function testInvoiceIntegrity() {
        echo BOLD . "\n🧾 5. INTEGRIDAD DE FACTURAS\n" . RESET;
        echo str_repeat("─", 50) . "\n";

        // Total facturas
        $total = $this->query("SELECT COUNT(*) as c FROM invoices");
        $this->info("Total facturas: {$total[0]['c']}");

        if ($total[0]['c'] == 0) {
            $this->warning("No hay facturas - Sistema sin transacciones");
            return;
        }

        // Facturas por estado
        $byStatus = $this->query("SELECT status, COUNT(*) as c FROM invoices GROUP BY status");
        foreach ($byStatus as $s) {
            $this->info("  - {$s['status']}: {$s['c']}");
        }

        // Facturas sin items
        $noItems = $this->query("
            SELECT i.id, i.number FROM invoices i
            LEFT JOIN invoice_items ii ON ii.invoice_id = i.id
            WHERE ii.id IS NULL AND i.status != 'returned'
        ");
        $this->test("Facturas tienen items", empty($noItems),
            !empty($noItems) ? count($noItems) . " facturas vacías" : '');

        // Verificar totales (considerando descuentos y recargos a nivel de factura)
        $mismatchTotals = $this->query("
            SELECT i.id, i.number, i.total as factura_total,
                   COALESCE(SUM(ii.subtotal - COALESCE(ii.discount_amount, 0) + COALESCE(ii.tax_amount, 0)), 0)
                   + COALESCE(i.surcharge_amount, 0) as items_total_with_surcharge
            FROM invoices i
            LEFT JOIN invoice_items ii ON ii.invoice_id = i.id
            WHERE i.status = 'paid'
            GROUP BY i.id
            HAVING ABS(factura_total - items_total_with_surcharge) > 1
        ");
        $this->test("Totales facturas = Suma items + recargos", empty($mismatchTotals),
            !empty($mismatchTotals) ? count($mismatchTotals) . " con diferencia" : '');

        // Facturas con números duplicados
        $duplicateNumbers = $this->query("
            SELECT number, COUNT(*) as c FROM invoices
            GROUP BY number HAVING c > 1
        ");
        $this->test("Sin números de factura duplicados", empty($duplicateNumbers),
            !empty($duplicateNumbers) ? count($duplicateNumbers) . " duplicados" : '');

        // Items con productos que no existen
        $orphanItems = $this->query("
            SELECT COUNT(*) as c FROM invoice_items ii
            LEFT JOIN products p ON p.id = ii.product_id
            WHERE p.id IS NULL
        ");
        $this->test("Items referencian productos existentes", $orphanItems[0]['c'] == 0,
            $orphanItems[0]['c'] > 0 ? "{$orphanItems[0]['c']} items huérfanos" : '');
    }

    private function testReturnsIntegrity() {
        echo BOLD . "\n↩️  6. INTEGRIDAD DE DEVOLUCIONES\n" . RESET;
        echo str_repeat("─", 50) . "\n";

        // Verificar si la tabla existe (puede ser 'returns' o 'product_returns')
        $tables = $this->query("SHOW TABLES LIKE 'returns'");
        $returnsTable = !empty($tables) ? 'returns' : null;

        if (!$returnsTable) {
            $tables = $this->query("SHOW TABLES LIKE 'product_returns'");
            $returnsTable = !empty($tables) ? 'product_returns' : null;
        }

        if (!$returnsTable) {
            $this->warning("Tabla de devoluciones no existe - Saltando tests");
            return;
        }

        $total = $this->query("SELECT COUNT(*) as c FROM $returnsTable");
        $this->info("Total devoluciones: {$total[0]['c']}");

        if ($total[0]['c'] == 0) {
            $this->info("No hay devoluciones registradas");
            return;
        }

        // Devoluciones sin factura original
        $noInvoice = $this->query("
            SELECT r.id, r.number FROM $returnsTable r
            LEFT JOIN invoices i ON i.id = r.original_invoice_id
            WHERE i.id IS NULL
        ");
        $this->test("Devoluciones tienen factura original", empty($noInvoice),
            !empty($noInvoice) ? count($noInvoice) . " sin factura" : '');

        // Devoluciones por estado
        $byStatus = $this->query("SELECT status, COUNT(*) as c FROM $returnsTable GROUP BY status");
        foreach ($byStatus as $s) {
            $this->info("  - {$s['status']}: {$s['c']}");
        }

        // Verificar return_items si existe
        $returnItemsTable = $this->query("SHOW TABLES LIKE 'return_items'");
        if (!empty($returnItemsTable)) {
            $itemsCount = $this->query("SELECT COUNT(*) as c FROM return_items");
            $this->info("Items de devolución: {$itemsCount[0]['c']}");

            // Items huérfanos
            $orphanItems = $this->query("
                SELECT COUNT(*) as c FROM return_items ri
                LEFT JOIN $returnsTable r ON r.id = ri.return_id
                WHERE r.id IS NULL
            ");
            $this->test("Sin items de devolución huérfanos", $orphanItems[0]['c'] == 0,
                $orphanItems[0]['c'] > 0 ? "{$orphanItems[0]['c']} huérfanos" : '');
        }
    }

    private function testCustomerIntegrity() {
        echo BOLD . "\n👤 7. INTEGRIDAD DE CLIENTES\n" . RESET;
        echo str_repeat("─", 50) . "\n";

        $total = $this->query("SELECT COUNT(*) as c FROM customers");
        $this->info("Total clientes: {$total[0]['c']}");

        // Verificar columnas disponibles
        $columns = $this->query("DESCRIBE customers");
        $columnNames = array_column($columns, 'Field');

        $docColumn = in_array('document_number', $columnNames) ? 'document_number' :
                     (in_array('document', $columnNames) ? 'document' : null);

        if ($docColumn) {
            // Documentos duplicados
            $duplicateDocs = $this->query("
                SELECT $docColumn, COUNT(*) as c FROM customers
                WHERE $docColumn IS NOT NULL AND $docColumn != ''
                GROUP BY $docColumn HAVING c > 1
            ");
            $this->test("Sin documentos duplicados", empty($duplicateDocs),
                !empty($duplicateDocs) ? count($duplicateDocs) . " duplicados" : '');
        } else {
            $this->warning("No se encontró columna de documento en clientes");
        }

        // Clientes con deuda negativa
        if (in_array('current_debt', $columnNames)) {
            $negativeDebt = $this->query("
                SELECT COUNT(*) as c FROM customers WHERE current_debt < 0
            ");
            $this->test("Sin deuda negativa", $negativeDebt[0]['c'] == 0,
                $negativeDebt[0]['c'] > 0 ? "{$negativeDebt[0]['c']} clientes" : '');
        }
    }

    private function testSupplierIntegrity() {
        echo BOLD . "\n🏪 8. INTEGRIDAD DE PROVEEDORES\n" . RESET;
        echo str_repeat("─", 50) . "\n";

        $total = $this->query("SELECT COUNT(*) as c FROM suppliers");
        $this->info("Total proveedores: {$total[0]['c']}");

        if ($total[0]['c'] == 0) {
            $this->warning("No hay proveedores registrados");
            return;
        }

        // Verificar columnas disponibles
        $columns = $this->query("DESCRIBE suppliers");
        $columnNames = array_column($columns, 'Field');

        // Proveedores sin productos
        $noProducts = $this->query("
            SELECT s.id, s.name FROM suppliers s
            LEFT JOIN products p ON p.supplier_id = s.id
            WHERE p.id IS NULL
        ");
        if (!empty($noProducts)) {
            $this->warning(count($noProducts) . " proveedores sin productos asociados");
        }

        // Proveedores con deuda (les debemos a ellos)
        $debtColumn = in_array('current_debt', $columnNames) ? 'current_debt' :
                      (in_array('current_balance', $columnNames) ? 'current_balance' : null);

        if ($debtColumn) {
            $withDebt = $this->query("SELECT COUNT(*) as c FROM suppliers WHERE $debtColumn > 0");
            if ($withDebt[0]['c'] > 0) {
                $this->info("{$withDebt[0]['c']} proveedores con deuda pendiente (les debemos)");
            }
        }

        // Proveedores activos
        if (in_array('active', $columnNames)) {
            $active = $this->query("SELECT COUNT(*) as c FROM suppliers WHERE active = 1");
            $this->info("Proveedores activos: {$active[0]['c']}");
        }
    }

    private function testCashSessionIntegrity() {
        echo BOLD . "\n💰 9. INTEGRIDAD DE SESIONES DE CAJA\n" . RESET;
        echo str_repeat("─", 50) . "\n";

        $total = $this->query("SELECT COUNT(*) as c FROM cash_sessions");
        $this->info("Total sesiones: {$total[0]['c']}");

        if ($total[0]['c'] == 0) {
            $this->warning("No hay sesiones de caja");
            return;
        }

        // Sesiones abiertas
        $open = $this->query("SELECT COUNT(*) as c FROM cash_sessions WHERE status = 'open'");
        $this->info("Sesiones abiertas: {$open[0]['c']}");

        // Múltiples sesiones abiertas por usuario
        $multipleOpen = $this->query("
            SELECT user_id, COUNT(*) as c FROM cash_sessions
            WHERE status = 'open'
            GROUP BY user_id HAVING c > 1
        ");
        $this->test("Máximo 1 sesión abierta por usuario", empty($multipleOpen),
            !empty($multipleOpen) ? count($multipleOpen) . " usuarios con múltiples cajas" : '');

        // Verificar columnas disponibles para tests adicionales
        $columns = $this->query("DESCRIBE cash_sessions");
        $columnNames = array_column($columns, 'Field');

        // Sesiones con montos negativos (si existe la columna)
        if (in_array('opening_amount', $columnNames)) {
            $negativeOpening = $this->query("
                SELECT COUNT(*) as c FROM cash_sessions WHERE opening_amount < 0
            ");
            $this->test("Sin montos de apertura negativos", $negativeOpening[0]['c'] == 0,
                $negativeOpening[0]['c'] > 0 ? "{$negativeOpening[0]['c']} sesiones" : '');
        }

        // Sesiones cerradas sin closing_amount
        if (in_array('closing_amount', $columnNames)) {
            $closedNoAmount = $this->query("
                SELECT COUNT(*) as c FROM cash_sessions
                WHERE status = 'closed' AND closing_amount IS NULL
            ");
            if ($closedNoAmount[0]['c'] > 0) {
                $this->warning("{$closedNoAmount[0]['c']} sesiones cerradas sin monto de cierre");
            }
        }
    }

    private function testInventoryMovements() {
        echo BOLD . "\n📊 10. MOVIMIENTOS DE INVENTARIO\n" . RESET;
        echo str_repeat("─", 50) . "\n";

        $total = $this->query("SELECT COUNT(*) as c FROM inventory_movements");
        $this->info("Total movimientos: {$total[0]['c']}");

        if ($total[0]['c'] == 0) {
            $this->warning("No hay movimientos de inventario registrados");
            return;
        }

        // Movimientos por tipo
        $byType = $this->query("SELECT type, COUNT(*) as c FROM inventory_movements GROUP BY type");
        foreach ($byType as $t) {
            $this->info("  - {$t['type']}: {$t['c']}");
        }

        // Movimientos con productos que no existen
        $orphanMov = $this->query("
            SELECT COUNT(*) as c FROM inventory_movements im
            LEFT JOIN products p ON p.id = im.product_id
            WHERE p.id IS NULL
        ");
        $this->test("Movimientos referencian productos existentes", $orphanMov[0]['c'] == 0,
            $orphanMov[0]['c'] > 0 ? "{$orphanMov[0]['c']} huérfanos" : '');
    }

    private function showSummary() {
        echo "\n" . BOLD . "═══════════════════════════════════════════════════════════════" . RESET . "\n";
        echo BOLD . "   📋 RESUMEN DE PRUEBAS - TIENDA GENERAL" . RESET . "\n";
        echo BOLD . "═══════════════════════════════════════════════════════════════" . RESET . "\n";

        $passed = count(array_filter($this->results, fn($r) => $r['passed']));
        $failed = count(array_filter($this->results, fn($r) => !$r['passed']));
        $total = count($this->results);

        echo "\n   " . GREEN . "✅ Pasadas: $passed" . RESET . "\n";
        echo "   " . RED . "❌ Fallidas: $failed" . RESET . "\n";
        echo "   " . YELLOW . "⚠️  Advertencias: " . count($this->warnings) . RESET . "\n";
        echo "\n";

        $percentage = $total > 0 ? round(($passed / $total) * 100, 1) : 0;

        if ($percentage >= 90) {
            echo "   " . GREEN . BOLD . "🎉 SISTEMA APROBADO ($percentage%)" . RESET . "\n";
            echo "   " . GREEN . "El sistema está listo para producción." . RESET . "\n";
        } elseif ($percentage >= 70) {
            echo "   " . YELLOW . BOLD . "⚠️  SISTEMA CON OBSERVACIONES ($percentage%)" . RESET . "\n";
            echo "   " . YELLOW . "Revisar las advertencias antes de producción." . RESET . "\n";
        } else {
            echo "   " . RED . BOLD . "❌ SISTEMA NO APROBADO ($percentage%)" . RESET . "\n";
            echo "   " . RED . "Corregir los errores antes de continuar." . RESET . "\n";
        }

        if (!empty($this->errors)) {
            echo "\n   " . RED . "Errores SQL detectados:" . RESET . "\n";
            foreach ($this->errors as $e) {
                echo "   - $e\n";
            }
        }

        echo "\n" . BOLD . "═══════════════════════════════════════════════════════════════" . RESET . "\n\n";
    }
}

// Ejecutar
$tenantId = isset($argv[1]) ? $argv[1] : null;
$tester = new GeneralStoreSystemTester($tenantId);
$tester->run();
