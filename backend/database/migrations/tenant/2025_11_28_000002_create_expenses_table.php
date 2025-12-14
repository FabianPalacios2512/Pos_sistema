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
        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->comment('Usuario que registró el gasto');

            $table->foreignId('category_id')
                ->constrained('expense_categories')
                ->onDelete('restrict')
                ->comment('Categoría del gasto');

            $table->unsignedBigInteger('cash_session_id')
                ->nullable()
                ->comment('Sesión de caja si el pago fue en efectivo');

            // Índice para mejorar búsquedas
            $table->index('cash_session_id');

            // Datos del gasto
            $table->decimal('amount', 10, 2)->comment('Monto del gasto');
            $table->text('description')->comment('Descripción detallada del gasto');
            $table->enum('payment_method', ['efectivo', 'transferencia', 'tarjeta'])
                ->default('efectivo')
                ->comment('Método de pago utilizado');

            $table->timestamp('date')->useCurrent()->comment('Fecha del gasto');

            // Información adicional
            $table->string('receipt_number', 50)->nullable()->comment('Número de factura o recibo');
            $table->string('supplier', 100)->nullable()->comment('Proveedor o beneficiario');
            $table->text('notes')->nullable()->comment('Notas adicionales');

            $table->timestamps();

            // Índices adicionales para mejorar rendimiento
            $table->index(['user_id', 'date']);
            $table->index(['category_id']);
            $table->index(['payment_method']);
            $table->index(['date']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
