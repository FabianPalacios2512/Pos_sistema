<?php

namespace App\Services;

use App\Models\Central\AiPlanLimit;
use App\Models\Central\CentralAiUsageLog;
use Stancl\Tenancy\Database\Models\Tenant;
use Carbon\Carbon;

class AiUsageService
{
    /**
     * Verificar si el tenant puede hacer una petición de IA
     */
    public function canMakeRequest(string $tenantId, int $estimatedTokens = 0): array
    {
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            return [
                'allowed' => false,
                'reason' => 'Tenant no encontrado',
            ];
        }

        // Obtener límites del plan
        $planName = $tenant->plan ?? 'free_trial';
        $limits = AiPlanLimit::getLimitsForPlan($planName);

        if (!$limits) {
            return [
                'allowed' => false,
                'reason' => 'Plan no configurado',
            ];
        }

        // Si es plan ilimitado, permitir
        if ($limits->unlimited) {
            return [
                'allowed' => true,
                'reason' => 'Plan ilimitado',
                'limits' => $limits->toApiResponse(),
            ];
        }

        // Verificar límite por hora
        $requestsLastHour = CentralAiUsageLog::where('tenant_id', $tenantId)
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->count();

        if ($requestsLastHour >= $limits->requests_per_hour) {
            return [
                'allowed' => false,
                'reason' => 'Límite de peticiones por hora alcanzado',
                'current_usage' => [
                    'requests_last_hour' => $requestsLastHour,
                    'limit_per_hour' => $limits->requests_per_hour,
                ],
            ];
        }

        // Verificar límite por día
        $requestsToday = CentralAiUsageLog::where('tenant_id', $tenantId)
            ->whereDate('created_at', Carbon::today())
            ->count();

        if ($requestsToday >= $limits->requests_per_day) {
            return [
                'allowed' => false,
                'reason' => 'Límite de peticiones diarias alcanzado',
                'current_usage' => [
                    'requests_today' => $requestsToday,
                    'limit_per_day' => $limits->requests_per_day,
                ],
            ];
        }

        // Verificar tokens por día
        $tokensToday = CentralAiUsageLog::where('tenant_id', $tenantId)
            ->whereDate('created_at', Carbon::today())
            ->sum('tokens_used');

        if ($tokensToday >= $limits->tokens_per_day) {
            return [
                'allowed' => false,
                'reason' => 'Límite de tokens diarios alcanzado',
                'current_usage' => [
                    'tokens_today' => $tokensToday,
                    'limit_tokens_day' => $limits->tokens_per_day,
                ],
            ];
        }

        // Verificar tokens por petición (si se proporciona estimación)
        if ($estimatedTokens > 0 && $estimatedTokens > $limits->tokens_per_request) {
            return [
                'allowed' => false,
                'reason' => 'La petición excede el límite de tokens por request',
                'current_usage' => [
                    'estimated_tokens' => $estimatedTokens,
                    'limit_per_request' => $limits->tokens_per_request,
                ],
            ];
        }

        return [
            'allowed' => true,
            'reason' => 'Dentro de los límites',
            'current_usage' => [
                'requests_last_hour' => $requestsLastHour,
                'requests_today' => $requestsToday,
                'tokens_today' => $tokensToday,
            ],
            'limits' => $limits->toApiResponse(),
        ];
    }

    /**
     * Obtener estadísticas de uso actual para un tenant
     */
    public function getUsageStats(string $tenantId): array
    {
        $tenant = Tenant::find($tenantId);
        $planName = $tenant->plan ?? 'free_trial';
        $limits = AiPlanLimit::getLimitsForPlan($planName);

        // Uso en la última hora
        $requestsLastHour = CentralAiUsageLog::where('tenant_id', $tenantId)
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->count();

        $tokensLastHour = CentralAiUsageLog::where('tenant_id', $tenantId)
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->sum('tokens_used');

        // Uso hoy
        $requestsToday = CentralAiUsageLog::where('tenant_id', $tenantId)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $tokensToday = CentralAiUsageLog::where('tenant_id', $tenantId)
            ->whereDate('created_at', Carbon::today())
            ->sum('tokens_used');

        $costToday = CentralAiUsageLog::where('tenant_id', $tenantId)
            ->whereDate('created_at', Carbon::today())
            ->sum('estimated_cost');

        // Total histórico
        $totalRequests = CentralAiUsageLog::where('tenant_id', $tenantId)->count();
        $totalTokens = CentralAiUsageLog::where('tenant_id', $tenantId)->sum('tokens_used');
        $totalCost = CentralAiUsageLog::where('tenant_id', $tenantId)->sum('estimated_cost');

        return [
            'tenant_id' => $tenantId,
            'plan' => $planName,
            'limits' => $limits ? $limits->toApiResponse() : null,
            'usage' => [
                'last_hour' => [
                    'requests' => $requestsLastHour,
                    'tokens' => $tokensLastHour,
                    'remaining_requests' => $limits && !$limits->unlimited
                        ? max(0, $limits->requests_per_hour - $requestsLastHour)
                        : 'unlimited',
                ],
                'today' => [
                    'requests' => $requestsToday,
                    'tokens' => $tokensToday,
                    'cost' => round($costToday, 4),
                    'remaining_requests' => $limits && !$limits->unlimited
                        ? max(0, $limits->requests_per_day - $requestsToday)
                        : 'unlimited',
                    'remaining_tokens' => $limits && !$limits->unlimited
                        ? max(0, $limits->tokens_per_day - $tokensToday)
                        : 'unlimited',
                ],
                'total' => [
                    'requests' => $totalRequests,
                    'tokens' => $totalTokens,
                    'cost' => round($totalCost, 4),
                ],
            ],
            'warnings' => $this->getUsageWarnings($tenantId, $limits, $requestsLastHour, $requestsToday, $tokensToday),
        ];
    }

    /**
     * Generar advertencias si está cerca de los límites
     */
    private function getUsageWarnings(string $tenantId, ?AiPlanLimit $limits, int $requestsLastHour, int $requestsToday, int $tokensToday): array
    {
        $warnings = [];

        if (!$limits || $limits->unlimited) {
            return $warnings;
        }

        // Advertencia: 80% del límite por hora
        if ($requestsLastHour >= ($limits->requests_per_hour * 0.8)) {
            $warnings[] = [
                'type' => 'hour_limit',
                'severity' => 'warning',
                'message' => 'Has usado el ' . round(($requestsLastHour / $limits->requests_per_hour) * 100) . '% de tus peticiones por hora',
            ];
        }

        // Advertencia: 90% del límite diario
        if ($requestsToday >= ($limits->requests_per_day * 0.9)) {
            $warnings[] = [
                'type' => 'day_limit',
                'severity' => 'critical',
                'message' => 'Has usado el ' . round(($requestsToday / $limits->requests_per_day) * 100) . '% de tus peticiones diarias',
            ];
        }

        // Advertencia: 80% de tokens diarios
        if ($tokensToday >= ($limits->tokens_per_day * 0.8)) {
            $warnings[] = [
                'type' => 'tokens_limit',
                'severity' => 'warning',
                'message' => 'Has usado el ' . round(($tokensToday / $limits->tokens_per_day) * 100) . '% de tus tokens diarios',
            ];
        }

        return $warnings;
    }

    /**
     * Registrar uso de IA
     */
    public function logUsage(string $tenantId, string $actionType, int $tokensUsed, string $model = 'gpt-4', float $estimatedCost = 0): void
    {
        CentralAiUsageLog::create([
            'tenant_id' => $tenantId,
            'action_type' => $actionType,
            'tokens_used' => $tokensUsed,
            'model_used' => $model,
            'estimated_cost' => $estimatedCost,
        ]);
    }
}
