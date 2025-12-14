<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\AiUsageService;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard principal del Super Admin
     */
    public function index()
    {
        return view('central.dashboard');
    }

    /**
     * API: Obtiene las métricas principales (KPIs)
     */
    public function getKpis()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Total de clientes activos
        $totalActiveClients = Tenant::count();

        // Clientes creados hoy
        $clientsCreatedToday = Tenant::whereDate('created_at', $today)->count();

        // MRR (Monthly Recurring Revenue) - Ejemplo simple
        // Asumiendo que cada tenant paga $29/mes
        $mrr = $totalActiveClients * 29;

        // Total de tokens IA consumidos este mes
        $aiTokensThisMonth = DB::connection('mysql')
            ->table('ai_usage_logs')
            ->where('created_at', '>=', $startOfMonth)
            ->sum('tokens_used');

        // Costo estimado de IA este mes
        $aiCostThisMonth = DB::connection('mysql')
            ->table('ai_usage_logs')
            ->where('created_at', '>=', $startOfMonth)
            ->sum('estimated_cost');

        return response()->json([
            'success' => true,
            'data' => [
                'total_active_clients' => $totalActiveClients,
                'clients_created_today' => $clientsCreatedToday,
                'mrr' => $mrr,
                'ai_tokens_this_month' => (int) $aiTokensThisMonth,
                'ai_cost_this_month' => (float) $aiCostThisMonth,
            ]
        ]);
    }

    /**
     * API: Lista todos los tenants con sus dominios
     */
    public function getTenants(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search', '');

        $query = Tenant::with('domains')
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where('id', 'like', "%{$search}%");
        }

        $tenants = $query->paginate($perPage);

        // Enriquecer con datos adicionales
        $tenants->getCollection()->transform(function ($tenant) {
            return [
                'id' => $tenant->id,
                'business_name' => $tenant->business_name ?? 'Sin Nombre',
                'plan' => $tenant->plan ?? 'free',
                'domains' => $tenant->domains->pluck('domain')->toArray(),
                'primary_domain' => $tenant->domains->first()?->domain ?? 'N/A',
                'status' => $this->getTenantStatus($tenant),
                'created_at' => $tenant->created_at,
                'subscription_ends_at' => $tenant->subscription_ends_at,
                'database_name' => $tenant->tenancy_db_name ?? "tenant_{$tenant->id}",
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $tenants
        ]);
    }

    /**
     * API: Monitor de consumo de IA por tenant
     */
    public function getAiUsage(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month
        $startDate = match($period) {
            'day' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            default => Carbon::now()->startOfMonth(),
        };

        $aiUsage = DB::connection('mysql')
            ->table('ai_usage_logs')
            ->select(
                'tenant_id',
                DB::raw('COUNT(*) as total_requests'),
                DB::raw('SUM(tokens_used) as total_tokens'),
                DB::raw('SUM(estimated_cost) as total_cost'),
                DB::raw('AVG(tokens_used) as avg_tokens_per_request')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('tenant_id')
            ->orderBy('total_cost', 'desc')
            ->get();

        // Calcular promedio general
        $avgTokens = $aiUsage->avg('total_tokens') ?? 0;
        $stdDeviation = $this->calculateStdDeviation($aiUsage->pluck('total_tokens')->toArray());

        // Enriquecer con datos del tenant y detectar anomalías
        $enrichedUsage = $aiUsage->map(function ($usage) use ($avgTokens, $stdDeviation) {
            $tenant = Tenant::find($usage->tenant_id);

            // Detectar uso anormalmente alto (> 2 desviaciones estándar)
            $isAnomalous = ($usage->total_tokens > ($avgTokens + (2 * $stdDeviation)));

            return [
                'tenant_id' => $usage->tenant_id,
                'business_name' => $tenant?->data['business_name'] ?? 'Desconocido',
                'primary_domain' => $tenant?->domains->first()?->domain ?? 'N/A',
                'total_requests' => $usage->total_requests,
                'total_tokens' => (int) $usage->total_tokens,
                'total_cost' => round($usage->total_cost, 4),
                'avg_tokens_per_request' => round($usage->avg_tokens_per_request, 0),
                'is_anomalous' => $isAnomalous,
                'alert_level' => $this->getAlertLevel($usage->total_cost),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $enrichedUsage,
            'stats' => [
                'total_tenants_using_ai' => $aiUsage->count(),
                'avg_tokens_per_tenant' => round($avgTokens, 0),
                'total_cost_period' => round($aiUsage->sum('total_cost'), 2),
            ]
        ]);
    }

    /**
     * API: Obtiene detalles de un tenant específico
     */
    public function getTenantDetails($tenantId)
    {
        $tenant = Tenant::with('domains')->find($tenantId);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant no encontrado'
            ], 404);
        }

        // Conectar a la base de datos del tenant para obtener estadísticas
        $stats = null;
        try {
            $tenant->run(function () use (&$stats) {
                $stats = [
                    'total_sales' => DB::table('sales')->count(),
                    'total_products' => DB::table('products')->count(),
                    'total_customers' => DB::table('customers')->count(),
                    'total_users' => DB::table('users')->count(),
                    'total_revenue' => DB::table('sales')->sum('total'),
                    'sales_today' => DB::table('sales')->whereDate('created_at', Carbon::today())->count(),
                    'revenue_today' => DB::table('sales')->whereDate('created_at', Carbon::today())->sum('total'),
                ];
            });
        } catch (\Exception $e) {
            $stats = ['error' => 'No se pudo conectar a la base de datos del tenant'];
        }

        // Obtener estadísticas de uso de IA con límites
        $aiUsageService = app(AiUsageService::class);
        $aiUsageStats = $aiUsageService->getUsageStats($tenantId);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $tenant->id,
                'business_name' => $tenant->business_name ?? 'Sin Nombre',
                'plan' => $tenant->plan ?? 'free_trial',
                'status' => $tenant->data['status'] ?? 'active',
                'domains' => $tenant->domains->pluck('domain')->toArray(),
                'primary_domain' => $tenant->domains->first()?->domain ?? 'N/A',
                'created_at' => $tenant->created_at,
                'subscription_ends_at' => $tenant->subscription_ends_at,
                'stats' => $stats,
                'ai_usage' => $aiUsageStats,
            ]
        ]);
    }

    /**
     * API: Crear nuevo tenant
     */
    public function createTenant(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|string|unique:tenants,id|max:50|regex:/^[a-z0-9-]+$/',
            'business_name' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'plan' => 'nullable|string|in:free_trial,basic,premium,enterprise',
        ]);

        try {
            // Reemplazar guiones por guiones bajos en el ID para evitar problemas con nombres de BD
            $tenantId = str_replace('-', '_', $validated['tenant_id']);

            // Crear tenant
            $tenant = Tenant::create([
                'id' => $tenantId,
                'business_name' => $validated['business_name'],
                'plan' => $validated['plan'] ?? 'free_trial',
                'data' => [
                    'status' => 'active',
                ]
            ]);

            // Crear dominio
            $tenant->domains()->create([
                'domain' => $validated['domain']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tienda creada exitosamente',
                'data' => [
                    'id' => $tenant->id,
                    'business_name' => $tenant->business_name,
                    'domain' => $validated['domain'],
                    'plan' => $tenant->plan,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la tienda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Actualizar tenant (cambiar plan, pausar, etc.)
     */
    public function updateTenant(Request $request, $id)
    {
        $tenant = Tenant::find($id);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant no encontrado'
            ], 404);
        }

        $validated = $request->validate([
            'business_name' => 'nullable|string|max:255',
            'plan' => 'nullable|string|in:free_trial,basic,premium,enterprise',
            'status' => 'nullable|string|in:active,paused,suspended',
        ]);

        try {
            // Actualizar business_name si se proporciona
            if (isset($validated['business_name'])) {
                $data = $tenant->data;
                $data['business_name'] = $validated['business_name'];
                $tenant->data = $data;
            }

            // Actualizar plan
            if (isset($validated['plan'])) {
                $tenant->plan = $validated['plan'];
            }

            // Actualizar estado
            if (isset($validated['status'])) {
                $data = $tenant->data;
                $data['status'] = $validated['status'];
                $tenant->data = $data;
            }

            $tenant->save();

            return response()->json([
                'success' => true,
                'message' => 'Tienda actualizada exitosamente',
                'data' => [
                    'id' => $tenant->id,
                    'business_name' => $tenant->data['business_name'] ?? 'Sin Nombre',
                    'plan' => $tenant->plan,
                    'status' => $tenant->data['status'] ?? 'active',
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la tienda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Eliminar tenant
     */
    public function deleteTenant($id)
    {
        $tenant = Tenant::find($id);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant no encontrado'
            ], 404);
        }

        try {
            // Eliminar dominios asociados
            $tenant->domains()->delete();

            // Eliminar tenant
            $tenant->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tienda eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la tienda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtiene los usuarios de un tenant específico
     */
    public function getTenantUsers($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);

            // Conectar a la base de datos del tenant
            $tenantDbName = 'tenant' . $tenant->id;

            // Obtener usuarios del tenant con sus roles
            $users = DB::connection('mysql')
                ->table($tenantDbName . '.users')
                ->leftJoin($tenantDbName . '.roles', $tenantDbName . '.users.role_id', '=', $tenantDbName . '.roles.id')
                ->select(
                    $tenantDbName . '.users.id',
                    $tenantDbName . '.users.name',
                    $tenantDbName . '.users.email',
                    $tenantDbName . '.users.active',
                    $tenantDbName . '.users.created_at',
                    $tenantDbName . '.roles.name as role'
                )
                ->orderBy($tenantDbName . '.users.id', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar usuarios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Resetea la contraseña de un usuario de un tenant
     */
    public function resetUserPassword($tenantId, $userId, Request $request)
    {
        try {
            $tenant = Tenant::findOrFail($tenantId);
            $newPassword = $request->input('password');

            if (!$newPassword) {
                return response()->json([
                    'success' => false,
                    'message' => 'La contraseña es requerida'
                ], 400);
            }

            // Conectar a la base de datos del tenant
            $tenantDbName = 'tenant' . $tenant->id;

            // Actualizar la contraseña del usuario
            DB::connection('mysql')
                ->table($tenantDbName . '.users')
                ->where('id', $userId)
                ->update([
                    'password' => bcrypt($newPassword)
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al resetear contraseña: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtiene los productos de un tenant específico
     */
    public function getTenantProducts($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);

            // Conectar a la base de datos del tenant
            $tenantDbName = 'tenant' . $tenant->id;

            // Obtener productos del tenant
            $products = DB::connection('mysql')
                ->table($tenantDbName . '.products')
                ->select('id', 'name', 'price', 'stock', 'created_at')
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $products
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar productos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Genera un link de registro con plan preseleccionado
     */
    public function generateSignupLink(Request $request)
    {
        $validated = $request->validate([
            'plan' => 'required|string|in:free_trial,basic,premium,enterprise',
        ]);

        try {
            // Generar token único
            $token = bin2hex(random_bytes(32));

            // Guardar en base de datos con expiración de 7 días
            DB::connection('mysql')->table('signup_tokens')->insert([
                'token' => $token,
                'plan' => $validated['plan'],
                'expires_at' => Carbon::now()->addDays(7),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Generar URL
            $baseUrl = config('app.url'); // En producción será tu dominio VPS
            $signupUrl = "{$baseUrl}/register?token={$token}";

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $token,
                    'plan' => $validated['plan'],
                    'url' => $signupUrl,
                    'expires_at' => Carbon::now()->addDays(7)->toDateTimeString(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar link: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helpers privados
     */
    private function getTenantStatus($tenant)
    {
        if ($tenant->subscription_ends_at && Carbon::parse($tenant->subscription_ends_at)->isPast()) {
            return 'expired';
        }
        return 'active';
    }

    private function calculateStdDeviation($values)
    {
        if (empty($values)) return 0;

        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(function($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $values)) / count($values);

        return sqrt($variance);
    }

    private function getAlertLevel($cost)
    {
        if ($cost > 10) return 'critical';      // Más de $10/mes
        if ($cost > 5) return 'warning';        // Más de $5/mes
        if ($cost > 1) return 'moderate';       // Más de $1/mes
        return 'normal';
    }
}
