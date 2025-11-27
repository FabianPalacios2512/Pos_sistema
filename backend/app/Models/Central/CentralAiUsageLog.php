<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant;

/**
 * Modelo CENTRAL para registrar el consumo de IA por tenant
 * Esta tabla vive en la base de datos CENTRAL (mysql connection)
 *
 * IMPORTANTE: Este modelo NO usa la conexión tenant, siempre usa la central
 */
class CentralAiUsageLog extends Model
{
    protected $connection = 'mysql'; // Conexión CENTRAL
    protected $table = 'ai_usage_logs'; // Tabla en BD central

    protected $fillable = [
        'tenant_id',
        'action_type',
        'tokens_used',
        'estimated_cost',
        'model_used',
        'prompt_summary',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'tokens_used' => 'integer',
        'estimated_cost' => 'decimal:6',
    ];

    /**
     * Relación con el Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Helper para registrar consumo de IA desde contexto de tenant
     *
     * Ejemplo de uso desde AIController del tenant:
     *
     * use App\Models\Central\CentralAiUsageLog;
     *
     * CentralAiUsageLog::logUsage('chat', 1500, 'gpt-4o-mini', 'Análisis de ventas...');
     */
    public static function logUsage(
        string $actionType,
        int $tokensUsed,
        string $modelUsed = 'gpt-4o-mini',
        ?string $promptSummary = null,
        ?array $metadata = null
    ) {
        // Obtener el tenant_id actual (si estamos en contexto tenant)
        $tenantId = null;

        try {
            $tenantId = tenant('id');
        } catch (\Exception $e) {
            \Log::warning('Intento de registrar AI usage sin tenant context');
            return null;
        }

        if (!$tenantId) {
            \Log::warning('Intento de registrar AI usage sin tenant_id');
            return null;
        }

        // Calcular costo estimado según el modelo
        $costPerToken = match($modelUsed) {
            'gpt-4' => 0.00003,           // $0.03 / 1K tokens
            'gpt-4-turbo' => 0.00001,     // $0.01 / 1K tokens
            'gpt-4o' => 0.000005,         // $0.005 / 1K tokens
            'gpt-4o-mini' => 0.00000015,  // $0.00015 / 1K tokens (default)
            'gpt-3.5-turbo' => 0.0000005, // $0.0005 / 1K tokens
            default => 0.00000015,
        };

        $estimatedCost = $tokensUsed * $costPerToken;

        return self::create([
            'tenant_id' => $tenantId,
            'action_type' => $actionType,
            'tokens_used' => $tokensUsed,
            'estimated_cost' => $estimatedCost,
            'model_used' => $modelUsed,
            'prompt_summary' => $promptSummary ? substr($promptSummary, 0, 500) : null,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Scopes para análisis
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeByActionType($query, $actionType)
    {
        return $query->where('action_type', $actionType);
    }
}
