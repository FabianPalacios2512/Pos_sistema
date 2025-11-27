<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

class AiPlanLimit extends Model
{
    protected $connection = 'mysql'; // Base de datos central

    protected $fillable = [
        'plan_name',
        'requests_per_hour',
        'requests_per_day',
        'tokens_per_request',
        'tokens_per_day',
        'unlimited',
    ];

    protected $casts = [
        'unlimited' => 'boolean',
    ];

    /**
     * Obtener límites para un plan específico
     */
    public static function getLimitsForPlan(string $planName): ?self
    {
        return self::where('plan_name', $planName)->first();
    }

    /**
     * Verificar si un plan es ilimitado
     */
    public function isUnlimited(): bool
    {
        return $this->unlimited;
    }

    /**
     * Obtener límites formateados para respuesta API
     */
    public function toApiResponse(): array
    {
        return [
            'plan' => $this->plan_name,
            'limits' => [
                'requests_per_hour' => $this->unlimited ? 'unlimited' : $this->requests_per_hour,
                'requests_per_day' => $this->unlimited ? 'unlimited' : $this->requests_per_day,
                'tokens_per_request' => $this->unlimited ? 'unlimited' : $this->tokens_per_request,
                'tokens_per_day' => $this->unlimited ? 'unlimited' : $this->tokens_per_day,
            ],
            'unlimited' => $this->unlimited,
        ];
    }
}
