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
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
if (!Schema::hasColumn('customers', 'has_discount')) {
    $table->boolean('has_discount')->default(false);
}
                        if (!Schema::hasColumn('customers', 'discount_type')) {
                            $table->enum('discount_type', ['percentage', 'fixed_amount'])->nullable();
                        }
                        if (!Schema::hasColumn('customers', 'discount_value')) {
                            $table->decimal('discount_value', 10, 2)->nullable();
                        }
                        if (!Schema::hasColumn('customers', 'discount_description')) {
                            $table->text('discount_description')->nullable();
                        }
            
                    });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['has_discount', 'discount_type', 'discount_value', 'discount_description']);
        });
    }
};
