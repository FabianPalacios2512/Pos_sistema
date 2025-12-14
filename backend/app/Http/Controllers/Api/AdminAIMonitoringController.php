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
     * Compatible con tenant y super admin
     */
    public function dashboard(Request $request)
    {
        $period = $request->get('period', '24h'); // 24h, 7d, 30d, all

        // Verificar si estamos en contexto de tenant o super admin
        $currentDatabase = DB::connection()->getDatabaseName();

        // Si no hay tenant activo (super admin), agregar datos de todos los tenants
        if (!$currentDatabase || strpos($currentDatabase, 'tenant') === false) {
            return $this->superAdminDashboard($period);
        }

        // Flujo normal para tenants
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
     * Dashboard para super admin (todos los tenants)
     */
    private function superAdminDashboard($period)
    {
        // Obtener todas las bases de datos de tenants
        $databases = DB::select('SHOW DATABASES');
        $tenantDatabases = [];

        foreach ($databases as $db) {
            $dbName = $db->Database;
            if (strpos($dbName, 'tenant') === 0 || strpos($dbName, 'tenanta') === 0) {
                $tenantDatabases[] = $dbName;
            }
        }

        $summary = [
            'total_requests' => 0,
            'successful' => 0,
            'rate_limited' => 0,
            'errors' => 0,
            'total_tokens' => 0,
            'avg_response_time_ms' => 0
        ];

        $allRequests = [];

        foreach ($tenantDatabases as $dbName) {
            try {
                $dateFilter = $this->getDateFilter($period);

                // Verificar si existe la tabla
                $tableExists = DB::select("SELECT COUNT(*) as count FROM information_schema.tables
                    WHERE table_schema = ? AND table_name = 'ai_usage_logs'", [$dbName]);

                if ($tableExists[0]->count == 0) continue;

                $stats = DB::select("
                    SELECT
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as successful,
                        SUM(CASE WHEN status = 'rate_limited' THEN 1 ELSE 0 END) as rate_limited,
                        SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as errors,
                        COALESCE(SUM(total_tokens), 0) as tokens,
                        COALESCE(AVG(response_time_ms), 0) as avg_time
                    FROM {$dbName}.ai_usage_logs
                    {$dateFilter}
                ")[0];

                $summary['total_requests'] += $stats->total;
                $summary['successful'] += $stats->successful;
                $summary['rate_limited'] += $stats->rate_limited;
                $summary['errors'] += $stats->errors;
                $summary['total_tokens'] += $stats->tokens;

                $requests = DB::select("
                    SELECT user_message, total_tokens, status, created_at
                    FROM {$dbName}.ai_usage_logs
                    {$dateFilter}
                    ORDER BY created_at DESC
                    LIMIT 10
                ");

                foreach ($requests as $req) {
                    $allRequests[] = [
                        'tenant' => $dbName,
                        'message' => substr($req->user_message, 0, 100),
                        'tokens' => $req->total_tokens,
                        'status' => $req->status,
                        'created_at' => $req->created_at
                    ];
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        if ($summary['total_requests'] > 0) {
            $summary['success_rate'] = round(($summary['successful'] / $summary['total_requests']) * 100, 2);
        }

        usort($allRequests, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'recent_requests' => array_slice($allRequests, 0, 50),
            'tenants_analyzed' => count($tenantDatabases),
            'keys_status' => [],
            'usage_by_hour' => [],
            'top_users' => []
        ]);
    }

    private function getDateFilter($period)
    {
        switch ($period) {
            case '24h':
                return "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            case '7d':
                return "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            case '30d':
                return "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            default:
                return "";
        }
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
