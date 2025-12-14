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
        if (Schema::hasTable('system_settings')) {
            Schema::table('system_settings', function (Blueprint $table) {
// Sistema de puntos de fidelización
                        if (!Schema::hasColumn('system_settings', 'enable_loyalty_system')) {
                            $table->boolean('enable_loyalty_system')->default(false)->after('discounts_enabled');
                        }
                        // Por cada $1000 pesos gastados = 1 punto (0.001)
                        if (!Schema::hasColumn('system_settings', 'loyalty_points_per_currency')) {
                            $table->decimal('loyalty_points_per_currency', 10, 6)->default(0.001)->after('enable_loyalty_system');
                        }
                        // Cada punto vale $10 pesos al redimirlo
                        if (!Schema::hasColumn('system_settings', 'loyalty_point_value')) {
                            $table->decimal('loyalty_point_value', 10, 2)->default(10.00)->after('loyalty_points_per_currency');
                        }
            
                    });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'enable_loyalty_system',
                'loyalty_points_per_currency',
                'loyalty_point_value'
            ]);
        });
    }
};
