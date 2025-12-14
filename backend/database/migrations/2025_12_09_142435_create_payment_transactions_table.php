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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('preference_id')->nullable();
            $table->string('payment_id')->nullable()->index();
            $table->string('plan'); // emprendedor, negocio_pro, enterprise, trial_express
            $table->string('frequency'); // monthly, yearly, 24months
            $table->decimal('amount', 12, 2);
            $table->boolean('include_dian')->default(false);
            $table->string('status')->default('pending'); // pending, approved, rejected, cancelled
            $table->string('status_detail')->nullable();
            $table->json('metadata')->nullable();
            $table->json('payment_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
