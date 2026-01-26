<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    protected $fillable = [
        'user_id',
        'api_key_index',
        'api_key_last_4',
        'user_message',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'status',
        'error_message',
        'response_time_ms',
        'model',
        'provider',
        'endpoint',
        'request_type',
        'voice_duration_seconds',
        'cost_usd',
        'ip_address',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'cost_usd' => 'decimal:8',
    ];

    /**
     * Precios de Gemini (USD por 1M tokens)
     * Actualizado: Enero 2026 - Fuente: https://ai.google.dev/pricing
     * 
     * Gemini Live API: 25 tokens por segundo de audio (entrada/salida)
     */
    const PRICING = [
        'gemini-2.0-flash' => [
            'input' => 0.10,   // $0.10 / 1M tokens
            'output' => 0.40, // $0.40 / 1M tokens
        ],
        'gemini-2.5-flash' => [
            'input_text' => 0.30,  // $0.30 / 1M tokens (texto)
            'input_audio' => 1.00, // $1.00 / 1M tokens (audio)
            'output' => 2.50,      // $2.50 / 1M tokens
        ],
        'gemini-2.5-flash-native-audio' => [
            // Live API - Precios especiales
            'input_text' => 0.50,   // $0.50 / 1M tokens (texto)
            'input_audio' => 3.00,  // $3.00 / 1M tokens (audio)
            'output_text' => 2.00,  // $2.00 / 1M tokens (texto)
            'output_audio' => 12.00, // $12.00 / 1M tokens (audio)
        ],
        'groq-llama' => [
            'input' => 0.05,
            'output' => 0.10,
        ],
    ];
    
    // 25 tokens por segundo de audio (documentación oficial de Gemini)
    const TOKENS_PER_SECOND_AUDIO = 25;

    /**
     * Calcular costo de tokens de texto
     */
    public static function calculateTextCost(int $inputTokens, int $outputTokens, string $provider = 'gemini'): float
    {
        $pricing = $provider === 'groq' ? self::PRICING['groq-llama'] : self::PRICING['gemini-2.0-flash'];
        
        $inputCost = ($inputTokens / 1000000) * $pricing['input'];
        $outputCost = ($outputTokens / 1000000) * $pricing['output'];
        
        return $inputCost + $outputCost;
    }

    /**
     * Calcular costo de llamada de voz (Gemini Live API)
     * 
     * Fórmula basada en documentación oficial:
     * - 25 tokens por segundo de audio
     * - Input audio: $3.00 / 1M tokens
     * - Output audio: $12.00 / 1M tokens
     * 
     * @param int $durationSeconds Duración de la llamada en segundos
     * @return float Costo en USD
     */
    public static function calculateVoiceCost(int $durationSeconds): float
    {
        $tokensPerSecond = self::TOKENS_PER_SECOND_AUDIO;
        $pricing = self::PRICING['gemini-2.5-flash-native-audio'];
        
        // Tokens de audio (entrada y salida, asumimos 50/50)
        $totalTokens = $durationSeconds * $tokensPerSecond;
        
        // Costo de entrada (audio que el usuario envía)
        $inputCost = ($totalTokens / 1000000) * $pricing['input_audio'];
        
        // Costo de salida (audio que la IA responde)
        $outputCost = ($totalTokens / 1000000) * $pricing['output_audio'];
        
        return $inputCost + $outputCost;
    }

    /**
     * Relación con usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Filtrar por key específica
     */
    public function scopeByApiKey($query, $keyIndex)
    {
        return $query->where('api_key_index', $keyIndex);
    }

    /**
     * Scope: Solo chat de texto
     */
    public function scopeChat($query)
    {
        return $query->whereIn('request_type', ['chat', 'chat_with_file']);
    }

    /**
     * Scope: Solo voz
     */
    public function scopeVoice($query)
    {
        return $query->where('request_type', 'voice');
    }

    /**
     * Scope: Solo exitosos
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope: Solo errores
     */
    public function scopeErrors($query)
    {
        return $query->whereIn('status', ['error', 'rate_limited']);
    }

    /**
     * Scope: Últimas 24 horas
     */
    public function scopeLast24Hours($query)
    {
        return $query->where('created_at', '>=', now()->subDay());
    }

    /**
     * Scope: Última semana
     */
    public function scopeLastWeek($query)
    {
        return $query->where('created_at', '>=', now()->subWeek());
    }

    /**
     * Scope: Último mes
     */
    public function scopeLastMonth($query)
    {
        return $query->where('created_at', '>=', now()->subMonth());
    }
}
