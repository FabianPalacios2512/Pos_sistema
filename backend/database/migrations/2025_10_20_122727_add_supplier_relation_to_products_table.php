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
            // Relación con proveedor principal (después de crear la tabla suppliers)
            $table->foreignId('main_supplier_id')->nullable()->constrained('suppliers')->after('last_purchase_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['main_supplier_id']);
            $table->dropColumn('main_supplier_id');
        });
    }
};
