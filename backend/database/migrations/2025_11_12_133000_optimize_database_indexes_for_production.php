<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Asegurar que la columna cash_session_id exista en invoices antes de indexar
        if (Schema::hasTable('invoices') && !Schema::hasColumn('invoices', 'cash_session_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->foreignId('cash_session_id')->nullable()->after('customer_id');

                if (Schema::hasTable('cash_sessions')) {
                    $table->foreign('cash_session_id')->references('id')->on('cash_sessions')->nullOnDelete();
                }
            });
        }

        // Verificar si los índices ya existen antes de crearlos
        $indexes = DB::select("SHOW INDEX FROM invoices WHERE Key_name = 'idx_invoices_date_status'");

        if (empty($indexes)) {
            // Índices para la tabla invoices (consultas más frecuentes)
            Schema::table('invoices', function (Blueprint $table) {
                // Índice compuesto para fecha y estado (dashboard queries)
                $table->index(['date', 'status'], 'idx_invoices_date_status');

                // Índice para cash_session_id (reportes por cajero)
                $table->index('cash_session_id', 'idx_invoices_cash_session');

                // Índice para customer_id (historial de clientes)
                $table->index('customer_id', 'idx_invoices_customer');

                // Índice para total (consultas por rango de precio)
                $table->index('total', 'idx_invoices_total');
            });
        }

        // Verificar índices de invoice_items
        $itemIndexes = DB::select("SHOW INDEX FROM invoice_items WHERE Key_name = 'idx_items_invoice_product'");

        if (empty($itemIndexes)) {
            // Índices para la tabla invoice_items (productos más vendidos)
            Schema::table('invoice_items', function (Blueprint $table) {
                // Índice compuesto para invoice_id y product_id
                $table->index(['invoice_id', 'product_id'], 'idx_items_invoice_product');

                // Índice para product_id (reportes de productos)
                $table->index('product_id', 'idx_items_product');

                // Índice para quantity (ordenamiento por cantidad)
                $table->index('quantity', 'idx_items_quantity');
            });
        }

        // Verificar índices de cash_sessions
        $sessionIndexes = DB::select("SHOW INDEX FROM cash_sessions WHERE Key_name = 'idx_sessions_status'");

        if (empty($sessionIndexes)) {
            // Índices para la tabla cash_sessions
            Schema::table('cash_sessions', function (Blueprint $table) {
                // Índice para status (sesiones activas)
                $table->index('status', 'idx_sessions_status');

                // Índice para user_id (reportes por cajero)
                $table->index('user_id', 'idx_sessions_user');

                // Índice compuesto para fecha y estado
                $table->index(['opened_at', 'status'], 'idx_sessions_date_status');
            });
        }

        // Verificar índices de products
        $productIndexes = DB::select("SHOW INDEX FROM products WHERE Key_name = 'idx_products_active'");

        if (empty($productIndexes)) {
            // Índices para la tabla products (búsquedas frecuentes)
            Schema::table('products', function (Blueprint $table) {
                // Índice para active (productos activos)
                $table->index('active', 'idx_products_active');

                // Índice para category_id (filtros por categoría)
                $table->index('category_id', 'idx_products_category');

                // Índice para current_stock (alertas de inventario)
                $table->index('current_stock', 'idx_products_stock');
            });
        }

        // Verificar índices de customers
        $customerIndexes = DB::select("SHOW INDEX FROM customers WHERE Key_name = 'idx_customers_active'");

        if (empty($customerIndexes)) {
            // Índices para la tabla customers
            Schema::table('customers', function (Blueprint $table) {
                // Índice para active (clientes activos)
                $table->index('active', 'idx_customers_active');

                // Índice para document_number (búsquedas por documento)
                $table->index('document_number', 'idx_customers_document');
            });
        }

        // Verificar índices de returns
        $returnIndexes = DB::select("SHOW INDEX FROM returns WHERE Key_name = 'idx_returns_invoice'");

        if (empty($returnIndexes)) {
            // Índices para la tabla returns (devoluciones)
            Schema::table('returns', function (Blueprint $table) {
                // Índice para original_invoice_id (devoluciones por factura)
                $table->index('original_invoice_id', 'idx_returns_invoice');

                // Índice para status (estado de devoluciones)
                $table->index('status', 'idx_returns_status');

                // Índice para return_date (reportes por fecha)
                $table->index('return_date', 'idx_returns_date');
            });
        }

        echo "✅ Índices de optimización aplicados correctamente\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar índices de manera segura verificando primero si existen
        try {
            // Índices de invoices
            $indexes = DB::select("SHOW INDEX FROM invoices WHERE Key_name = 'idx_invoices_date_status'");
            if (!empty($indexes)) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->dropIndex('idx_invoices_date_status');
                    $table->dropIndex('idx_invoices_cash_session');
                    $table->dropIndex('idx_invoices_customer');
                    $table->dropIndex('idx_invoices_total');
                });
            }

            // Índices de invoice_items
            $itemIndexes = DB::select("SHOW INDEX FROM invoice_items WHERE Key_name = 'idx_items_invoice_product'");
            if (!empty($itemIndexes)) {
                Schema::table('invoice_items', function (Blueprint $table) {
                    $table->dropIndex('idx_items_invoice_product');
                    $table->dropIndex('idx_items_product');
                    $table->dropIndex('idx_items_quantity');
                });
            }

            // Índices de cash_sessions
            $sessionIndexes = DB::select("SHOW INDEX FROM cash_sessions WHERE Key_name = 'idx_sessions_status'");
            if (!empty($sessionIndexes)) {
                Schema::table('cash_sessions', function (Blueprint $table) {
                    $table->dropIndex('idx_sessions_status');
                    $table->dropIndex('idx_sessions_user');
                    $table->dropIndex('idx_sessions_date_status');
                });
            }

            // Índices de products
            $productIndexes = DB::select("SHOW INDEX FROM products WHERE Key_name = 'idx_products_active'");
            if (!empty($productIndexes)) {
                Schema::table('products', function (Blueprint $table) {
                    $table->dropIndex('idx_products_active');
                    $table->dropIndex('idx_products_category');
                    $table->dropIndex('idx_products_stock');
                });
            }

            // Índices de customers
            $customerIndexes = DB::select("SHOW INDEX FROM customers WHERE Key_name = 'idx_customers_active'");
            if (!empty($customerIndexes)) {
                Schema::table('customers', function (Blueprint $table) {
                    $table->dropIndex('idx_customers_active');
                    $table->dropIndex('idx_customers_document');
                });
            }

            // Índices de returns
            $returnIndexes = DB::select("SHOW INDEX FROM returns WHERE Key_name = 'idx_returns_invoice'");
            if (!empty($returnIndexes)) {
                Schema::table('returns', function (Blueprint $table) {
                    $table->dropIndex('idx_returns_invoice');
                    $table->dropIndex('idx_returns_status');
                    $table->dropIndex('idx_returns_date');
                });
            }

            echo "✅ Rollback de índices completado\n";

        } catch (Exception $e) {
            echo "⚠️ Error durante el rollback: " . $e->getMessage() . "\n";
        }
    }
};
