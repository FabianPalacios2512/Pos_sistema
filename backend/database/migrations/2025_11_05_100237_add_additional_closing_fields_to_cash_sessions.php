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
            // Solo agregar campos que realmente faltan
            $table->text('expenses_detail')->nullable()->after('total_expenses');
            $table->json('closing_breakdown')->nullable()->after('expenses_detail'); // Para guardar desglose detallado
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'expenses_detail',
                'closing_breakdown'
            ]);
        });
    }
};
