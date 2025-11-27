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
        Schema::table('products', function (Blueprint $table) {
            // Campos de costos e inventario
            if (!Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 10, 2)->default(0)->after('sale_price'); // Precio de compra
            }
            if (!Schema::hasColumn('products', 'avg_cost')) {
                $table->decimal('avg_cost', 10, 2)->default(0)->after('cost_price'); // Costo promedio móvil
            }

            // Control de stock
            if (!Schema::hasColumn('products', 'min_stock')) {
                $table->integer('min_stock')->default(5)->after('current_stock'); // Stock mínimo
            }
            if (!Schema::hasColumn('products', 'ideal_stock')) {
                $table->integer('ideal_stock')->default(50)->after('min_stock'); // Stock ideal
            }

            // Métricas de ventas
            $table->integer('total_sold')->default(0)->after('ideal_stock'); // Total vendido histórico
            $table->decimal('total_revenue', 12, 2)->default(0)->after('total_sold'); // Ingresos totales
            $table->decimal('total_profit', 12, 2)->default(0)->after('total_revenue'); // Ganancia total

            // Fechas importantes
            $table->datetime('last_sale_date')->nullable()->after('total_profit');
            $table->datetime('last_purchase_date')->nullable()->after('last_sale_date');

            // Métricas calculadas (sin relación aún)
            $table->decimal('avg_discount_applied', 5, 2)->default(0)->after('last_purchase_date'); // Descuento promedio
            $table->integer('days_to_sell_avg')->default(0)->after('avg_discount_applied'); // Días promedio para vender

            // Índices para optimizar consultas
            $table->index(['active', 'current_stock']);
            $table->index(['min_stock', 'current_stock']);
            $table->index(['total_sold', 'last_sale_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['active', 'current_stock']);
            $table->dropIndex(['min_stock', 'current_stock']);
            $table->dropIndex(['total_sold', 'last_sale_date']);

            $table->dropColumn([
                'cost_price',
                'avg_cost',
                'min_stock',
                'ideal_stock',
                'total_sold',
                'total_revenue',
                'total_profit',
                'last_sale_date',
                'last_purchase_date',
                'avg_discount_applied',
                'days_to_sell_avg'
            ]);
        });
    }
};
