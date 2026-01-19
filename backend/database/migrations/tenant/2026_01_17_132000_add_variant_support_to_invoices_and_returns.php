<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 👗 MIGRACIÓN: Soporte completo de variantes en ventas y devoluciones
     *
     * Esta migración agrega soporte de variantes a:
     * 1. invoice_items - Para saber qué variante se vendió
     * 2. return_items - Para saber qué variante se devuelve
     *
     * BENEFICIOS:
     * - Trazabilidad completa de ventas por variante
     * - Devoluciones restauran stock a la variante correcta
     * - Reportes precisos por talla/color
     */
    public function up(): void
    {
        // 1. Agregar product_variant_id a invoice_items
        if (Schema::hasTable('invoice_items') && !Schema::hasColumn('invoice_items', 'product_variant_id')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->unsignedBigInteger('product_variant_id')
                    ->nullable()
                    ->after('product_id');

                // Agregar columna para guardar las opciones de la variante (ej: "Talla: M, Color: Rojo")
                $table->string('variant_options', 500)
                    ->nullable()
                    ->after('product_variant_id');

                // Índice para búsquedas
                $table->index('product_variant_id', 'ii_variant_idx');
            });
        }

        // 2. Agregar product_variant_id a return_items
        if (Schema::hasTable('return_items') && !Schema::hasColumn('return_items', 'product_variant_id')) {
            Schema::table('return_items', function (Blueprint $table) {
                $table->unsignedBigInteger('product_variant_id')
                    ->nullable()
                    ->after('product_id');

                // Guardar las opciones de la variante también
                $table->string('variant_options', 500)
                    ->nullable()
                    ->after('product_variant_id');

                // Índice para búsquedas
                $table->index('product_variant_id', 'ri_variant_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('invoice_items') && Schema::hasColumn('invoice_items', 'product_variant_id')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->dropIndex('ii_variant_idx');
                $table->dropColumn(['product_variant_id', 'variant_options']);
            });
        }

        if (Schema::hasTable('return_items') && Schema::hasColumn('return_items', 'product_variant_id')) {
            Schema::table('return_items', function (Blueprint $table) {
                $table->dropIndex('ri_variant_idx');
                $table->dropColumn(['product_variant_id', 'variant_options']);
            });
        }
    }
};
