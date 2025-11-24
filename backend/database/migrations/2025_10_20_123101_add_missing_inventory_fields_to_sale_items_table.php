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
            $table->decimal('unit_cost', 10, 2)->nullable()->after('unit_price')->comment('Costo unitario del producto al momento de la venta');
            $table->decimal('profit_margin', 5, 2)->nullable()->after('unit_cost')->comment('Margen de ganancia en porcentaje');
            $table->decimal('profit_amount', 10, 2)->nullable()->after('profit_margin')->comment('Monto de ganancia total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['unit_cost', 'profit_margin', 'profit_amount']);
        });
    }
};
