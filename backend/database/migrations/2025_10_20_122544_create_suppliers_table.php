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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            // Información básica
            $table->string('name'); // Nombre del proveedor
            $table->string('document')->unique(); // NIT/RUT/RFC
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('contact_person')->nullable();

            // Información comercial
            $table->enum('payment_terms', ['immediate', '15_days', '30_days', '45_days', '60_days', '90_days'])->default('30_days');
            $table->decimal('credit_limit', 12, 2)->default(0);
            $table->decimal('current_debt', 12, 2)->default(0);

            // Métricas de rendimiento
            $table->integer('total_orders')->default(0); // Total de pedidos
            $table->decimal('total_purchased', 12, 2)->default(0); // Total comprado
            $table->decimal('avg_delivery_days', 5, 2)->default(0); // Días promedio de entrega
            $table->decimal('fulfillment_rate', 5, 2)->default(100); // % de cumplimiento

            // Fechas importantes
            $table->datetime('last_order_date')->nullable();
            $table->datetime('last_delivery_date')->nullable();

            // Estado
            $table->boolean('active')->default(true);
            $table->text('notes')->nullable();

            $table->timestamps();

            // Índices
            $table->index(['active', 'name']);
            $table->index(['document']);
            $table->index(['last_order_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
