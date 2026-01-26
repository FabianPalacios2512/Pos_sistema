<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuperAdminAIMonitoringController extends Controller
{
    /**
     * Dashboard global de monitoreo de IA para super admin
     * Muestra datos de TODOS los tenants
     */
    public function dashboard(Request $request)
    {
        $period = $request->get('period', '24h'); // 24h, 7d, 30d, all

        // Obtener todas las bases de datos de tenants
        $databases = DB::select('SHOW DATABASES');
        $tenantDatabases = [];

        foreach ($databases as $db) {
            $dbName = $db->Database;
            if (strpos($dbName, 'tenant') === 0 && $dbName !== 'tenants') {
                $tenantDatabases[] = $dbName;
            }
        }

        $summary = [
            'total_requests' => 0,
            'successful' => 0,
            'rate_limited' => 0,
            'errors' => 0,
            'total_tokens' => 0,
            'avg_response_time_ms' => 0,
            'total_cost_usd' => 0,
            'total_cost_cop' => 0,
            'chat_requests' => 0,
            'voice_requests' => 0,
            'voice_minutes' => 0,
        ];

        $allRequests = [];
        $totalResponseTime = 0;
        $responseTimeCount = 0;

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
                        COALESCE(AVG(response_time_ms), 0) as avg_time,
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
                $summary['total_cost_usd'] += floatval($stats->cost_usd);
                $summary['voice_requests'] += $stats->voice_count;
                $summary['voice_minutes'] += round($stats->voice_seconds / 60, 2);
                $summary['chat_requests'] += ($stats->total - $stats->voice_count);
                
                // Acumular para promedio de tiempo de respuesta
                if ($stats->avg_time > 0 && $stats->total > 0) {
                    $totalResponseTime += ($stats->avg_time * $stats->total);
                    $responseTimeCount += $stats->total;
                }

                $requests = DB::select("
                    SELECT user_message, total_tokens, status, created_at, request_type, cost_usd, voice_duration_seconds
                    FROM {$dbName}.ai_usage_logs
                    {$dateFilter}
                    ORDER BY created_at DESC
                    LIMIT 10
                ");

                foreach ($requests as $req) {
                    $allRequests[] = [
                        'tenant' => $dbName,
                        'message' => substr($req->user_message ?? '', 0, 100),
                        'tokens' => $req->total_tokens ?? 0,
                        'status' => $req->status,
                        'type' => $req->request_type ?? 'chat',
                        'cost_usd' => $req->cost_usd ?? 0,
                        'voice_seconds' => $req->voice_duration_seconds ?? 0,
                        'created_at' => $req->created_at
                    ];
                }
            } catch (\Exception $e) {
                \Log::error("Error procesando tenant {$dbName}: " . $e->getMessage());
                continue;
            }
        }

        if ($summary['total_requests'] > 0) {
            $summary['success_rate'] = round(($summary['successful'] / $summary['total_requests']) * 100, 2);
        }
        
        // Calcular promedio de tiempo de respuesta ponderado
        if ($responseTimeCount > 0) {
            $summary['avg_response_time_ms'] = round($totalResponseTime / $responseTimeCount);
        }
        
        // Calcular costo en COP
        $summary['total_cost_usd'] = round($summary['total_cost_usd'], 6);
        $summary['total_cost_cop'] = round($summary['total_cost_usd'] * 4200, 2);

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
}
