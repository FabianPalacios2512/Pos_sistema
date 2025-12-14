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
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // Información de la API Key usada
            $table->integer('api_key_index')->comment('Índice de la key usada (1-10)');
            $table->string('api_key_last_4', 4)->comment('Últimos 4 caracteres de la key');

            // Detalles de la petición
            $table->text('user_message')->nullable();
            $table->integer('prompt_tokens')->default(0);
            $table->integer('completion_tokens')->default(0);
            $table->integer('total_tokens')->default(0);

            // Resultado
            $table->enum('status', ['success', 'error', 'rate_limited'])->default('success');
            $table->text('error_message')->nullable();

            // Timing
            $table->integer('response_time_ms')->nullable()->comment('Tiempo de respuesta en ms');

            // Contexto adicional
            $table->string('model', 100)->default('llama-3.3-70b-versatile');
            $table->string('endpoint', 100)->default('chat');
            $table->ipAddress('ip_address')->nullable();

            $table->timestamps();

            // Índices para búsqueda rápida
            $table->index('api_key_index');
            $table->index('status');
            $table->index('created_at');
            $table->index(['api_key_index', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
    }
};
