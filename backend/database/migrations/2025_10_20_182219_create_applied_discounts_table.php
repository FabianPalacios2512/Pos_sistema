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
        Schema::create('applied_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('discount_id')->nullable()->constrained('discounts')->onDelete('set null');
            $table->string('discount_type'); // 'percentage', 'fixed_amount', 'manual'
            $table->decimal('discount_value', 10, 2); // Valor del descuento (% o monto fijo)
            $table->decimal('discount_amount', 12, 2); // Monto real descontado
            $table->string('discount_reason')->nullable(); // Razón del descuento
            $table->string('applied_by')->nullable(); // Usuario que aplicó el descuento
            $table->timestamp('applied_at'); // Fecha y hora de aplicación
            $table->text('notes')->nullable(); // Notas adicionales
            $table->timestamps();

            // Índices para optimizar consultas
            $table->index(['customer_id', 'applied_at']);
            $table->index('invoice_id');
            $table->index('discount_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applied_discounts');
    }
};
