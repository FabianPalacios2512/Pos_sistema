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
        if (Schema::hasTable('inventory_movements')) {
            return;
        }
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Usuario responsable
            $table->foreignId('sale_id')->nullable()->constrained()->onDelete('set null'); // Si es por venta
            // No crear foreign key para supplier aquí, se puede crear después si suppliers existe
            $table->unsignedBigInteger('supplier_id')->nullable(); // Si es compra

            // Datos del movimiento
            $table->enum('type', ['in', 'out']); // Entrada o salida
            $table->enum('reason', [
                'sale', 'purchase', 'adjustment_positive', 'adjustment_negative',
                'loss', 'expired', 'returned', 'damaged', 'transfer', 'initial_stock'
            ]);

            $table->integer('quantity'); // Cantidad (positiva o negativa)
            $table->integer('previous_stock'); // Stock anterior
            $table->integer('new_stock'); // Stock después del movimiento

            // Datos financieros
            $table->decimal('unit_cost', 10, 2)->default(0); // Costo unitario
            $table->decimal('total_cost', 12, 2)->default(0); // Costo total del movimiento
            $table->decimal('unit_price', 10, 2)->default(0); // Precio de venta (si aplica)
            $table->decimal('total_value', 12, 2)->default(0); // Valor total del movimiento

            // Detalles
            $table->text('notes')->nullable(); // Observaciones
            $table->string('reference')->nullable(); // Referencia externa (factura, etc.)
            $table->datetime('movement_date'); // Fecha del movimiento

            $table->timestamps();

            // Índices para optimizar consultas
            $table->index(['product_id', 'movement_date']);
            $table->index(['type', 'reason']);
            $table->index(['user_id', 'movement_date']);
            $table->index(['sale_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
