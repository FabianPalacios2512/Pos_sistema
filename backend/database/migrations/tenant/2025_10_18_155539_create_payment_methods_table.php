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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();

            $table->string('name'); // Nombre del método (Efectivo, Tarjeta, etc.)
            $table->string('code')->unique(); // Código único (cash, card, transfer, etc.)
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Icono del método de pago
            $table->boolean('active')->default(true);
            $table->boolean('requires_reference')->default(false); // Si requiere número de referencia
            $table->integer('sort_order')->default(0); // Orden de visualización

            // Configuración específica del método
            $table->json('config')->nullable(); // Configuraciones adicionales

            $table->timestamps();

            $table->index(['active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
