<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_error_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 50)->index();
            $table->string('error_hash', 64)->index(); // SHA-256 del mensaje+contexto para deduplicar
            $table->string('severity', 20)->default('error'); // error, warning, critical
            $table->string('type', 80)->nullable(); // exception_class o tipo de error
            $table->text('message'); // Mensaje original del error
            $table->text('ai_summary')->nullable(); // Resumen generado por Groq
            $table->string('file', 255)->nullable();
            $table->integer('line')->nullable();
            $table->json('context')->nullable(); // request_url, user_agent, user_id, etc
            $table->unsignedInteger('occurrence_count')->default(1);
            $table->timestamp('first_seen_at')->useCurrent();
            $table->timestamp('last_seen_at')->useCurrent()->index();
            $table->boolean('resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'error_hash']); // Un registro por error único por tenant
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_error_logs');
    }
};
