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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();

            // Información básica del descuento
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('code')->unique(); // Código promocional
            $table->enum('type', ['percentage', 'fixed_amount']); // Porcentaje o monto fijo
            $table->decimal('value', 10, 2); // Valor del descuento

            // Aplicación del descuento
            $table->enum('applies_to', ['all_products', 'specific_products', 'categories', 'customers']);
            $table->json('applicable_items')->nullable(); // IDs de productos, categorías o clientes

            // Restricciones
            $table->decimal('minimum_amount', 10, 2)->nullable(); // Monto mínimo de compra
            $table->decimal('maximum_discount', 10, 2)->nullable(); // Descuento máximo aplicable
            $table->integer('usage_limit')->nullable(); // Límite de usos
            $table->integer('used_count')->default(0); // Cuántas veces se ha usado

            // Fechas
            $table->datetime('starts_at')->nullable();
            $table->datetime('expires_at')->nullable();

            // Estado
            $table->boolean('active')->default(true);
            $table->boolean('stackable')->default(false); // Se puede combinar con otros descuentos

            $table->timestamps();

            // Índices
            $table->index(['code', 'active']);
            $table->index(['expires_at', 'active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
