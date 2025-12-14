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

        // Lista de bases de datos de tenants
        $tenantDatabases = $this->getTenantDatabases();

        $summary = [
            'total_requests' => 0,
            'successful_requests' => 0,
            'failed_requests' => 0,
            'total_tokens' => 0,
            'prompt_tokens' => 0,
            'completion_tokens' => 0,
            'avg_response_time' => 0,
            'total_cost_usd' => 0
        ];

        $allRequests = [];
        $usageByTenant = [];

        foreach ($tenantDatabases as $dbName) {
            try {
                $data = $this->getTenantData($dbName, $period);

                // Acumular totales
                $summary['total_requests'] += $data['total_requests'];
                $summary['successful_requests'] += $data['successful_requests'];
                $summary['failed_requests'] += $data['failed_requests'];
                $summary['total_tokens'] += $data['total_tokens'];
                $summary['prompt_tokens'] += $data['prompt_tokens'];
                $summary['completion_tokens'] += $data['completion_tokens'];
                $summary['total_cost_usd'] += $data['cost_usd'];

                // Agregar requests
                $allRequests = array_merge($allRequests, $data['recent_requests']);

                // Stats por tenant
                if ($data['total_requests'] > 0) {
                    $usageByTenant[] = [
                        'tenant' => $dbName,
                        'requests' => $data['total_requests'],
                        'tokens' => $data['total_tokens'],
                        'cost_usd' => $data['cost_usd'],
                        'success_rate' => round(($data['successful_requests'] / $data['total_requests']) * 100, 1)
                    ];
                }
            } catch (\Exception $e) {
                // Ignorar tenants sin tabla o errores
                continue;
            }
        }

        // Calcular promedio de tiempo de respuesta
        if ($summary['total_requests'] > 0) {
            $totalResponseTime = 0;
            foreach ($allRequests as $req) {
                $totalResponseTime += $req['response_time_ms'] ?? 0;
            }
            $summary['avg_response_time'] = round($totalResponseTime / $summary['total_requests']);
        }

        // Ordenar por uso (mayor a menor)
        usort($usageByTenant, function($a, $b) {
            return $b['tokens'] - $a['tokens'];
        });

        // Ordenar requests recientes por fecha
        usort($allRequests, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'usage_by_tenant' => array_slice($usageByTenant, 0, 10), // Top 10 tenants
            'recent_requests' => array_slice($allRequests, 0, 50), // Últimas 50
            'period' => $period,
            'tenants_analyzed' => count($usageByTenant)
        ]);
    }

    /**
     * Obtiene lista de bases de datos de tenants
     */
    private function getTenantDatabases()
    {
        $databases = DB::select('SHOW DATABASES');
        $tenantDatabases = [];

        foreach ($databases as $db) {
            $dbName = $db->Database;

            // Filtrar solo bases de datos de tenants (ejemplo: tenantX_)
            if (strpos($dbName, 'tenant') === 0 && $dbName !== 'tenants') {
                $tenantDatabases[] = $dbName;
            }
        }

        return $tenantDatabases;
    }

    /**
     * Obtiene datos de IA de un tenant específico
     */
    private function getTenantData($dbName, $period)
    {
        // Determinar filtro de fecha
        $dateFilter = '';
        switch ($period) {
            case '24h':
                $dateFilter = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
                break;
            case '7d':
                $dateFilter = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case '30d':
                $dateFilter = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                break;
            default:
                $dateFilter = "";
        }

        // Verificar si existe la tabla
        $tableExists = DB::select("SELECT COUNT(*) as count FROM information_schema.tables
            WHERE table_schema = ? AND table_name = 'ai_usage_logs'", [$dbName]);

        if ($tableExists[0]->count == 0) {
            return [
                'total_requests' => 0,
                'successful_requests' => 0,
                'failed_requests' => 0,
                'total_tokens' => 0,
                'prompt_tokens' => 0,
                'completion_tokens' => 0,
                'cost_usd' => 0,
                'recent_requests' => []
            ];
        }

        // Query para totales
        $totals = DB::select("
            SELECT
                COUNT(*) as total_requests,
                SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as successful_requests,
                SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as failed_requests,
                COALESCE(SUM(total_tokens), 0) as total_tokens,
                COALESCE(SUM(prompt_tokens), 0) as prompt_tokens,
                COALESCE(SUM(completion_tokens), 0) as completion_tokens
            FROM {$dbName}.ai_usage_logs
            {$dateFilter}
        ");

        $total = $totals[0];

        // Calcular costo estimado (ejemplo: $0.001 por 1000 tokens)
        $costUsd = ($total->total_tokens / 1000) * 0.001;

        // Requests recientes
        $recentRequests = DB::select("
            SELECT
                id,
                user_id,
                api_key_index,
                api_key_last_4,
                LEFT(user_message, 100) as user_message,
                prompt_tokens,
                completion_tokens,
                total_tokens,
                status,
                response_time_ms,
                model,
                created_at
            FROM {$dbName}.ai_usage_logs
            {$dateFilter}
            ORDER BY created_at DESC
            LIMIT 20
        ");

        // Convertir a array
        $requests = array_map(function($req) use ($dbName) {
            return [
                'tenant' => $dbName,
                'id' => $req->id,
                'user_id' => $req->user_id,
                'api_key_index' => $req->api_key_index,
                'api_key_last_4' => $req->api_key_last_4,
                'user_message' => $req->user_message,
                'prompt_tokens' => $req->prompt_tokens,
                'completion_tokens' => $req->completion_tokens,
                'total_tokens' => $req->total_tokens,
                'status' => $req->status,
                'response_time_ms' => $req->response_time_ms,
                'model' => $req->model,
                'created_at' => $req->created_at
            ];
        }, $recentRequests);

        return [
            'total_requests' => $total->total_requests,
            'successful_requests' => $total->successful_requests,
            'failed_requests' => $total->failed_requests,
            'total_tokens' => $total->total_tokens,
            'prompt_tokens' => $total->prompt_tokens,
            'completion_tokens' => $total->completion_tokens,
            'cost_usd' => round($costUsd, 4),
            'recent_requests' => $requests
        ];
    }
}
