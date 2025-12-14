<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Crear tabla pivot para relación Many-to-Many entre productos y proveedores.
     * Esto permite que:
     * - Un producto pueda tener múltiples proveedores
     * - Un proveedor pueda suministrar múltiples productos
     * - Se pueda registrar el precio de compra por proveedor
     */
    public function up(): void
    {
        if (!Schema::hasTable('product_supplier')) {
            Schema::create('product_supplier', function (Blueprint $table) {
                $table->id();

                // Relaciones
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');

                // Datos específicos de la relación
                $table->decimal('cost_price', 12, 2)->nullable(); // Precio de compra específico de este proveedor
                $table->string('supplier_sku')->nullable(); // SKU del proveedor para este producto
                $table->integer('lead_time_days')->default(0); // Días de entrega
                $table->integer('min_order_quantity')->default(1); // Cantidad mínima de pedido
                $table->boolean('is_preferred')->default(false); // Proveedor preferido para este producto
                $table->text('notes')->nullable(); // Notas adicionales

                // Metadata
                $table->timestamp('last_purchase_date')->nullable(); // Última compra a este proveedor
                $table->decimal('last_purchase_price', 12, 2)->nullable(); // Último precio de compra
                $table->boolean('active')->default(true); // Si la relación está activa

                $table->timestamps();

                // Índices
                $table->unique(['product_id', 'supplier_id']); // Un proveedor solo puede estar una vez por producto
                $table->index(['product_id', 'is_preferred']); // Para buscar proveedor preferido
                $table->index(['supplier_id', 'active']); // Para listar productos activos de un proveedor
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_supplier');
    }
};
