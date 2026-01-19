<?php
/**
 * 🧪 TESTER PARA TIENDAS TIPO "FASHION" (MODA)
 *
 * Este script valida la integridad de datos para tiendas con productos VARIABLES
 * (con variantes de talla, color, etc). Ej: tiendas de ropa, zapaterías, accesorios.
 *
 * CARACTERÍSTICAS DE TIENDAS FASHION:
 * - product_type = 'variable'
 * - Stock principal calculado: SUM(product_variants.stock)
 * - product_warehouse usa product_variant_id != NULL
 * - Cada variante tiene su propio SKU, precio y stock
 * - store_type = 'fashion' en la tabla tenants
 *
 * USO: php system_tester_fashion.php [tenant_id]
 * Si no se especifica tenant_id, lista los tenants disponibles tipo FASHION
 */

// Colores para terminal
define('GREEN', "\033[32m");
define('RED', "\033[31m");
define('YELLOW', "\033[33m");
define('BLUE', "\033[34m");
define('CYAN', "\033[36m");
define('MAGENTA', "\033[35m");
define('RESET', "\033[0m");
define('BOLD', "\033[1m");

class FashionStoreSystemTester {
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

    private function tableExists($tableName) {
        $result = $this->query("SHOW TABLES LIKE '$tableName'");
        return !empty($result);
    }

    private function columnExists($table, $column) {
        $result = $this->query("SHOW COLUMNS FROM $table LIKE '$column'");
        return !empty($result);
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

    public function listFashionTenants() {
        echo "\n" . BOLD . "👗 TENANTS DISPONIBLES:" . RESET . "\n";
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

        $hasFashion = false;
        foreach ($tenants as $t) {
            // Detectar tipo de tienda basándose en productos de la BD del tenant
            $storeType = $this->detectStoreType($t['id']);
            $typeIcon = $storeType === 'fashion' ? '👗' : '📦';
            $typeColor = $storeType === 'fashion' ? MAGENTA : CYAN;

            if ($storeType === 'fashion') $hasFashion = true;

            echo "  $typeIcon " . BOLD . "[{$t['id']}]" . RESET;
            echo " {$t['business_name']}";
            echo " - " . $typeColor . $storeType . RESET;
            echo " - Plan: {$t['plan']}\n";
        }

        if (!$hasFashion) {
            echo "\n" . YELLOW . "  ⚠️ No hay tenants tipo FASHION detectados." . RESET . "\n";
            echo CYAN . "  Los tenants existentes son tipo GENERAL (productos simples)." . RESET . "\n";
        }

        echo "\n" . YELLOW . "Uso: php system_tester_fashion.php [tenant_id]" . RESET . "\n";
        echo MAGENTA . "Este tester es para tiendas tipo FASHION (productos con variantes)." . RESET . "\n";
    }

    private function detectStoreType($tenantId) {
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
            $this->listFashionTenants();
            return;
        }

        // Obtener info del tenant
        $tenant = $this->query("SELECT * FROM tenants WHERE id = '{$this->tenantId}'");
        if (empty($tenant)) {
            die(RED . "❌ Tenant no encontrado con ID: {$this->tenantId}" . RESET . "\n");
        }
        $tenant = $tenant[0];
        $this->tenantName = $tenant['business_name'];

        // Verificar que sea tipo fashion (detectando productos)
        $storeType = $this->detectStoreType($this->tenantId);
        if ($storeType !== 'fashion') {
            die(RED . "❌ El tenant '{$this->tenantName}' es tipo '$storeType', no 'fashion'.\n" .
                YELLOW . "   Use system_tester_general.php para tiendas generales." . RESET . "\n");
        }

        echo "\n" . BOLD . "═══════════════════════════════════════════════════════════════" . RESET . "\n";
        echo BOLD . "   👗 TESTER DE SISTEMA - TIENDA FASHION (MODA)" . RESET . "\n";
        echo BOLD . "═══════════════════════════════════════════════════════════════" . RESET . "\n";
        echo "   📍 Tenant: " . MAGENTA . "{$this->tenantName}" . RESET . " (ID: {$this->tenantId})\n";
        echo "   📦 Tipo: " . MAGENTA . "FASHION (productos con variantes)" . RESET . "\n";
        echo "   📅 Fecha: " . date('Y-m-d H:i:s') . "\n";
        echo BOLD . "═══════════════════════════════════════════════════════════════" . RESET . "\n\n";

        // Conectar a la base del tenant
        $this->connectTenant("tenant{$this->tenantId}");

        // Ejecutar tests
        $this->testDatabaseStructure();
        $this->testVariantTables();
        $this->testProductIntegrity();
        $this->testVariantIntegrity();
        $this->testStockIntegrity();
        $this->testWarehouseVariantStock();
        $this->testInvoiceIntegrity();
        $this->testReturnsIntegrity();
        $this->testPurchaseOrdersVariants();
        $this->testCustomerIntegrity();
        $this->testCashSessionIntegrity();

        // Mostrar resumen
        $this->showSummary();
    }

    private function testDatabaseStructure() {
        echo BOLD . "\n📊 1. ESTRUCTURA DE BASE DE DATOS (FASHION)\n" . RESET;
        echo str_repeat("─", 50) . "\n";

        $requiredTables = [
            'products', 'product_variants', 'product_options', 'product_option_values',
            'categories', 'suppliers', 'customers',
            'invoices', 'invoice_items', 'returns', 'return_items',
            'cash_sessions', 'inventory_movements', 'product_warehouse',
            'warehouses', 'users', 'purchase_orders', 'purchase_order_items'
        ];

        $tables = $this->query("SHOW TABLES");
        $existingTables = array_column($tables, 'Tables_in_' . $this->tenantDb);

        foreach ($requiredTables as $table) {
            $exists = in_array($table, $existingTables);
            $this->test("Tabla '$table' existe", $exists);

            if (!$exists && in_array($table, ['product_variants', 'product_options', 'product_option_values'])) {
                $this->warning("⚠️ Tabla crítica para FASHION no existe!");
            }
        }
    }

    private function testVariantTables() {
        echo BOLD . "\n👔 2. ESTRUCTURA DE TABLAS DE VARIANTES\n" . RESET;
        echo str_repeat("─", 50) . "\n";

        // Verificar columnas de product_variants
        if (!$this->tableExists('product_variants')) {
            $this->test("Tabla product_variants existe", false, "CRÍTICO para fashion");
            return;
        }

        $variantColumns = $this->query("DESCRIBE product_variants");
        $columnNames = array_column($variantColumns, 'Field');

        $requiredVariantCols = ['id', 'product_id', 'sku', 'price', 'cost_price', 'stock', 'options_summary', 'active'];
        foreach ($requiredVariantCols as $col) {
            $this->test("Columna product_variants.$col existe", in_array($col, $columnNames));
        }

        // Verificar product_warehouse tiene product_variant_id
        if ($this->tableExists('product_warehouse')) {
            $warehouseCols = $this->query("DESCRIBE product_warehouse");
            $warehouseColNames = array_column($warehouseCols, 'Field');

            $this->test("Columna product_warehouse.product_variant_id existe",
                in_array('product_variant_id', $warehouseColNames),
                "Necesario para stock por variante y bodega");
        }

        // Verificar purchase_order_items tiene variant_id
        if ($this->tableExists('purchase_order_items')) {
            $poiCols = $this->query("DESCRIBE purchase_order_items");
            $poiColNames = array_column($poiCols, 'Field');

            $this->test("Columna purchase_order_items.variant_id existe",
                in_array('variant_id', $poiColNames),
                "Necesario para compras de variantes");
        }
    }

    private function testProductIntegrity() {
        echo BOLD . "\n📦 3. INTEGRIDAD DE PRODUCTOS\n" . RESET;
        echo str_repeat("─", 50) . "\n";

        // Total productos
        $total = $this->query("SELECT COUNT(*) as c FROM products");
        $this->info("Total productos: {$total[0]['c']}");

        // Por tipo
        $byType = $this->query("SELECT product_type, COUNT(*) as c FROM products GROUP BY product_type");
        foreach ($byType as $t) {
            $type = $t['product_type'] ?: 'simple';
            $icon = $type === 'variable' ? '👔' : '📦';
            $this->info("  $icon $type: {$t['c']}");
        }

        // Productos variable vs simple
        $variable = $this->query("SELECT COUNT(*) as c FROM products WHERE product_type = 'variable'");
        $simple = $this->query("SELECT COUNT(*) as c FROM products WHERE product_type != 'variable' OR product_type IS NULL");

        if ($variable[0]['c'] == 0) {
            $this->warning("No hay productos tipo 'variable' - ¿Es realmente una tienda FASHION?");
        }

        // Productos activos
        $active = $this->query("SELECT COUNT(*) as c FROM products WHERE active = 1");
        $this->info("Productos activos: {$active[0]['c']}");
    }

    private function testVariantIntegrity() {
        echo BOLD . "\n👗 4. INTEGRIDAD DE VARIANTES\n" . RESET;
        echo str_repeat("─", 50) . "\n";

        if (!$this->tableExists('product_variants')) {
            $this->warning("Tabla product_variants no existe - No se pueden probar variantes");
            return;
        }

        // Total variantes
        $total = $this->query("SELECT COUNT(*) as c FROM product_variants");
        $this->info("Total variantes: {$total[0]['c']}");

        if ($total[0]['c'] == 0) {
            $this->warning("No hay variantes creadas");
            return;
        }

        // Productos variable SIN variantes (error crítico)
        $productsNoVariants = $this->query("
            SELECT p.id, p.name FROM products p
            LEFT JOIN product_variants pv ON pv.product_id = p.id
            WHERE p.product_type = 'variable' AND pv.id IS NULL AND p.active = 1
        ");
        $this->test("Productos variable tienen variantes", empty($productsNoVariants),
            !empty($productsNoVariants) ? count($productsNoVariants) . " productos sin variantes!" : '');

        if (!empty($productsNoVariants)) {
            echo "\n    " . RED . "Productos variable sin variantes:" . RESET . "\n";
            foreach (array_slice($productsNoVariants, 0, 5) as $p) {
                echo "      - [{$p['id']}] {$p['name']}\n";
            }
        }

        // Variantes huérfanas (sin producto padre)
        $orphanVariants = $this->query("
            SELECT pv.id, pv.sku FROM product_variants pv
            LEFT JOIN products p ON p.id = pv.product_id
            WHERE p.id IS NULL
        ");
        $this->test("Sin variantes huérfanas", empty($orphanVariants),
            !empty($orphanVariants) ? count($orphanVariants) . " variantes sin producto" : '');

        // SKUs de variantes duplicados
        $duplicateSku = $this->query("
            SELECT sku, COUNT(*) as c FROM product_variants
            WHERE sku IS NOT NULL AND sku != ''
            GROUP BY sku HAVING c > 1
        ");
        $this->test("Sin SKUs de variantes duplicados", empty($duplicateSku),
            !empty($duplicateSku) ? count($duplicateSku) . " SKUs repetidos" : '');

        // Variantes con stock negativo
        $negativeStock = $this->query("SELECT COUNT(*) as c FROM product_variants WHERE stock < 0");
        $this->test("Sin stock negativo en variantes", $negativeStock[0]['c'] == 0,
            $negativeStock[0]['c'] > 0 ? "{$negativeStock[0]['c']} con stock negativo" : '');

        // Variantes sin options_summary
        $noOptions = $this->query("
            SELECT COUNT(*) as c FROM product_variants
            WHERE options_summary IS NULL OR options_summary = ''
        ");
        if ($noOptions[0]['c'] > 0) {
            $this->warning("{$noOptions[0]['c']} variantes sin options_summary (difícil identificar)");
        }
    }

    private function testStockIntegrity() {
        echo BOLD . "\n📈 5. INTEGRIDAD DE STOCK (PRODUCTOS VARIABLES)\n" . RESET;
        echo str_repeat("─", 50) . "\n";

        if (!$this->tableExists('product_variants')) {
            $this->warning("No se puede verificar stock de variantes - tabla no existe");
            return;
        }

        // Stock en producto padre vs suma de variantes
        $stockMismatch = $this->query("
            SELECT p.id, p.name, p.current_stock as stock_producto,
                   COALESCE(SUM(pv.stock), 0) as stock_variantes
            FROM products p
            LEFT JOIN product_variants pv ON pv.product_id = p.id
            WHERE p.product_type = 'variable'
            GROUP BY p.id
            HAVING ABS(stock_producto - stock_variantes) > 0
        ");

        $this->test("Stock producto = Suma de variantes", empty($stockMismatch),
            !empty($stockMismatch) ? count($stockMismatch) . " productos con diferencia" : '');

        if (!empty($stockMismatch)) {
            echo "\n    " . YELLOW . "Productos con diferencia de stock:" . RESET . "\n";
            foreach (array_slice($stockMismatch, 0, 5) as $p) {
                echo "      - [{$p['id']}] {$p['name']}: padre={$p['stock_producto']}, variantes={$p['stock_variantes']}\n";
            }
            if (count($stockMismatch) > 5) {
                echo "      ... y " . (count($stockMismatch) - 5) . " más\n";
            }
        }

        // Productos simples también deben tener stock coherente
        $simpleStockCheck = $this->query("
            SELECT COUNT(*) as c FROM products
            WHERE (product_type != 'variable' OR product_type IS NULL)
            AND current_stock < 0
        ");
        $this->test("Productos simples sin stock negativo", $simpleStockCheck[0]['c'] == 0,
            $simpleStockCheck[0]['c'] > 0 ? "{$simpleStockCheck[0]['c']} con stock negativo" : '');
    }

    private function testWarehouseVariantStock() {
        echo BOLD . "\n🏭 6. STOCK POR BODEGA Y VARIANTE\n" . RESET;
        echo str_repeat("─", 50) . "\n";

        // Contar bodegas
        $warehouses = $this->query("SELECT COUNT(*) as c FROM warehouses");
        $this->info("Total bodegas: {$warehouses[0]['c']}");

        if (!$this->tableExists('product_warehouse')) {
            $this->warning("Tabla product_warehouse no existe");
            return;
        }

        if (!$this->columnExists('product_warehouse', 'product_variant_id')) {
            $this->warning("product_warehouse no tiene columna product_variant_id - No soporta stock por variante");
            return;
        }

        // Stock de variantes en product_warehouse vs product_variants
        $variantWarehouseMismatch = $this->query("
            SELECT pv.id, pv.sku, pv.stock as stock_variante,
                   COALESCE(SUM(pw.stock), 0) as stock_bodegas
            FROM product_variants pv
            LEFT JOIN product_warehouse pw ON pw.product_variant_id = pv.id
            GROUP BY pv.id
            HAVING ABS(stock_variante - stock_bodegas) > 0
        ");

        $this->test("Stock variante = Stock en bodegas", empty($variantWarehouseMismatch),
            !empty($variantWarehouseMismatch) ? count($variantWarehouseMismatch) . " variantes con diferencia" : '');

        if (!empty($variantWarehouseMismatch)) {
            echo "\n    " . YELLOW . "Variantes con diferencia de stock:" . RESET . "\n";
            foreach (array_slice($variantWarehouseMismatch, 0, 5) as $v) {
                echo "      - [{$v['id']}] {$v['sku']}: variante={$v['stock_variante']}, bodegas={$v['stock_bodegas']}\n";
            }
        }

        // Registros duplicados (misma variante, misma bodega)
        $duplicateWarehouse = $this->query("
            SELECT product_id, product_variant_id, warehouse_id, COUNT(*) as c
            FROM product_warehouse
            WHERE product_variant_id IS NOT NULL
            GROUP BY product_id, product_variant_id, warehouse_id
            HAVING c > 1
        ");
        $this->test("Sin duplicados variante-bodega", empty($duplicateWarehouse),
            !empty($duplicateWarehouse) ? count($duplicateWarehouse) . " duplicados" : '');

        // Stock negativo en bodegas
        $negativeWarehouse = $this->query("
            SELECT COUNT(*) as c FROM product_warehouse WHERE stock < 0
        ");
        $this->test("Sin stock negativo en bodegas", $negativeWarehouse[0]['c'] == 0,
            $negativeWarehouse[0]['c'] > 0 ? "{$negativeWarehouse[0]['c']} registros negativos" : '');
    }

    private function testInvoiceIntegrity() {
        echo BOLD . "\n🧾 7. INTEGRIDAD DE FACTURAS\n" . RESET;
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

        // Items de productos variable - verificar que tengan variante asociada
        // (Este es un check importante para FASHION)
        // Nota: Actualmente invoice_items puede no tener variant_id, esto podría ser una mejora futura
        $this->info("ℹ️ Nota: Los invoice_items actualmente no guardan variant_id específico");
    }

    private function testReturnsIntegrity() {
        echo BOLD . "\n↩️  8. INTEGRIDAD DE DEVOLUCIONES\n" . RESET;
        echo str_repeat("─", 50) . "\n";

        // Verificar si la tabla existe (puede ser 'returns' o 'product_returns')
        $returnsTable = null;
        if ($this->tableExists('returns')) {
            $returnsTable = 'returns';
        } elseif ($this->tableExists('product_returns')) {
            $returnsTable = 'product_returns';
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
        if ($this->tableExists('return_items')) {
            $itemsCount = $this->query("SELECT COUNT(*) as c FROM return_items");
            $this->info("Items de devolución: {$itemsCount[0]['c']}");

            // Verificar si return_items tiene soporte para variantes
            if ($this->columnExists('return_items', 'product_variant_id')) {
                $this->test("return_items soporta variantes", true);

                // Verificar items de productos variable sin variant_id
                $missingVariant = $this->query("
                    SELECT COUNT(*) as c FROM return_items ri
                    JOIN products p ON p.id = ri.product_id
                    WHERE p.product_type = 'variable' AND ri.product_variant_id IS NULL
                ");
                $this->test("Items de productos variable tienen variant_id", $missingVariant[0]['c'] == 0,
                    $missingVariant[0]['c'] > 0 ? "{$missingVariant[0]['c']} sin variante" : '');
            } else {
                $this->warning("⚠️ return_items NO tiene product_variant_id");
                $this->warning("Las devoluciones de productos variable no restaurarán stock a la variante correcta");
            }
        }
    }

    private function testPurchaseOrdersVariants() {
        echo BOLD . "\n📋 9. ÓRDENES DE COMPRA CON VARIANTES\n" . RESET;
        echo str_repeat("─", 50) . "\n";

        if (!$this->tableExists('purchase_orders')) {
            $this->warning("Tabla purchase_orders no existe");
            return;
        }

        $total = $this->query("SELECT COUNT(*) as c FROM purchase_orders");
        $this->info("Total órdenes de compra: {$total[0]['c']}");

        if ($total[0]['c'] == 0) {
            $this->info("No hay órdenes de compra");
            return;
        }

        // Verificar si purchase_order_items tiene variant_id
        if (!$this->tableExists('purchase_order_items')) {
            $this->warning("Tabla purchase_order_items no existe");
            return;
        }

        if (!$this->columnExists('purchase_order_items', 'variant_id')) {
            $this->warning("purchase_order_items no tiene columna variant_id");
            return;
        }

        // Items de productos variable con variant_id
        $itemsWithVariant = $this->query("
            SELECT COUNT(*) as c FROM purchase_order_items poi
            JOIN products p ON p.id = poi.product_id
            WHERE p.product_type = 'variable' AND poi.variant_id IS NOT NULL
        ");

        $itemsWithoutVariant = $this->query("
            SELECT COUNT(*) as c FROM purchase_order_items poi
            JOIN products p ON p.id = poi.product_id
            WHERE p.product_type = 'variable' AND poi.variant_id IS NULL
        ");

        $this->info("Items de productos variable CON variant_id: {$itemsWithVariant[0]['c']}");

        if ($itemsWithoutVariant[0]['c'] > 0) {
            $this->warning("{$itemsWithoutVariant[0]['c']} items de productos variable SIN variant_id");
        } else {
            $this->test("Todos los items variable tienen variant_id", true);
        }
    }

    private function testCustomerIntegrity() {
        echo BOLD . "\n👤 10. INTEGRIDAD DE CLIENTES\n" . RESET;
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

    private function testCashSessionIntegrity() {
        echo BOLD . "\n💰 11. INTEGRIDAD DE SESIONES DE CAJA\n" . RESET;
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

    private function showSummary() {
        echo "\n" . BOLD . "═══════════════════════════════════════════════════════════════" . RESET . "\n";
        echo BOLD . "   📋 RESUMEN DE PRUEBAS - TIENDA FASHION" . RESET . "\n";
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

        // Notas específicas para Fashion
        echo "\n   " . MAGENTA . "📝 NOTAS ESPECÍFICAS PARA TIENDAS FASHION:" . RESET . "\n";
        echo "   • Las devoluciones actualmente NO manejan variantes específicamente\n";
        echo "   • Verificar manualmente que los SKUs de variantes sean únicos y descriptivos\n";
        echo "   • El stock del producto padre debe ser siempre = suma de sus variantes\n";

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
$tester = new FashionStoreSystemTester($tenantId);
$tester->run();
