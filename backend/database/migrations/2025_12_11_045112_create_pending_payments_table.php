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
        Schema::create('pending_payments', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // Referencia única del pago
            $table->string('tenant_id'); // ID del tenant
            $table->string('plan'); // basic, premium, enterprise
            $table->enum('payment_frequency', ['monthly', 'yearly', '24months']); // Frecuencia de pago
            $table->integer('amount_in_cents'); // Monto en centavos
            $table->string('customer_email'); // Email del cliente
            $table->string('payment_link_id')->nullable(); // ID del payment link de Wompi
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->timestamps();

            // Índices para búsquedas rápidas
            $table->index('reference');
            $table->index('tenant_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_payments');
    }
};
