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
        Schema::table('invoice_items', function (Blueprint $table) {
            // Agregar columna para saber de qué warehouse se descontó este producto
            $table->foreignId('source_warehouse_id')->nullable()->after('product_id')->constrained('warehouses')->onDelete('set null');
            $table->index('source_warehouse_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeign(['source_warehouse_id']);
            $table->dropColumn('source_warehouse_id');
        });
    }
};
