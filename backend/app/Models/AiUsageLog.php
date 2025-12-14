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
        'endpoint',
        'ip_address',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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
