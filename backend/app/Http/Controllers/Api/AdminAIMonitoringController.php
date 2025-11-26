<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AiUsageLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminAIMonitoringController extends Controller
{
    /**
     * Dashboard general de monitoreo de IA
     */
    public function dashboard(Request $request)
    {
        $period = $request->get('period', '24h'); // 24h, 7d, 30d, all

        $query = AiUsageLog::query();

        // Filtrar por período
        switch ($period) {
            case '24h':
                $query->last24Hours();
                break;
            case '7d':
                $query->lastWeek();
                break;
            case '30d':
                $query->lastMonth();
                break;
        }

        return response()->json([
            'summary' => $this->getSummary($period),
            'keys_status' => $this->getKeysStatus($period),
            'usage_by_hour' => $this->getUsageByHour($period),
            'recent_requests' => $this->getRecentRequests(20),
            'top_users' => $this->getTopUsers($period),
        ]);
    }

    /**
     * Resumen general
     */
    private function getSummary($period)
    {
        $query = AiUsageLog::query();

        switch ($period) {
            case '24h':
                $query->last24Hours();
                break;
            case '7d':
                $query->lastWeek();
                break;
            case '30d':
                $query->lastMonth();
                break;
        }

        $total = $query->count();
        $successful = (clone $query)->where('status', 'success')->count();
        $rateLimited = (clone $query)->where('status', 'rate_limited')->count();
        $errors = (clone $query)->where('status', 'error')->count();

        $totalTokens = (clone $query)->sum('total_tokens');
        $avgResponseTime = (clone $query)->avg('response_time_ms');

        return [
            'total_requests' => $total,
            'successful' => $successful,
            'rate_limited' => $rateLimited,
            'errors' => $errors,
            'success_rate' => $total > 0 ? round(($successful / $total) * 100, 2) : 0,
            'total_tokens' => $totalTokens,
            'avg_response_time_ms' => round($avgResponseTime, 0),
        ];
    }

    /**
     * Estado de cada API Key
     */
    private function getKeysStatus($period)
    {
        $query = AiUsageLog::query();

        switch ($period) {
            case '24h':
                $query->last24Hours();
                break;
            case '7d':
                $query->lastWeek();
                break;
            case '30d':
                $query->lastMonth();
                break;
        }

        $keys = [];
        for ($i = 1; $i <= 10; $i++) {
            // Verificar si la key existe en .env
            $envKey = env("GROQ_API_KEY_{$i}");

            if (!empty($envKey)) {
                $keyQuery = (clone $query)->where('api_key_index', $i);
                $keyStats = $keyQuery->get();

                // Obtener últimos 4 caracteres de la key del .env
                $keyLast4 = substr($envKey, -4);

                if ($keyStats->count() > 0) {
                    // Key con registros - calcular stats reales
                    $keys[] = [
                        'key_index' => $i,
                        'key_last_4' => $keyLast4,
                        'total_requests' => $keyStats->count(),
                        'successful' => $keyStats->where('status', 'success')->count(),
                        'rate_limited' => $keyStats->where('status', 'rate_limited')->count(),
                        'errors' => $keyStats->where('status', 'error')->count(),
                        'total_tokens' => $keyStats->sum('total_tokens'),
                        'avg_response_time' => round($keyStats->avg('response_time_ms'), 0),
                        'status' => $keyStats->where('status', 'rate_limited')->count() > 0 ? 'rate_limited' : 'active',
                    ];
                } else {
                    // Key configurada pero sin uso - mostrar con stats en 0
                    $keys[] = [
                        'key_index' => $i,
                        'key_last_4' => $keyLast4,
                        'total_requests' => 0,
                        'successful' => 0,
                        'rate_limited' => 0,
                        'errors' => 0,
                        'total_tokens' => 0,
                        'avg_response_time' => 0,
                        'status' => 'active', // Nueva key = disponible
                    ];
                }
            }
        }

        return $keys;
    }

    /**
     * Uso por hora
     */
    private function getUsageByHour($period)
    {
        $hours = $period === '24h' ? 24 : ($period === '7d' ? 24 * 7 : 24 * 30);

        $usage = AiUsageLog::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as hour'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as successful'),
            DB::raw('SUM(total_tokens) as tokens')
        )
        ->where('created_at', '>=', now()->subHours($hours))
        ->groupBy('hour')
        ->orderBy('hour', 'asc')
        ->get();

        return $usage;
    }

    /**
     * Peticiones recientes
     */
    private function getRecentRequests($limit = 20)
    {
        return AiUsageLog::with('user:id,name,email')
            ->select([
                'id',
                'user_id',
                'api_key_index',
                'api_key_last_4',
                'user_message',
                'total_tokens',
                'status',
                'response_time_ms',
                'created_at'
            ])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user' => $log->user ? $log->user->name : 'Sistema',
                    'key_index' => $log->api_key_index,
                    'key_last_4' => $log->api_key_last_4,
                    'message_preview' => substr($log->user_message ?? '', 0, 100) . '...',
                    'tokens' => $log->total_tokens,
                    'status' => $log->status,
                    'response_time' => $log->response_time_ms,
                    'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    /**
     * Usuarios que más usan la IA
     */
    private function getTopUsers($period)
    {
        $query = AiUsageLog::query();

        switch ($period) {
            case '24h':
                $query->last24Hours();
                break;
            case '7d':
                $query->lastWeek();
                break;
            case '30d':
                $query->lastMonth();
                break;
        }

        return $query->select([
                'user_id',
                DB::raw('COUNT(*) as total_requests'),
                DB::raw('SUM(total_tokens) as total_tokens'),
                DB::raw('AVG(response_time_ms) as avg_response_time')
            ])
            ->with('user:id,name,email')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderBy('total_requests', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($stat) {
                return [
                    'user_id' => $stat->user_id,
                    'user_name' => $stat->user->name ?? 'Usuario eliminado',
                    'user_email' => $stat->user->email ?? '',
                    'total_requests' => $stat->total_requests,
                    'total_tokens' => $stat->total_tokens,
                    'avg_response_time' => round($stat->avg_response_time, 0),
                ];
            });
    }

    /**
     * Estadísticas detalladas de una key específica
     */
    public function keyDetails(Request $request, $keyIndex)
    {
        $period = $request->get('period', '7d');

        $query = AiUsageLog::where('api_key_index', $keyIndex);

        switch ($period) {
            case '24h':
                $query->last24Hours();
                break;
            case '7d':
                $query->lastWeek();
                break;
            case '30d':
                $query->lastMonth();
                break;
        }

        $logs = $query->get();

        return response()->json([
            'key_index' => $keyIndex,
            'key_last_4' => $logs->first()->api_key_last_4 ?? 'N/A',
            'stats' => [
                'total_requests' => $logs->count(),
                'successful' => $logs->where('status', 'success')->count(),
                'rate_limited' => $logs->where('status', 'rate_limited')->count(),
                'errors' => $logs->where('status', 'error')->count(),
                'total_tokens' => $logs->sum('total_tokens'),
                'prompt_tokens' => $logs->sum('prompt_tokens'),
                'completion_tokens' => $logs->sum('completion_tokens'),
                'avg_response_time' => round($logs->avg('response_time_ms'), 0),
            ],
            'recent_logs' => $logs->sortByDesc('created_at')->take(50)->values()->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user' => $log->user->name ?? 'Sistema',
                    'status' => $log->status,
                    'tokens' => $log->total_tokens,
                    'response_time' => $log->response_time_ms,
                    'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
                    'message_preview' => substr($log->user_message ?? '', 0, 100),
                ];
            }),
        ]);
    }
}
