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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('product_name'); // Nombre del producto al momento de la venta
            $table->string('product_sku')->nullable(); // SKU del producto
            $table->decimal('quantity', 10, 2); // Cantidad vendida
            $table->decimal('unit_price', 12, 2); // Precio unitario al momento de la venta
            $table->decimal('cost_price', 12, 2)->nullable(); // Precio de costo para calcular ganancia
            $table->decimal('subtotal', 12, 2); // quantity * unit_price
            $table->decimal('discount_amount', 10, 2)->default(0); // Descuento aplicado a este item
            $table->decimal('tax_amount', 10, 2)->default(0); // Impuesto aplicado
            $table->text('notes')->nullable(); // Notas adicionales del item
            $table->timestamps();

            // Índices para optimizar consultas
            $table->index(['invoice_id', 'product_id']);
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
