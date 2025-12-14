<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Esta tabla está en la base de datos CENTRAL para trackear uso de IA
     */
    public function up(): void
    {
        Schema::connection('mysql')->create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('action_type'); // 'chat', 'analysis', 'recommendation', etc.
            $table->integer('tokens_used')->default(0);
            $table->decimal('estimated_cost', 10, 6)->default(0); // Costo estimado en USD
            $table->string('model_used')->default('gpt-4o-mini'); // gpt-4, gpt-3.5-turbo, etc.
            $table->text('prompt_summary')->nullable(); // Resumen del prompt
            $table->json('metadata')->nullable(); // Datos adicionales
            $table->timestamps();

            // Foreign key a la tabla central de tenants
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists('ai_usage_logs');
    }
};
