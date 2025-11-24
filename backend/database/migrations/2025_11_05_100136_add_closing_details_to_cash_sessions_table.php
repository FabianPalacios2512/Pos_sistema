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
            $table->enum('closing_status', ['exact', 'deficit', 'surplus', 'with_expenses'])->nullable()->after('closing_notes');
            $table->decimal('expected_amount', 10, 2)->nullable()->after('closing_status');
            $table->decimal('difference_amount', 10, 2)->nullable()->after('expected_amount');
            $table->decimal('total_expenses', 10, 2)->default(0)->after('difference_amount');
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
                'closing_status',
                'expected_amount',
                'difference_amount',
                'total_expenses',
                'expenses_detail',
                'closing_breakdown'
            ]);
        });
    }
};
