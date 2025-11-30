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
        if (Schema::hasTable('products') && Schema::hasTable('suppliers')) {
            Schema::table('products', function (Blueprint $table) {
                // Crear foreign key para supplier_id si no existe
                if (!Schema::hasColumn('products', 'main_supplier_id')) {
                    // Si supplier_id existe pero no tiene foreign key, crearla
                    if (Schema::hasColumn('products', 'supplier_id')) {
                        $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
                    }

                    // Relación con proveedor principal (después de crear la tabla suppliers)
                    $table->foreignId('main_supplier_id')->nullable()->constrained('suppliers')->after('last_purchase_date');
                }
            });
        }
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
