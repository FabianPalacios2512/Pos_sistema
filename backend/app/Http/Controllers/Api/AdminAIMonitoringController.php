<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AiUsageLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AdminAIMonitoringController extends Controller
{
    /**
     * Dashboard general de monitoreo de IA
     * Compatible con tenant y super admin
     */
    public function dashboard(Request $request)
    {
        $period = $request->get('period', '24h');

        $currentDatabase = DB::connection()->getDatabaseName();

        if (!$currentDatabase || strpos($currentDatabase, 'tenant') === false) {
            return $this->superAdminDashboard($period);
        }

        return $this->tenantDashboard($period);
    }

    /**
     * Dashboard completo para super admin (todos los tenants)
     */
    private function superAdminDashboard($period)
    {
        $tenantDatabases = $this->getTenantDatabases();
        $dateFilter = $this->getDateFilter($period);
        $dateFilterAnd = $this->getDateFilter($period, true);

        $summary = [
            'total_requests' => 0, 'successful' => 0, 'rate_limited' => 0, 'errors' => 0,
            'total_tokens' => 0, 'total_input_tokens' => 0, 'total_output_tokens' => 0,
            'avg_response_time_ms' => 0, 'total_cost_usd' => 0, 'total_cost_cop' => 0,
            'chat_requests' => 0, 'voice_requests' => 0, 'voice_minutes' => 0,
        ];

        $allRequests = [];
        $keyStatsAgg = [];
        $tenantBreakdown = [];
        $modelBreakdown = [];
        $dailyAgg = [];
        $hourlyAgg = [];
        $totalResponseTime = 0;
        $totalResponseCount = 0;
        $firstDate = null;

        foreach ($tenantDatabases as $dbName) {
            try {
                $tableExists = DB::select(
                    "SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_schema = ? AND table_name = 'ai_usage_logs'",
                    [$dbName]
                );
                if ($tableExists[0]->cnt == 0) continue;

                // Summary stats
                $stats = DB::select("
                    SELECT
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as successful,
                        SUM(CASE WHEN status = 'rate_limited' THEN 1 ELSE 0 END) as rate_limited,
                        SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as errors,
                        COALESCE(SUM(total_tokens), 0) as tokens,
                        COALESCE(SUM(prompt_tokens), 0) as input_tokens,
                        COALESCE(SUM(completion_tokens), 0) as output_tokens,
                        COALESCE(SUM(response_time_ms), 0) as total_time,
                        COALESCE(SUM(CASE WHEN response_time_ms > 0 THEN 1 ELSE 0 END), 0) as time_count,
                        COALESCE(SUM(cost_usd), 0) as cost_usd,
                        SUM(CASE WHEN request_type = 'voice' THEN 1 ELSE 0 END) as voice_count,
                        COALESCE(SUM(voice_duration_seconds), 0) as voice_seconds,
                        MIN(created_at) as first_date
                    FROM {$dbName}.ai_usage_logs
                    {$dateFilter}
                ")[0];

                $summary['total_requests'] += $stats->total;
                $summary['successful'] += $stats->successful;
                $summary['rate_limited'] += $stats->rate_limited;
                $summary['errors'] += $stats->errors;
                $summary['total_tokens'] += $stats->tokens;
                $summary['total_input_tokens'] += $stats->input_tokens;
                $summary['total_output_tokens'] += $stats->output_tokens;
                $totalResponseTime += $stats->total_time;
                $totalResponseCount += $stats->time_count;
                $summary['total_cost_usd'] += floatval($stats->cost_usd);
                $summary['voice_requests'] += $stats->voice_count;
                $summary['voice_minutes'] += round($stats->voice_seconds / 60, 2);
                $summary['chat_requests'] += ($stats->total - $stats->voice_count);

                if ($stats->first_date && (!$firstDate || $stats->first_date < $firstDate)) {
                    $firstDate = $stats->first_date;
                }

                // Tenant breakdown
                if ($stats->total > 0) {
                    $tenantId = str_replace(['tenant', 'tenanta'], '', $dbName);
                    $tenantBreakdown[] = [
                        'tenant_id' => $tenantId ?: $dbName,
                        'total_requests' => (int) $stats->total,
                        'tokens' => (int) $stats->tokens,
                        'cost_usd' => round(floatval($stats->cost_usd), 6),
                    ];
                }

                // Per-key stats
                $keyRows = DB::select("
                    SELECT
                        api_key_index,
                        api_key_last_4,
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as successful,
                        SUM(CASE WHEN status = 'rate_limited' THEN 1 ELSE 0 END) as rate_limited,
                        SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as errors,
                        COALESCE(SUM(total_tokens), 0) as tokens,
                        COALESCE(AVG(response_time_ms), 0) as avg_time,
                        MAX(created_at) as last_used
                    FROM {$dbName}.ai_usage_logs
                    {$dateFilter}
                    GROUP BY api_key_index, api_key_last_4
                ");

                foreach ($keyRows as $kr) {
                    $idx = (int) $kr->api_key_index;
                    if (!isset($keyStatsAgg[$idx])) {
                        $keyStatsAgg[$idx] = [
                            'key_index' => $idx, 'key_last_4' => $kr->api_key_last_4,
                            'total_requests' => 0, 'successful' => 0, 'rate_limited' => 0,
                            'errors' => 0, 'total_tokens' => 0, 'avg_times' => [], 'last_used' => null,
                        ];
                    }
                    $keyStatsAgg[$idx]['total_requests'] += $kr->total;
                    $keyStatsAgg[$idx]['successful'] += $kr->successful;
                    $keyStatsAgg[$idx]['rate_limited'] += $kr->rate_limited;
                    $keyStatsAgg[$idx]['errors'] += $kr->errors;
                    $keyStatsAgg[$idx]['total_tokens'] += $kr->tokens;
                    $keyStatsAgg[$idx]['avg_times'][] = (float) $kr->avg_time;
                    if (!$keyStatsAgg[$idx]['last_used'] || $kr->last_used > $keyStatsAgg[$idx]['last_used']) {
                        $keyStatsAgg[$idx]['last_used'] = $kr->last_used;
                    }
                }

                // Model breakdown
                $modelRows = DB::select("
                    SELECT
                        COALESCE(provider, 'groq') as provider,
                        COALESCE(model, 'llama-3.3-70b') as model,
                        COUNT(*) as total,
                        COALESCE(SUM(total_tokens), 0) as tokens,
                        COALESCE(SUM(cost_usd), 0) as cost_usd
                    FROM {$dbName}.ai_usage_logs
                    {$dateFilter}
                    GROUP BY provider, model
                ");

                foreach ($modelRows as $mr) {
                    $key = $mr->provider . '|' . $mr->model;
                    if (!isset($modelBreakdown[$key])) {
                        $modelBreakdown[$key] = [
                            'provider' => $mr->provider,
                            'model' => $mr->model,
                            'requests' => 0, 'tokens' => 0, 'cost' => 0,
                        ];
                    }
                    $modelBreakdown[$key]['requests'] += $mr->total;
                    $modelBreakdown[$key]['tokens'] += $mr->tokens;
                    $modelBreakdown[$key]['cost'] += floatval($mr->cost_usd);
                }

                // Daily usage (last 30 days always)
                $dailyRows = DB::select("
                    SELECT DATE(created_at) as day, COUNT(*) as requests,
                        SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as errors,
                        COALESCE(SUM(total_tokens), 0) as tokens,
                        COALESCE(SUM(cost_usd), 0) as cost
                    FROM {$dbName}.ai_usage_logs
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    GROUP BY DATE(created_at)
                ");

                foreach ($dailyRows as $dr) {
                    $d = $dr->day;
                    if (!isset($dailyAgg[$d])) {
                        $dailyAgg[$d] = ['day' => $d, 'requests' => 0, 'errors' => 0, 'tokens' => 0, 'cost' => 0];
                    }
                    $dailyAgg[$d]['requests'] += $dr->requests;
                    $dailyAgg[$d]['errors'] += $dr->errors;
                    $dailyAgg[$d]['tokens'] += $dr->tokens;
                    $dailyAgg[$d]['cost'] += floatval($dr->cost);
                }

                // Hourly usage (last 48h)
                $hourlyRows = DB::select("
                    SELECT DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') as hour,
                        COUNT(*) as requests,
                        COALESCE(SUM(total_tokens), 0) as tokens,
                        SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as errors
                    FROM {$dbName}.ai_usage_logs
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 48 HOUR)
                    GROUP BY DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00')
                ");

                foreach ($hourlyRows as $hr) {
                    $h = $hr->hour;
                    if (!isset($hourlyAgg[$h])) {
                        $hourlyAgg[$h] = ['hour' => $h, 'requests' => 0, 'tokens' => 0, 'errors' => 0];
                    }
                    $hourlyAgg[$h]['requests'] += $hr->requests;
                    $hourlyAgg[$h]['tokens'] += $hr->tokens;
                    $hourlyAgg[$h]['errors'] += $hr->errors;
                }

                // Recent requests
                $requests = DB::select("
                    SELECT user_message, total_tokens, status, created_at, request_type,
                        cost_usd, voice_duration_seconds, response_time_ms, model, provider
                    FROM {$dbName}.ai_usage_logs
                    {$dateFilter}
                    ORDER BY created_at DESC
                    LIMIT 15
                ");

                $tenantLabel = $tenantId ?? $dbName;
                foreach ($requests as $req) {
                    $allRequests[] = [
                        'tenant' => $tenantLabel,
                        'message' => substr($req->user_message ?? '', 0, 100),
                        'tokens' => (int) $req->total_tokens,
                        'status' => $req->status,
                        'type' => $req->request_type ?? 'chat',
                        'cost_usd' => floatval($req->cost_usd ?? 0),
                        'voice_seconds' => (int) ($req->voice_duration_seconds ?? 0),
                        'response_time_ms' => (int) ($req->response_time_ms ?? 0),
                        'model' => $req->model ?? '',
                        'provider' => $req->provider ?? 'groq',
                        'created_at' => $req->created_at,
                    ];
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Finalize summary
        $summary['avg_response_time_ms'] = $totalResponseCount > 0 ? round($totalResponseTime / $totalResponseCount) : 0;
        $summary['success_rate'] = $summary['total_requests'] > 0
            ? round(($summary['successful'] / $summary['total_requests']) * 100, 2) : 0;
        $summary['total_cost_usd'] = round($summary['total_cost_usd'], 6);
        $summary['total_cost_cop'] = round($summary['total_cost_usd'] * 4200, 2);

        // Build keys_status with configured keys
        $configuredKeys = $this->getConfiguredGroqKeys();
        $keysStatus = [];
        foreach ($configuredKeys as $idx => $last4) {
            $agg = $keyStatsAgg[$idx] ?? null;
            $status = 'idle';
            if ($agg) {
                $status = $agg['rate_limited'] > 0 ? 'degraded' : ($agg['errors'] > ($agg['total_requests'] * 0.3) ? 'degraded' : 'active');
            }
            $keysStatus[] = [
                'key_index' => $idx,
                'key_last_4' => $agg['key_last_4'] ?? $last4,
                'total_requests' => $agg['total_requests'] ?? 0,
                'successful' => $agg['successful'] ?? 0,
                'rate_limited' => $agg['rate_limited'] ?? 0,
                'errors' => $agg['errors'] ?? 0,
                'total_tokens' => $agg['total_tokens'] ?? 0,
                'avg_response_time' => $agg ? round(array_sum($agg['avg_times']) / max(count($agg['avg_times']), 1)) : 0,
                'status' => $status,
                'last_used' => $agg['last_used'] ?? null,
            ];
        }

        // Sort arrays
        usort($allRequests, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));
        usort($tenantBreakdown, fn($a, $b) => $b['total_requests'] - $a['total_requests']);

        ksort($dailyAgg);
        ksort($hourlyAgg);

        $modelBreakdownArr = array_values($modelBreakdown);
        usort($modelBreakdownArr, fn($a, $b) => $b['requests'] - $a['requests']);

        // Cost projections
        $daysWithData = count($dailyAgg) ?: 1;
        $dailyAvgCost = $summary['total_cost_usd'] / $daysWithData;

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'keys_status' => $keysStatus,
            'keys_total' => count($configuredKeys),
            'usage_by_day' => array_values($dailyAgg),
            'usage_by_hour' => array_values($hourlyAgg),
            'tenant_breakdown' => $tenantBreakdown,
            'model_breakdown' => $modelBreakdownArr,
            'recent_requests' => array_slice($allRequests, 0, 50),
            'tenants_analyzed' => count($tenantDatabases),
            'cost_projection_monthly_usd' => round($dailyAvgCost * 30, 4),
            'cost_projection_monthly_cop' => round($dailyAvgCost * 30 * 4200, 2),
        ]);
    }

    /**
     * Dashboard para tenant individual
     */
    private function tenantDashboard($period)
    {
        $query = AiUsageLog::query();
        switch ($period) {
            case '24h': $query->last24Hours(); break;
            case '7d': $query->lastWeek(); break;
            case '30d': $query->lastMonth(); break;
        }

        return response()->json([
            'success' => true,
            'summary' => $this->getTenantSummary($period),
            'keys_status' => $this->getTenantKeysStatus($period),
            'usage_by_hour' => $this->getTenantUsageByHour($period),
            'recent_requests' => $this->getTenantRecentRequests(20),
            'top_users' => $this->getTenantTopUsers($period),
        ]);
    }

    // ==================== HELPER METHODS ====================

    private function getTenantDatabases(): array
    {
        $databases = DB::select('SHOW DATABASES');
        $result = [];
        foreach ($databases as $db) {
            $name = $db->Database;
            if (str_starts_with($name, 'tenant')) {
                $result[] = $name;
            }
        }
        return $result;
    }

    private function getConfiguredGroqKeys(): array
    {
        $keys = [];
        for ($i = 1; $i <= 20; $i++) {
            $val = config("services.groq.api_key_{$i}");
            if (!empty($val)) {
                $keys[$i] = substr($val, -4);
            }
        }
        return $keys;
    }

    private function getDateFilter($period, $asAnd = false): string
    {
        $prefix = $asAnd ? 'AND' : 'WHERE';
        switch ($period) {
            case '24h': return "{$prefix} created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            case '7d':  return "{$prefix} created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            case '30d': return "{$prefix} created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            default:    return "";
        }
    }

    // ==================== TENANT-SPECIFIC METHODS ====================

    private function getTenantSummary($period)
    {
        $query = AiUsageLog::query();
        switch ($period) {
            case '24h': $query->last24Hours(); break;
            case '7d': $query->lastWeek(); break;
            case '30d': $query->lastMonth(); break;
        }

        $total = $query->count();
        $successful = (clone $query)->where('status', 'success')->count();
        $rateLimited = (clone $query)->where('status', 'rate_limited')->count();
        $errors = (clone $query)->where('status', 'error')->count();
        $totalTokens = (clone $query)->sum('total_tokens');
        $avgResponseTime = (clone $query)->avg('response_time_ms');
        $totalCostUsd = (clone $query)->sum('cost_usd');
        $chatRequests = (clone $query)->whereIn('request_type', ['chat', 'chat_with_file'])->count();
        $voiceRequests = (clone $query)->where('request_type', 'voice')->count();
        $totalVoiceSeconds = (clone $query)->where('request_type', 'voice')->sum('voice_duration_seconds');

        return [
            'total_requests' => $total,
            'successful' => $successful,
            'rate_limited' => $rateLimited,
            'errors' => $errors,
            'success_rate' => $total > 0 ? round(($successful / $total) * 100, 2) : 0,
            'total_tokens' => $totalTokens,
            'avg_response_time_ms' => round($avgResponseTime, 0),
            'total_cost_usd' => round($totalCostUsd, 6),
            'total_cost_cop' => round($totalCostUsd * 4200, 2),
            'chat_requests' => $chatRequests,
            'voice_requests' => $voiceRequests,
            'voice_minutes' => round($totalVoiceSeconds / 60, 2),
        ];
    }

    private function getTenantKeysStatus($period)
    {
        $query = AiUsageLog::query();
        switch ($period) {
            case '24h': $query->last24Hours(); break;
            case '7d': $query->lastWeek(); break;
            case '30d': $query->lastMonth(); break;
        }

        $keys = [];
        for ($i = 1; $i <= 20; $i++) {
            $envKey = config("services.groq.api_key_{$i}");
            if (!empty($envKey)) {
                $keyQuery = (clone $query)->where('api_key_index', $i);
                $keyStats = $keyQuery->get();
                $keys[] = [
                    'key_index' => $i,
                    'key_last_4' => substr($envKey, -4),
                    'total_requests' => $keyStats->count(),
                    'successful' => $keyStats->where('status', 'success')->count(),
                    'rate_limited' => $keyStats->where('status', 'rate_limited')->count(),
                    'errors' => $keyStats->where('status', 'error')->count(),
                    'total_tokens' => $keyStats->sum('total_tokens'),
                    'avg_response_time' => round($keyStats->avg('response_time_ms') ?? 0),
                    'status' => $keyStats->count() === 0 ? 'idle'
                        : ($keyStats->where('status', 'rate_limited')->count() > 0 ? 'rate_limited' : 'active'),
                ];
            }
        }
        return $keys;
    }

    private function getTenantUsageByHour($period)
    {
        $hours = $period === '24h' ? 24 : ($period === '7d' ? 168 : 720);
        return AiUsageLog::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as hour'),
            DB::raw('COUNT(*) as requests'),
            DB::raw('SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as successful'),
            DB::raw('COALESCE(SUM(total_tokens), 0) as tokens')
        )
        ->where('created_at', '>=', now()->subHours($hours))
        ->groupBy('hour')
        ->orderBy('hour', 'asc')
        ->get();
    }

    private function getTenantRecentRequests($limit = 20)
    {
        return AiUsageLog::with('user:id,name,email')
            ->select(['id', 'user_id', 'api_key_index', 'api_key_last_4', 'user_message',
                'total_tokens', 'status', 'response_time_ms', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($log) => [
                'id' => $log->id,
                'user' => $log->user?->name ?? 'Sistema',
                'key_index' => $log->api_key_index,
                'key_last_4' => $log->api_key_last_4,
                'message_preview' => substr($log->user_message ?? '', 0, 100),
                'tokens' => $log->total_tokens,
                'status' => $log->status,
                'response_time' => $log->response_time_ms,
                'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
            ]);
    }

    private function getTenantTopUsers($period)
    {
        $query = AiUsageLog::query();
        switch ($period) {
            case '24h': $query->last24Hours(); break;
            case '7d': $query->lastWeek(); break;
            case '30d': $query->lastMonth(); break;
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
            ->map(fn($stat) => [
                'user_id' => $stat->user_id,
                'user_name' => $stat->user?->name ?? 'Usuario eliminado',
                'user_email' => $stat->user?->email ?? '',
                'total_requests' => $stat->total_requests,
                'total_tokens' => $stat->total_tokens,
                'avg_response_time' => round($stat->avg_response_time, 0),
            ]);
    }

    /**
     * Test a specific Groq key live or return stats
     * Super admin context: sends a real ping to Groq API
     * Tenant context: returns usage stats from DB
     */
    public function keyDetails(Request $request, $keyIndex)
    {
        $keyIndex = (int) $keyIndex;
        $currentDatabase = DB::connection()->getDatabaseName();

        // Super admin context: test the key live against Groq
        if (!$currentDatabase || strpos($currentDatabase, 'tenant') === false) {
            return $this->testGroqKeyLive($keyIndex);
        }

        // Tenant context: return DB stats
        $period = $request->get('period', '7d');
        $query = AiUsageLog::where('api_key_index', $keyIndex);
        switch ($period) {
            case '24h': $query->last24Hours(); break;
            case '7d': $query->lastWeek(); break;
            case '30d': $query->lastMonth(); break;
        }

        $logs = $query->get();
        return response()->json([
            'success' => true,
            'key_index' => $keyIndex,
            'key_last_4' => $logs->first()?->api_key_last_4 ?? 'N/A',
            'stats' => [
                'total_requests' => $logs->count(),
                'successful' => $logs->where('status', 'success')->count(),
                'rate_limited' => $logs->where('status', 'rate_limited')->count(),
                'errors' => $logs->where('status', 'error')->count(),
                'total_tokens' => $logs->sum('total_tokens'),
                'avg_response_time' => round($logs->avg('response_time_ms') ?? 0),
            ],
        ]);
    }

    /**
     * Test a Groq API key live by sending a minimal request
     */
    private function testGroqKeyLive(int $keyIndex)
    {
        $apiKey = config("services.groq.api_key_{$keyIndex}");

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'status' => 'not_configured',
                'key_index' => $keyIndex,
                'response_time_ms' => 0,
            ]);
        }

        $start = microtime(true);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [['role' => 'user', 'content' => 'ping']],
                'max_tokens' => 5,
            ]);

            $elapsed = round((microtime(true) - $start) * 1000);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'status' => 'active',
                    'key_index' => $keyIndex,
                    'response_time_ms' => $elapsed,
                    'remaining' => [
                        'requests' => $response->header('x-ratelimit-remaining-requests'),
                        'tokens' => $response->header('x-ratelimit-remaining-tokens'),
                    ],
                    'limits' => [
                        'requests' => $response->header('x-ratelimit-limit-requests'),
                        'tokens' => $response->header('x-ratelimit-limit-tokens'),
                    ],
                ]);
            }

            if ($response->status() === 429) {
                return response()->json([
                    'success' => true,
                    'status' => 'rate_limited',
                    'key_index' => $keyIndex,
                    'response_time_ms' => $elapsed,
                    'remaining' => ['requests' => '0', 'tokens' => '0'],
                    'limits' => [
                        'requests' => $response->header('x-ratelimit-limit-requests'),
                        'tokens' => $response->header('x-ratelimit-limit-tokens'),
                    ],
                ]);
            }

            return response()->json([
                'success' => true,
                'status' => 'error',
                'key_index' => $keyIndex,
                'response_time_ms' => $elapsed,
                'error' => $response->status() . ': ' . substr($response->body(), 0, 200),
            ]);

        } catch (\Exception $e) {
            $elapsed = round((microtime(true) - $start) * 1000);
            return response()->json([
                'success' => true,
                'status' => 'unreachable',
                'key_index' => $keyIndex,
                'response_time_ms' => $elapsed,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
