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
        Schema::create('dismissed_alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Para multi-usuario futuro
            $table->string('alert_type'); // 'stock_out', 'stock_low', 'rotation', etc.
            $table->unsignedBigInteger('product_id')->nullable(); // Producto específico
            $table->string('alert_key'); // Clave única: tipo+producto_id
            $table->timestamp('dismissed_at');
            $table->timestamps();

            // Índices para consultas rápidas
            $table->index(['user_id', 'alert_key']);
            $table->index(['alert_type', 'product_id']);

            // Constraint para evitar duplicados por usuario
            $table->unique(['user_id', 'alert_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dismissed_alerts');
    }
};
