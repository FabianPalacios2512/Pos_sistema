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
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('has_discount')->default(false);
            $table->enum('discount_type', ['percentage', 'fixed_amount'])->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->text('discount_description')->nullable();
        });
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
