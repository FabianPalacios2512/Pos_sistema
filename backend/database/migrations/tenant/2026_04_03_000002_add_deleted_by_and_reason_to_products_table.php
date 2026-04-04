<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'deleted_by')) {
                    $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');
                }
                if (!Schema::hasColumn('products', 'deleted_reason')) {
                    $table->text('deleted_reason')->nullable()->after('deleted_by');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'deleted_by')) {
                    $table->dropColumn('deleted_by');
                }
                if (Schema::hasColumn('products', 'deleted_reason')) {
                    $table->dropColumn('deleted_reason');
                }
            });
        }
    }
};
