<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            // Costos y rentabilidad por item
            $table->decimal('unit_cost', 10, 2)->default(0)->after('unit_price'); // Costo unitario en el momento de la venta
            $table->decimal('discount_allocated', 10, 2)->default(0)->after('unit_cost'); // Descuento asignado a este item
            $table->decimal('revenue', 12, 2)->default(0)->after('discount_allocated'); // Ingresos = (unit_price - discount_allocated) * quantity
            $table->decimal('profit', 12, 2)->default(0)->after('revenue'); // Ganancia = revenue - (unit_cost * quantity)
            $table->decimal('profit_margin', 5, 2)->default(0)->after('profit'); // Margen = (profit / revenue) * 100

            // Índices para análisis
            $table->index(['product_id', 'created_at']);
            $table->index(['profit_margin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'created_at']);
            $table->dropIndex(['profit_margin']);

            $table->dropColumn([
                'unit_cost',
                'discount_allocated',
                'revenue',
                'profit',
                'profit_margin'
            ]);
        });
    }
};
