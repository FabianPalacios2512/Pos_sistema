<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\AiUsageService;

class CheckAiUsageLimit
{
    protected $aiUsageService;

    public function __construct(AiUsageService $aiUsageService)
    {
        $this->aiUsageService = $aiUsageService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtener tenant_id del tenant actual
        $tenantId = tenant('id');

        if (!$tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo identificar el tenant',
            ], 403);
        }

        // Verificar si puede hacer la petición
        $check = $this->aiUsageService->canMakeRequest($tenantId, 0);

        if (!$check['allowed']) {
            // Obtener estadísticas completas para mostrar más información
            $stats = $this->aiUsageService->getUsageStats($tenantId);

            // Calcular tiempo de espera correcto basado en la petición más antigua de la última hora
            $oldestRequest = \App\Models\Central\CentralAiUsageLog::where('tenant_id', $tenantId)
                ->where('created_at', '>=', now()->subHour())
                ->orderBy('created_at', 'asc')
                ->first();

            $waitUntil = null;
            $minutesRemaining = 60; // Por defecto 1 hora

            if ($oldestRequest) {
                // La ventana de 1 hora se renueva desde la petición más antigua
                $waitUntil = $oldestRequest->created_at->addHour();
                $minutesRemaining = max(0, now()->diffInMinutes($waitUntil, false));

                // Si ya pasó el tiempo, ajustar (por si hay algún delay)
                if ($minutesRemaining <= 0) {
                    $minutesRemaining = 0;
                    $waitUntil = now();
                }
            } else {
                $waitUntil = now()->addHour();
            }

            $message = $check['reason'];
            if (str_contains($check['reason'], 'hora')) {
                $message = "Has alcanzado tu límite de {$stats['limits']['limits']['requests_per_hour']} peticiones por hora. ";
                $message .= "Podrás volver a usar la IA en " . ceil($minutesRemaining) . " minutos (a las " . $waitUntil->format('H:i') . ").";
            }

            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => 'AI_LIMIT_EXCEEDED',
                'limit_type' => str_contains($check['reason'], 'hora') ? 'hourly' : 'daily',
                'usage' => [
                    'current_hour' => $stats['usage']['last_hour']['requests'],
                    'limit_hour' => $stats['limits']['limits']['requests_per_hour'],
                    'remaining_hour' => $stats['usage']['last_hour']['remaining_requests'],
                    'current_day' => $stats['usage']['today']['requests'],
                    'limit_day' => $stats['limits']['limits']['requests_per_day'],
                    'remaining_day' => $stats['usage']['today']['remaining_requests'],
                ],
                'wait_until' => $waitUntil->format('H:i'),
                'minutes_remaining' => ceil($minutesRemaining),
                'upgrade_message' => 'Actualiza tu plan para obtener más peticiones de IA',
            ], 429); // 429 Too Many Requests
        }        return $next($request);
    }
}
