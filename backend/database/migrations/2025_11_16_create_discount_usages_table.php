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
        Schema::create('discount_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_id')->constrained()->onDelete('cascade');
            $table->string('user_identifier')->nullable(); // IP, user_id, session_id, etc.
            $table->string('customer_email')->nullable(); // Email del cliente si está disponible
            $table->string('customer_phone')->nullable(); // Teléfono del cliente si está disponible
            $table->string('invoice_number')->nullable(); // Número de factura donde se usó
            $table->decimal('discount_amount', 10, 2); // Monto del descuento aplicado
            $table->json('usage_metadata')->nullable(); // Información adicional (IP, user agent, etc.)
            $table->timestamp('used_at');
            $table->timestamps();

            // Índices para consultas rápidas
            $table->index(['discount_id', 'user_identifier']);
            $table->index(['discount_id', 'customer_email']);
            $table->index(['discount_id', 'customer_phone']);
            $table->index('used_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_usages');
    }
};
