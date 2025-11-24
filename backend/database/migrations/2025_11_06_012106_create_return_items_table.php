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
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained('returns')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('original_invoice_item_id')->nullable()->constrained('invoice_items')->onDelete('set null');
            $table->integer('quantity'); // Cantidad devuelta
            $table->integer('original_quantity'); // Cantidad original en la factura
            $table->decimal('unit_price', 10, 2); // Precio unitario original
            $table->decimal('discount_amount', 10, 2)->default(0); // Descuento aplicado en el item original
            $table->decimal('subtotal', 10, 2); // Subtotal del item devuelto
            $table->text('reason')->nullable(); // Razón específica del producto devuelto
            $table->timestamps();

            // Índices
            $table->index(['return_id']);
            $table->index(['product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_items');
    }
};
