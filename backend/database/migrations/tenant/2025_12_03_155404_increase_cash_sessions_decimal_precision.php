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
        Schema::table('cash_sessions', function (Blueprint $table) {
            // Aumentar precisión de decimal(10,2) a decimal(15,2)
            // Esto permite valores hasta 9,999,999,999,999.99
            $table->decimal('opening_amount', 15, 2)->change();
            $table->decimal('closing_amount', 15, 2)->nullable()->change();
            $table->decimal('expected_amount', 15, 2)->nullable()->change();
            $table->decimal('actual_amount', 15, 2)->nullable()->change();
            $table->decimal('difference_amount', 15, 2)->nullable()->change();
            $table->decimal('total_expenses', 15, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_sessions', function (Blueprint $table) {
            // Revertir a decimal(10,2)
            $table->decimal('opening_amount', 10, 2)->change();
            $table->decimal('closing_amount', 10, 2)->nullable()->change();
            $table->decimal('expected_amount', 10, 2)->nullable()->change();
            $table->decimal('actual_amount', 10, 2)->nullable()->change();
            $table->decimal('difference_amount', 10, 2)->nullable()->change();
            $table->decimal('total_expenses', 10, 2)->default(0)->change();
        });
    }
};
