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
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // Número de devolución RET-000001
            $table->foreignId('original_invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('cash_session_id')->constrained('cash_sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Cajero que procesó la devolución
            $table->date('return_date');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->enum('refund_method', ['cash', 'card', 'transfer', 'store_credit'])->default('cash');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->text('reason')->nullable(); // Motivo de la devolución
            $table->text('notes')->nullable(); // Notas adicionales
            $table->json('items'); // JSON con los productos devueltos
            $table->timestamps();

            // Índices para consultas rápidas
            $table->index(['return_date']);
            $table->index(['status']);
            $table->index(['cash_session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
