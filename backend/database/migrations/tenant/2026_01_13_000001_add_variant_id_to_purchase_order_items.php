<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Agregar soporte para variantes de producto en órdenes de compra.
     * Permite que tiendas de moda puedan ordenar tallas/colores específicos.
     */
    public function up(): void
    {
        if (Schema::hasTable('purchase_order_items')) {
            Schema::table('purchase_order_items', function (Blueprint $table) {
                // Solo agregar si no existe
                if (!Schema::hasColumn('purchase_order_items', 'variant_id')) {
                    $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');

                    // Agregar foreign key si la tabla de variantes existe
                    if (Schema::hasTable('product_variants')) {
                        $table->foreign('variant_id')
                              ->references('id')
                              ->on('product_variants')
                              ->onDelete('set null');
                    }

                    // Índice para mejorar rendimiento
                    $table->index(['product_id', 'variant_id']);
                }

                // Columna para guardar resumen de opciones de la variante (talla, color, etc.)
                if (!Schema::hasColumn('purchase_order_items', 'variant_options')) {
                    $table->json('variant_options')->nullable()->after('variant_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('purchase_order_items')) {
            Schema::table('purchase_order_items', function (Blueprint $table) {
                // Eliminar foreign key si existe
                try {
                    $table->dropForeign(['variant_id']);
                } catch (\Exception $e) {
                    // Ignorar si no existe
                }

                // Eliminar índice
                try {
                    $table->dropIndex(['product_id', 'variant_id']);
                } catch (\Exception $e) {
                    // Ignorar si no existe
                }

                // Eliminar columnas
                if (Schema::hasColumn('purchase_order_items', 'variant_id')) {
                    $table->dropColumn('variant_id');
                }
                if (Schema::hasColumn('purchase_order_items', 'variant_options')) {
                    $table->dropColumn('variant_options');
                }
            });
        }
    }
};
