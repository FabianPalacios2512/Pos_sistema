<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Crear tabla de órdenes de compra (Purchase Orders).
     * Permite registrar pedidos a proveedores y controlar inventario.
     */
    public function up(): void
    {
        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->id();

                // Información básica
                $table->string('order_number')->unique(); // PO-2025-001
                $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('restrict');
                $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null');

                // Estado de la orden
                $table->enum('status', [
                    'draft',      // Borrador (puede editarse)
                    'pending',    // Enviada al proveedor
                    'partial',    // Recibida parcialmente
                    'received',   // Recibida completamente
                    'cancelled'   // Cancelada
                ])->default('draft');

                // Fechas importantes
                $table->date('order_date'); // Fecha de creación
                $table->date('expected_date')->nullable(); // Fecha esperada de entrega
                $table->date('received_date')->nullable(); // Fecha real de recepción

                // Montos
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->decimal('tax', 12, 2)->default(0);
                $table->decimal('shipping_cost', 12, 2)->default(0);
                $table->decimal('discount', 12, 2)->default(0);
                $table->decimal('total', 12, 2)->default(0);

                // Pago
                $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
                $table->decimal('paid_amount', 12, 2)->default(0);

                // Información adicional
                $table->text('notes')->nullable(); // Notas internas
                $table->text('supplier_notes')->nullable(); // Notas del proveedor
                $table->string('reference')->nullable(); // Referencia externa

                // Metadata
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');

                $table->timestamps();
                $table->softDeletes(); // Para no perder historial

                // Índices
                $table->index(['status', 'order_date']);
                $table->index(['supplier_id', 'status']);
                $table->index(['warehouse_id', 'status']);
                $table->index('order_number');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
