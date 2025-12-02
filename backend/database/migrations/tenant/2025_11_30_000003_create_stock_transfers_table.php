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
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('destination_warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Quien hace el traslado
            $table->string('reference_number')->unique(); // TRF-00001
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'in_transit', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('transferred_at')->nullable(); // Fecha de envío
            $table->timestamp('received_at')->nullable(); // Fecha de recepción
            $table->timestamps();

            $table->index(['status']);
            $table->index(['source_warehouse_id']);
            $table->index(['destination_warehouse_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
