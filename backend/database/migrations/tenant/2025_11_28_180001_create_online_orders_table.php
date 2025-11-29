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
        Schema::create('online_orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('order_number', 20)->unique(); // Ej: PED-402
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_document', 20); // Cédula/Documento
            $table->text('customer_address')->nullable();
            $table->enum('delivery_type', ['pickup', 'delivery'])->default('pickup');
            $table->enum('status', [
                'pending',
                'confirmed',
                'preparing',
                'ready',
                'completed',
                'cancelled'
            ])->default('pending');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->text('note')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            // Índices para búsquedas optimizadas
            $table->index(['uuid']);
            $table->index(['order_number']);
            $table->index(['status']);
            $table->index(['customer_phone']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_orders');
    }
};
