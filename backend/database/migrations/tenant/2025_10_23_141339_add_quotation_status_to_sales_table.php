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
        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'status')) {
            Schema::table('sales', function (Blueprint $table) {
                // Modificar el enum para incluir 'quotation'
                $table->enum('status', ['completed', 'pending', 'cancelled', 'refunded', 'quotation'])->default('completed')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Revertir al enum original
            $table->enum('status', ['completed', 'pending', 'cancelled', 'refunded'])->default('completed')->change();
        });
    }
};
