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
        Schema::create('active_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('alert_key')->unique(); // stock_low_1, stock_out_2, etc.
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('alert_type'); // 'stock_low', 'stock_out', etc.
            $table->string('severity'); // 'critical', 'warning', 'info'
            $table->text('message');
            $table->json('metadata')->nullable(); // stock actual, mínimo, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['product_id', 'alert_type']);
            $table->index('is_active');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_alerts');
    }
};
