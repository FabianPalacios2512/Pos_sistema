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
        // Agregar foreign keys que faltaban para supplier_id después de que suppliers existe

        // Para inventory_movements
        if (Schema::hasTable('inventory_movements') &&
            Schema::hasTable('suppliers') &&
            Schema::hasColumn('inventory_movements', 'supplier_id')) {
            Schema::table('inventory_movements', function (Blueprint $table) {
                $table->foreign('supplier_id')
                    ->references('id')
                    ->on('suppliers')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('inventory_movements')) {
            Schema::table('inventory_movements', function (Blueprint $table) {
                $table->dropForeign(['supplier_id']);
            });
        }
    }
};
