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
if (!Schema::hasColumn('cash_sessions', 'closing_status')) {
    $table->enum('closing_status', ['exact', 'deficit', 'surplus', 'with_expenses'])->nullable()->after('closing_notes');
}
                        if (!Schema::hasColumn('cash_sessions', 'expected_amount')) {
                            $table->decimal('expected_amount', 10, 2)->nullable()->after('closing_status');
                        }
                        if (!Schema::hasColumn('cash_sessions', 'difference_amount')) {
                            $table->decimal('difference_amount', 10, 2)->nullable()->after('expected_amount');
                        }
                        if (!Schema::hasColumn('cash_sessions', 'total_expenses')) {
                            $table->decimal('total_expenses', 10, 2)->default(0)->after('difference_amount');
                        }
                        if (!Schema::hasColumn('cash_sessions', 'expenses_detail')) {
                            $table->text('expenses_detail')->nullable()->after('total_expenses');
                        }
                        if (!Schema::hasColumn('cash_sessions', 'closing_breakdown')) {
                            $table->json('closing_breakdown')->nullable()->after('expenses_detail'); // Para guardar desglose detallado
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
