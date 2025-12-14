<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            if (!Schema::hasColumn('discounts', 'allow_multiple_uses_per_user')) {
                $table->boolean('allow_multiple_uses_per_user')->default(true)->after('stackable');
            }

            // Renombrar used_count a times_used si existe
            if (Schema::hasColumn('discounts', 'used_count') && !Schema::hasColumn('discounts', 'times_used')) {
                $table->renameColumn('used_count', 'times_used');
            }
        });
    }

    public function down(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            if (Schema::hasColumn('discounts', 'allow_multiple_uses_per_user')) {
                $table->dropColumn('allow_multiple_uses_per_user');
            }

            if (Schema::hasColumn('discounts', 'times_used')) {
                $table->renameColumn('times_used', 'used_count');
            }
        });
    }
};
