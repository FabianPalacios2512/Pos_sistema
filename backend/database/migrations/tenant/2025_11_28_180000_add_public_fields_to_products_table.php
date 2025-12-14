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
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
if (!Schema::hasColumn('products', 'is_public')) {
    $table->boolean('is_public')->default(true)->after('active');
}
                        if (!Schema::hasColumn('products', 'public_description')) {
                            $table->text('public_description')->nullable()->after('is_public');
                        }
                        if (!Schema::hasColumn('products', 'public_image')) {
                            $table->string('public_image')->nullable()->after('public_description');
                        }
                        $table->index(['is_public', 'active']);
            
                    });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_public', 'active']);
            $table->dropColumn(['is_public', 'public_description', 'public_image']);
        });
    }
};
