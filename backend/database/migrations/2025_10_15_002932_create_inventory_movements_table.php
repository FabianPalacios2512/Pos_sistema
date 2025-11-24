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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['purchase', 'sale', 'adjustment', 'return', 'transfer']);
            $table->integer('quantity'); // Positivo para entrada, negativo para salida
            $table->integer('previous_stock');
            $table->integer('new_stock');
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->string('reference')->nullable(); // Factura, orden, etc
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Quien hizo el movimiento
            $table->timestamp('movement_date')->useCurrent();
            $table->timestamps();

            $table->index(['product_id']);
            $table->index(['type']);
            $table->index(['movement_date']);
            $table->index(['user_id']);
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
