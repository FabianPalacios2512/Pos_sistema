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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // Número de factura
            $table->enum('type', ['invoice', 'quote', 'credit_note'])->default('invoice'); // Tipo de documento
            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // Cliente
            $table->date('date'); // Fecha de emisión
            $table->date('due_date')->nullable(); // Fecha de vencimiento
            $table->decimal('subtotal', 12, 2)->default(0); // Subtotal
            $table->decimal('tax_amount', 12, 2)->default(0); // Impuestos
            $table->decimal('total', 12, 2); // Total
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft'); // Estado
            $table->text('notes')->nullable(); // Notas adicionales
            $table->json('items')->nullable(); // Items de la factura en JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
