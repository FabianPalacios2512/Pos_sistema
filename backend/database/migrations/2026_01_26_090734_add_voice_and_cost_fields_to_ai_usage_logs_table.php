<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ai_usage_logs', function (Blueprint $table) {
            // Tipo de request: chat, voice, chat_with_file
            if (!Schema::hasColumn('ai_usage_logs', 'request_type')) {
                $table->enum('request_type', ['chat', 'voice', 'chat_with_file'])->default('chat')->after('endpoint');
            }
            
            // Duración de llamada de voz en segundos
            if (!Schema::hasColumn('ai_usage_logs', 'voice_duration_seconds')) {
                $table->integer('voice_duration_seconds')->nullable()->after('request_type');
            }
            
            // Costo estimado en USD (para calcular gastos)
            if (!Schema::hasColumn('ai_usage_logs', 'cost_usd')) {
                $table->decimal('cost_usd', 10, 8)->default(0)->after('voice_duration_seconds');
            }
            
            // Provider usado (gemini, groq, etc)
            if (!Schema::hasColumn('ai_usage_logs', 'provider')) {
                $table->string('provider', 50)->default('gemini')->after('model');
            }
        });
        
        // Modificar el enum de status para incluir más estados
        DB::statement("ALTER TABLE ai_usage_logs MODIFY COLUMN status ENUM('success', 'error', 'rate_limited', 'timeout', 'cancelled') NOT NULL DEFAULT 'success'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_usage_logs', function (Blueprint $table) {
            $table->dropColumn(['request_type', 'voice_duration_seconds', 'cost_usd', 'provider']);
        });
    }
};
