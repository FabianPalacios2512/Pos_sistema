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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('document_type')->default('CC'); // CC, NIT, CE, etc
            $table->string('document_number')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['M', 'F', 'Other'])->nullable();
            $table->decimal('credit_limit', 12, 2)->default(0);
            $table->decimal('current_debt', 12, 2)->default(0);
            $table->decimal('total_purchases', 12, 2)->default(0);
            $table->integer('total_orders')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamp('last_purchase')->nullable();
            $table->timestamps();

            $table->index(['active']);
            $table->index(['email']);
            $table->index(['document_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
