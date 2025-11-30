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
        if (Schema::hasTable('cash_sessions')) {
            Schema::table('cash_sessions', function (Blueprint $table) {
// Agregar columna actual_amount después de expected_amount
                        if (!Schema::hasColumn('cash_sessions', 'actual_amount')) {
                            $table->decimal('actual_amount', 10, 2)->nullable()->after('expected_amount');
                        }
            
                    });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_sessions', function (Blueprint $table) {
            $table->dropColumn('actual_amount');
        });
    }
};
