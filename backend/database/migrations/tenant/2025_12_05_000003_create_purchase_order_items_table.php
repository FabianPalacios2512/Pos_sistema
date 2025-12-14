<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Items/productos de cada orden de compra.
     */
    public function up(): void
    {
        if (!Schema::hasTable('purchase_order_items')) {
            Schema::create('purchase_order_items', function (Blueprint $table) {
                $table->id();

                // Relaciones
                $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products')->onDelete('restrict');

                // Cantidades
                $table->decimal('quantity_ordered', 10, 2); // Cantidad pedida
                $table->decimal('quantity_received', 10, 2)->default(0); // Cantidad recibida
                $table->string('unit')->default('unidad'); // Unidad de medida

                // Precios
                $table->decimal('unit_cost', 12, 2); // Costo unitario
                $table->decimal('subtotal', 12, 2); // quantity_ordered * unit_cost
                $table->decimal('tax_amount', 12, 2)->default(0);
                $table->decimal('total', 12, 2); // subtotal + tax

                // Metadata
                $table->text('notes')->nullable();
                $table->boolean('received')->default(false); // Si ya fue recibido completamente

                $table->timestamps();

                // Índices
                $table->index(['purchase_order_id', 'product_id']);
                $table->index('product_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
