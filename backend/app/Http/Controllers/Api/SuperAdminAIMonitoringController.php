<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SuperAdminAIMonitoringController extends Controller
{
    /**
     * Dashboard global de monitoreo de IA para super admin
     * Datos ricos: resumen, por-key, por-hora, por-tenant, salud de keys
     */
    public function dashboard(Request $request)
    {
        $period = $request->get('period', '24h');

        $tenantDatabases = $this->getTenantDatabases();
        $dateFilter = $this->getDateFilter($period);

        $summary = [
            'total_requests' => 0,
            'successful' => 0,
            'rate_limited' => 0,
            'errors' => 0,
            'total_tokens' => 0,
            'total_input_tokens' => 0,
            'total_output_tokens' => 0,
            'avg_response_time_ms' => 0,
            'total_cost_usd' => 0,
            'total_cost_cop' => 0,
            'chat_requests' => 0,
            'voice_requests' => 0,
            'voice_minutes' => 0,
            'success_rate' => 0,
        ];

        $allRequests = [];
        $keyStats = [];
        $tenantStats = [];
        $hourlyData = [];
        $dailyData = [];
        $modelStats = [];
        $totalResponseTime = 0;
        $responseTimeCount = 0;

        foreach ($tenantDatabases as $dbName) {
            try {
                $tableExists = DB::select(
                    "SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_schema = ? AND table_name = 'ai_usage_logs'",
                    [$dbName]
                );
                if ($tableExists[0]->cnt == 0) continue;

                // Global stats
                $stats = DB::select("
                    SELECT
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as successful,
                        SUM(CASE WHEN status = 'rate_limited' THEN 1 ELSE 0 END) as rate_limited,
                        SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as errors,
                        COALESCE(SUM(total_tokens), 0) as tokens,
                        COALESCE(SUM(prompt_tokens), 0) as input_tokens,
                        COALESCE(SUM(completion_tokens), 0) as output_tokens,
                        COALESCE(AVG(CASE WHEN response_time_ms > 0 THEN response_time_ms END), 0) as avg_time,
                        COALESCE(SUM(cost_usd), 0) as cost_usd,
                        SUM(CASE WHEN request_type = 'voice' THEN 1 ELSE 0 END) as voice_count,
                        COALESCE(SUM(voice_duration_seconds), 0) as voice_seconds
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
                $summary['total_cost_usd'] += floatval($stats->cost_usd);
                $summary['voice_requests'] += $stats->voice_count;
                $summary['voice_minutes'] += round($stats->voice_seconds / 60, 2);
                $summary['chat_requests'] += ($stats->total - $stats->voice_count);

                if ($stats->avg_time > 0 && $stats->total > 0) {
                    $totalResponseTime += ($stats->avg_time * $stats->total);
                    $responseTimeCount += $stats->total;
                }

                // Per-key stats
                $keys = DB::select("
                    SELECT
                        COALESCE(api_key_index, 0) as key_index,
                        COALESCE(api_key_last_4, '????') as key_last4,
                        COUNT(*) as total_requests,
                        SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as successful,
                        SUM(CASE WHEN status = 'rate_limited' THEN 1 ELSE 0 END) as rate_limited,
                        SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as errors,
                        COALESCE(SUM(total_tokens), 0) as tokens,
                        COALESCE(AVG(CASE WHEN response_time_ms > 0 THEN response_time_ms END), 0) as avg_time,
                        COALESCE(SUM(cost_usd), 0) as cost_usd,
                        MAX(created_at) as last_used
                    FROM {$dbName}.ai_usage_logs
                    {$dateFilter}
                    GROUP BY api_key_index, api_key_last_4
                ");

                foreach ($keys as $k) {
                    $idx = (int) $k->key_index;
                    if (!isset($keyStats[$idx])) {
                        $keyStats[$idx] = [
                            'key_index' => $idx,
                            'key_last_4' => $k->key_last4,
                            'total_requests' => 0,
                            'successful' => 0,
                            'rate_limited' => 0,
                            'errors' => 0,
                            'total_tokens' => 0,
                            'cost_usd' => 0,
                            'avg_response_time' => 0,
                            'last_used' => null,
                            '_time_sum' => 0,
                            '_time_count' => 0,
                        ];
                    }
                    $keyStats[$idx]['total_requests'] += $k->total_requests;
                    $keyStats[$idx]['successful'] += $k->successful;
                    $keyStats[$idx]['rate_limited'] += $k->rate_limited;
                    $keyStats[$idx]['errors'] += $k->errors;
                    $keyStats[$idx]['total_tokens'] += $k->tokens;
                    $keyStats[$idx]['cost_usd'] += floatval($k->cost_usd);
                    $keyStats[$idx]['key_last_4'] = $k->key_last4;
                    if ($k->avg_time > 0) {
                        $keyStats[$idx]['_time_sum'] += ($k->avg_time * $k->total_requests);
                        $keyStats[$idx]['_time_count'] += $k->total_requests;
                    }
                    if (!$keyStats[$idx]['last_used'] || $k->last_used > $keyStats[$idx]['last_used']) {
                        $keyStats[$idx]['last_used'] = $k->last_used;
                    }
                }

                // Per-tenant aggregate
                $tenantId = str_replace('tenant', '', $dbName);
                $tenantStats[$tenantId] = [
                    'tenant_id' => $tenantId,
                    'total_requests' => (int) $stats->total,
                    'successful' => (int) $stats->successful,
                    'tokens' => (int) $stats->tokens,
                    'cost_usd' => round(floatval($stats->cost_usd), 6),
                    'voice_minutes' => round($stats->voice_seconds / 60, 2),
                    'avg_time' => round(floatval($stats->avg_time)),
                ];

                // Hourly usage (last 48h)
                $hourly = DB::select("
                    SELECT
                        DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') as hour,
                        COUNT(*) as requests,
                        COALESCE(SUM(total_tokens), 0) as tokens,
                        COALESCE(SUM(cost_usd), 0) as cost
                    FROM {$dbName}.ai_usage_logs
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 48 HOUR)
                    GROUP BY DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00')
                ");
                foreach ($hourly as $h) {
                    if (!isset($hourlyData[$h->hour])) {
                        $hourlyData[$h->hour] = ['hour' => $h->hour, 'requests' => 0, 'tokens' => 0, 'cost' => 0];
                    }
                    $hourlyData[$h->hour]['requests'] += $h->requests;
                    $hourlyData[$h->hour]['tokens'] += $h->tokens;
                    $hourlyData[$h->hour]['cost'] += floatval($h->cost);
                }

                // Daily usage (last 30 days)
                $daily = DB::select("
                    SELECT
                        DATE(created_at) as day,
                        COUNT(*) as requests,
                        COALESCE(SUM(total_tokens), 0) as tokens,
                        COALESCE(SUM(cost_usd), 0) as cost,
                        SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as successful,
                        SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as errors
                    FROM {$dbName}.ai_usage_logs
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    GROUP BY DATE(created_at)
                ");
                foreach ($daily as $d) {
                    $day = $d->day;
                    if (!isset($dailyData[$day])) {
                        $dailyData[$day] = ['day' => $day, 'requests' => 0, 'tokens' => 0, 'cost' => 0, 'successful' => 0, 'errors' => 0];
                    }
                    $dailyData[$day]['requests'] += $d->requests;
                    $dailyData[$day]['tokens'] += $d->tokens;
                    $dailyData[$day]['cost'] += floatval($d->cost);
                    $dailyData[$day]['successful'] += $d->successful;
                    $dailyData[$day]['errors'] += $d->errors;
                }

                // Model stats
                $models = DB::select("
                    SELECT
                        COALESCE(model, 'unknown') as model_name,
                        COALESCE(provider, 'unknown') as provider,
                        COUNT(*) as total,
                        COALESCE(SUM(total_tokens), 0) as tokens,
                        COALESCE(SUM(cost_usd), 0) as cost
                    FROM {$dbName}.ai_usage_logs
                    {$dateFilter}
                    GROUP BY model, provider
                ");
                foreach ($models as $m) {
                    $key = $m->provider . '/' . $m->model_name;
                    if (!isset($modelStats[$key])) {
                        $modelStats[$key] = ['model' => $m->model_name, 'provider' => $m->provider, 'requests' => 0, 'tokens' => 0, 'cost' => 0];
                    }
                    $modelStats[$key]['requests'] += $m->total;
                    $modelStats[$key]['tokens'] += $m->tokens;
                    $modelStats[$key]['cost'] += floatval($m->cost);
                }

                // Recent requests
                $requests = DB::select("
                    SELECT user_message, total_tokens, prompt_tokens, completion_tokens, status, created_at,
                           request_type, cost_usd, voice_duration_seconds, response_time_ms,
                           api_key_index, model, provider
                    FROM {$dbName}.ai_usage_logs
                    {$dateFilter}
                    ORDER BY created_at DESC
                    LIMIT 20
                ");
                foreach ($requests as $req) {
                    $allRequests[] = [
                        'tenant' => $tenantId,
                        'message' => mb_substr($req->user_message ?? '', 0, 120),
                        'tokens' => $req->total_tokens ?? 0,
                        'input_tokens' => $req->prompt_tokens ?? 0,
                        'output_tokens' => $req->completion_tokens ?? 0,
                        'status' => $req->status,
                        'type' => $req->request_type ?? 'chat',
                        'cost_usd' => floatval($req->cost_usd ?? 0),
                        'voice_seconds' => $req->voice_duration_seconds ?? 0,
                        'response_time_ms' => $req->response_time_ms ?? 0,
                        'key_index' => $req->api_key_index ?? null,
                        'model' => $req->model ?? 'unknown',
                        'provider' => $req->provider ?? 'unknown',
                        'created_at' => $req->created_at,
                    ];
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Finalize summary
        if ($summary['total_requests'] > 0) {
            $summary['success_rate'] = round(($summary['successful'] / $summary['total_requests']) * 100, 1);
        }
        if ($responseTimeCount > 0) {
            $summary['avg_response_time_ms'] = round($totalResponseTime / $responseTimeCount);
        }
        $summary['total_cost_usd'] = round($summary['total_cost_usd'], 6);
        $summary['total_cost_cop'] = round($summary['total_cost_usd'] * 4200, 0);

        // Finalize key stats
        $keysStatusList = [];
        foreach ($keyStats as &$ks) {
            if ($ks['_time_count'] > 0) {
                $ks['avg_response_time'] = round($ks['_time_sum'] / $ks['_time_count']);
            }
            $ks['cost_usd'] = round($ks['cost_usd'], 6);
            $ks['status'] = $ks['errors'] > ($ks['total_requests'] * 0.5) ? 'degraded' : 'active';
            unset($ks['_time_sum'], $ks['_time_count']);
            $keysStatusList[] = $ks;
        }

        // Add keys from config that have no usage yet
        $configuredKeys = $this->getConfiguredGroqKeys();
        foreach ($configuredKeys as $idx => $last4) {
            $found = false;
            foreach ($keysStatusList as &$ks) {
                if ($ks['key_index'] === $idx) {
                    $found = true;
                    $ks['key_last_4'] = $last4;
                    break;
                }
            }
            if (!$found) {
                $keysStatusList[] = [
                    'key_index' => $idx,
                    'key_last_4' => $last4,
                    'total_requests' => 0,
                    'successful' => 0,
                    'rate_limited' => 0,
                    'errors' => 0,
                    'total_tokens' => 0,
                    'cost_usd' => 0,
                    'avg_response_time' => 0,
                    'last_used' => null,
                    'status' => 'idle',
                ];
            }
        }
        usort($keysStatusList, fn($a, $b) => $a['key_index'] - $b['key_index']);

        ksort($hourlyData);
        ksort($dailyData);
        usort($allRequests, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));
        usort($tenantStats, fn($a, $b) => $b['total_requests'] - $a['total_requests']);

        // Cost projection (monthly)
        $daysInPeriod = match($period) {
            '24h' => 1, '7d' => 7, '30d' => 30,
            default => max(1, count($dailyData)),
        };
        $dailyAvgCost = $daysInPeriod > 0 ? $summary['total_cost_usd'] / $daysInPeriod : 0;
        $monthlyProjection = round($dailyAvgCost * 30, 4);

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'keys_status' => $keysStatusList,
            'keys_total' => count($configuredKeys),
            'usage_by_hour' => array_values($hourlyData),
            'usage_by_day' => array_values($dailyData),
            'tenant_breakdown' => array_values($tenantStats),
            'model_breakdown' => array_values($modelStats),
            'recent_requests' => array_slice($allRequests, 0, 50),
            'tenants_analyzed' => count($tenantDatabases),
            'cost_projection_monthly_usd' => $monthlyProjection,
            'cost_projection_monthly_cop' => round($monthlyProjection * 4200, 0),
        ]);
    }

    /**
     * Test a specific Groq key health
     */
    public function testKey(Request $request, $keyIndex)
    {
        $keyIndex = (int) $keyIndex;
        $key = config("services.groq.api_key_{$keyIndex}");

        if (!$key) {
            return response()->json(['success' => false, 'message' => 'Key not configured']);
        }

        try {
            $start = microtime(true);
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => 'Bearer ' . $key,
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [['role' => 'user', 'content' => 'ping']],
                'max_tokens' => 5,
            ]);
            $elapsed = round((microtime(true) - $start) * 1000);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'status' => 'active',
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
            } elseif ($response->status() === 429) {
                return response()->json([
                    'success' => true,
                    'status' => 'rate_limited',
                    'response_time_ms' => $elapsed,
                    'retry_after' => $response->header('retry-after'),
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'status' => 'error',
                    'response_time_ms' => $elapsed,
                    'error' => $response->json('error.message') ?? 'Unknown error',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'status' => 'unreachable',
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function getTenantDatabases(): array
    {
        $databases = DB::select('SHOW DATABASES');
        $tenantDbs = [];
        foreach ($databases as $db) {
            $name = $db->Database;
            if (str_starts_with($name, 'tenant') && $name !== 'tenants') {
                $tenantDbs[] = $name;
            }
        }
        return $tenantDbs;
    }

    private function getConfiguredGroqKeys(): array
    {
        $keys = [];
        for ($i = 1; $i <= 20; $i++) {
            $key = config("services.groq.api_key_{$i}");
            if ($key) {
                $keys[$i] = substr($key, -4);
            }
        }
        return $keys;
    }

    private function getDateFilter(string $period): string
    {
        return match ($period) {
            '24h' => "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)",
            '7d' => "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
            '30d' => "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
            default => "",
        };
    }
}
