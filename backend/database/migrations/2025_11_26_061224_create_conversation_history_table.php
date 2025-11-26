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
        Schema::create('conversation_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('session_id', 100)->index(); // UUID o hash de sesión
            $table->enum('role', ['system', 'user', 'assistant']); // Rol del mensaje
            $table->text('content'); // Contenido del mensaje
            $table->timestamp('created_at')->useCurrent();

            // Índices para búsquedas eficientes
            $table->index(['session_id', 'created_at']);
            $table->index('user_id');

            // Foreign key (opcional)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_history');
    }
};
