<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

/**
 * 👑 Controlador Super Admin (God Mode)
 *
 * Gestión centralizada de todos los tenants del sistema
 */
class SuperAdminController extends Controller
{
    /**
     * 📊 Obtener KPIs generales del sistema
     *
     * GET /admin/api/kpis
     */
    public function getKPIs()
    {
        try {
            $totalTenants = Tenant::count();

            // Tenants activos = aquellos cuya suscripción no ha expirado
            $activeTenants = Tenant::where('subscription_ends_at', '>', now())
                ->orWhereNull('subscription_ends_at')
                ->count();

            // MRR (Monthly Recurring Revenue) - Calcular basado en planes
            $mrr = 0;
            $tenants = Tenant::all();
            foreach ($tenants as $tenant) {
                // Solo contar si la suscripción está activa
                if (!$tenant->subscription_ends_at || $tenant->subscription_ends_at > now()) {
                    $planPrices = [
                        'free' => 0,
                        'basic' => 35000,
                        'premium' => 75000,
                        'enterprise' => 150000
                    ];
                    $mrr += $planPrices[$tenant->plan] ?? 0;
                }
            }

            // Nuevos tenants hoy
            $newToday = Tenant::whereDate('created_at', today())->count();

            // Error logs totals
            $totalErrors = 0;
            try {
                $totalErrors = DB::connection('mysql')->table('tenant_error_logs')
                    ->where('resolved', false)
                    ->count();
            } catch (\Throwable $e) {}

            // Login stats today
            $loginsToday = 0;
            $failsToday = 0;
            try {
                $loginsToday = DB::connection('mysql')->table('login_attempts')
                    ->where('result', 'success')
                    ->where('attempted_at', '>=', today())
                    ->count();
                $failsToday = DB::connection('mysql')->table('login_attempts')
                    ->where('result', 'fail')
                    ->where('attempted_at', '>=', today())
                    ->count();
            } catch (\Throwable $e) {}

            return response()->json([
                'success' => true,
                'data' => [
                    'active_clients' => $activeTenants,
                    'total_clients' => $totalTenants,
                    'mrr' => $mrr,
                    'new_today' => $newToday,
                    'ai_tokens_month' => 0,
                    'total_errors' => $totalErrors,
                    'logins_today' => $loginsToday,
                    'login_fails_today' => $failsToday,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('❌ Error obteniendo KPIs:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener KPIs'
            ], 500);
        }
    }

    /**
     * 📋 Obtener lista de todos los tenants
     *
     * GET /admin/api/tenants
     */
    public function getTenants()
    {
        try {
            // Get error counts per tenant
            $errorCounts = [];
            try {
                $errorCounts = DB::connection('mysql')->table('tenant_error_logs')
                    ->select('tenant_id', DB::raw('SUM(CASE WHEN resolved = 0 THEN 1 ELSE 0 END) as active_errors'))
                    ->groupBy('tenant_id')
                    ->pluck('active_errors', 'tenant_id')
                    ->toArray();
            } catch (\Throwable $e) {
                // Table may not exist yet
            }

            $tenants = Tenant::with('domains')->get()->map(function($tenant) use ($errorCounts) {
                // stancl/tenancy permite acceder a valores de JSON como propiedades directas
                $subscriptionStart = $tenant->subscription_start ?? ($tenant->created_at ? $tenant->created_at->format('Y-m-d') : null);
                $subscriptionEnd = $tenant->subscription_end ?? $tenant->subscription_ends_at;

                // Convertir a Carbon si es string
                if ($subscriptionEnd && is_string($subscriptionEnd)) {
                    $subscriptionEnd = \Carbon\Carbon::parse($subscriptionEnd);
                }

                // Determinar status: primero usar el guardado, luego calcular por fecha
                $status = $tenant->status ?? 'active';
                if ($subscriptionEnd && $subscriptionEnd < now() && $status === 'active') {
                    $status = 'suspended'; // Expirado
                }

                return [
                    'id' => $tenant->id,
                    'name' => $tenant->business_name ?? $tenant->id,
                    'domain' => $tenant->domains->first()->domain ?? 'sin-dominio',
                    'plan' => $tenant->plan,
                    'status' => $status,
                    'created_at' => $tenant->created_at->format('Y-m-d H:i:s'),
                    'subscription_start' => $subscriptionStart,
                    'subscription_end' => $subscriptionEnd ? (is_string($subscriptionEnd) ? $subscriptionEnd : $subscriptionEnd->format('Y-m-d')) : null,
                    'error_count' => (int)($errorCounts[$tenant->id] ?? 0),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $tenants
            ]);
        } catch (\Exception $e) {
            \Log::error('❌ Error obteniendo tenants:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener tenants'
            ], 500);
        }
    }

    /**
     * 🔍 Obtener detalles de un tenant específico
     *
     * GET /admin/api/tenants/{id}
     */
    public function getTenantDetails($tenantId)
    {
        try {
            $tenant = Tenant::with('domains')->find($tenantId);

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant no encontrado'
                ], 404);
            }

            // Obtener estadísticas del tenant ejecutando consultas en su base de datos
            $stats = [];
            $adminUser = null;
            try {
                $tenant->run(function() use (&$stats, &$adminUser) {
                    $stats = [
                        'total_users' => DB::table('users')->count(),
                        'total_products' => DB::table('products')->count(),
                        'total_sales' => DB::table('sales')->count(),
                        'total_customers' => DB::table('customers')->count(),
                        'total_revenue' => DB::table('sales')->sum('total') ?? 0
                    ];
                    // Obtener el usuario admin (primer usuario creado)
                    $adminUser = DB::table('users')->orderBy('id')->first();
                });
            } catch (\Exception $e) {
                $stats = ['error' => 'No se pudo conectar a la base de datos del tenant'];
            }

            // stancl/tenancy permite acceder a valores de JSON como propiedades directas
            $subscriptionStart = $tenant->subscription_start ?? ($tenant->created_at ? $tenant->created_at->format('Y-m-d') : null);
            $subscriptionEnd = $tenant->subscription_end ?? $tenant->subscription_ends_at;

            // Status guardado en el JSON
            $status = $tenant->status ?? 'active';

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $tenant->id,
                    'business_name' => $tenant->business_name ?? $tenant->id,
                    'name' => $tenant->business_name ?? $tenant->id,
                    'domain' => $tenant->domains->first()->domain ?? 'sin-dominio',
                    'primary_domain' => $tenant->domains->first()->domain ?? 'sin-dominio',
                    'plan' => $tenant->plan ?? 'free_trial',
                    'status' => $status,
                    'created_at' => $tenant->created_at->format('Y-m-d H:i:s'),
                    'subscription_start' => $subscriptionStart,
                    'subscription_end' => $subscriptionEnd,
                    'owner_name' => $tenant->owner_name ?? null,
                    'cedula' => $tenant->cedula ?? null,
                    'admin_email' => $adminUser->email ?? null,
                    'admin_phone' => $adminUser->phone ?? null,
                    'stats' => $stats
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('❌ Error obteniendo detalles del tenant:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener detalles del tenant'
            ], 500);
        }
    }

    /**
     * ➕ Crear un nuevo tenant manualmente (Super Admin Only)
     *
     * POST /api/admin/tenants
     *
     * Body:
     * {
     *   "owner_name": "Juan Pérez",
     *   "cedula": "123456789",
     *   "business_name": "Mi Tienda Test",
     *   "subdomain": "mi-tienda-test",
     *   "plan": "premium",
     *   "admin_email": "admin@test.com",
     *   "admin_password": "password123"
     * }
     */
    public function createTenant(Request $request)
    {
        try {
            // Validación básica primero
            $validated = $request->validate([
                'owner_name' => 'required|string|max:255',
                'cedula' => 'required|string|max:15',
                'business_name' => 'required|string|max:255',
                'subdomain' => 'required|string|max:50|regex:/^[a-z0-9-]+$/',
                'plan' => 'required|in:free,basic,premium,enterprise',
                'admin_email' => 'required|email',
                'admin_password' => 'required|string|min:6'
            ]);

            // 🌍 Determinar dominio base según el entorno
            // Usar APP_ENV como fuente principal (más confiable que request()->getHost())
            $appEnv = config('app.env');
            $isProduction = $appEnv === 'production';

            // Si hay CENTRAL_DOMAIN configurado, usarlo para determinar el dominio base
            $centralDomain = env('CENTRAL_DOMAIN', '105pos.pro');
            $baseDomain = $isProduction ? '.' . $centralDomain : '.localhost';
            $checkDomain = $validated['subdomain'] . $baseDomain;


            // Validar que el dominio no exista
            if (DB::table('domains')->where('domain', $checkDomain)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este subdominio ya está ocupado. Por favor elige otro.',
                    'field' => 'subdomain'
                ], 422);
            }

            // Validar que la cédula no esté registrada
            $cedulaExists = DB::table('tenants')
                ->whereRaw("JSON_EXTRACT(data, '$.cedula') = ?", [$validated['cedula']])
                ->exists();

            if ($cedulaExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta cédula/NIT ya está registrada en el sistema.',
                    'field' => 'cedula'
                ], 422);
            }

            // Validar que el email no esté usado (revisar todos los tenants)
            $tenants = Tenant::all();
            $emailExists = false;

            foreach ($tenants as $t) {
                $t->run(function() use ($validated, &$emailExists) {
                    if (DB::table('users')->where('email', $validated['admin_email'])->exists()) {
                        $emailExists = true;
                    }
                });
                if ($emailExists) break;
            }

            if ($emailExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este email ya está registrado en otro negocio.',
                    'field' => 'admin_email'
                ], 422);
            }

            // Generar tenant_id único desde el subdomain
            $tenantId = $validated['subdomain'];

            // Verificar que el tenant_id no exista
            if (Tenant::find($tenantId)) {
                // Si existe, agregar timestamp
                $tenantId = $validated['subdomain'] . '_' . time();
            }

            // 🌍 Construir dominio completo según el entorno
            // Usar la misma lógica basada en APP_ENV
            $domain = $isProduction
                ? $validated['subdomain'] . '.' . $centralDomain
                : $validated['subdomain'] . '.localhost';


            // Validar que el dominio final no exista
            if (DB::table('domains')->where('domain', $domain)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este subdominio ya está ocupado. Por favor elige otro.',
                    'field' => 'subdomain'
                ], 422);
            }

            // Crear el tenant
            $tenant = Tenant::create([
                'id' => $tenantId,
                'business_name' => $validated['business_name'],
                'plan' => $validated['plan'],
                'subscription_ends_at' => now()->addMonths(1), // 1 mes de cortesía
                'data' => json_encode([
                    'created_by' => 'super_admin',
                    'owner_name' => $validated['owner_name'],
                    'cedula' => $validated['cedula'],
                    'created_at' => now()->toDateTimeString()
                ])
            ]);

            // Crear el dominio
            $tenant->domains()->create([
                'domain' => $domain  // ✅ Usar $domain en lugar de $fullDomain
            ]);

            // 🎯 PASO 1: Ejecutar seeder manualmente (como hace TenantRegisterController)
            // El seeder crea: roles (Administrador, Vendedor), usuario admin, system_settings, etc.
            $tenant->run(function () {
                try {
                    $seeder = new \Database\Seeders\DatabaseSeeder();
                    $seeder->run();
                } catch (\Exception $e) {
                    \Log::error('❌ SuperAdmin: Error ejecutando DatabaseSeeder: ' . $e->getMessage());
                    throw $e; // Re-lanzar para que el catch externo maneje el error
                }
            });

            // 🎯 PASO 2: Actualizar el usuario admin con los datos del formulario
            // El seeder ya creó el usuario admin con role_id=1, solo actualizamos sus datos
            $tenant->run(function() use ($validated) {
                try {
                    // Obtener el usuario admin creado por el seeder
                    $adminUser = DB::table('users')->where('role_id', 1)->first();

                    if ($adminUser) {
                        // Actualizar con los datos proporcionados por el super admin
                        DB::table('users')
                            ->where('id', $adminUser->id)
                            ->update([
                                'name' => $validated['owner_name'],
                                'email' => $validated['admin_email'],
                                'cc' => $validated['cedula'],
                                'password' => Hash::make($validated['admin_password']),
                                'email_verified_at' => now(),
                                'updated_at' => now()
                            ]);

                    }

                    // Actualizar configuración del sistema
                    DB::table('system_settings')
                        ->where('id', 1)
                        ->update([
                            'company_name' => $validated['business_name'],
                            'company_document' => $validated['cedula'],
                            'onboarding_completed' => 0,
                            'updated_at' => now()
                        ]);


                } catch (\Exception $e) {
                    \Log::error('❌ SuperAdmin: Error actualizando datos del tenant: ' . $e->getMessage());
                    throw $e;
                }
            });


            return response()->json([
                'success' => true,
                'message' => '✅ Tenant creado exitosamente',
                'data' => [
                    'tenant_id' => $tenant->id,
                    'business_name' => $validated['business_name'],
                    'owner_name' => $validated['owner_name'],
                    'cedula' => $validated['cedula'],
                    'domain' => $domain,
                    'plan' => $validated['plan'],
                    'subscription_end' => $tenant->subscription_ends_at->format('Y-m-d H:i:s'),
                    'login_url' => ($isProduction ? 'https://' : 'http://') . $domain . '/login',
                    'credentials' => [
                        'email' => $validated['admin_email'],
                        'password' => $validated['admin_password']
                    ]
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('❌ Error creando tenant manualmente:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al crear tenant: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar disponibilidad de subdominio
     */
    public function checkDomainAvailability($domain)
    {
        try {
            // Buscar en la tabla domains (central)
            $exists = DB::table('domains')->where('domain', $domain)->exists();

            return response()->json([
                'available' => !$exists,
                'domain' => $domain
            ]);
        } catch (\Exception $e) {
            \Log::error('❌ Error verificando disponibilidad de dominio:', [
                'domain' => $domain,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'available' => false,
                'error' => 'Error al verificar disponibilidad'
            ], 500);
        }
    }

    /**
     * Verificar disponibilidad de cédula/NIT
     */
    public function checkCedulaAvailability($cedula)
    {
        try {
            // Buscar en los tenants si la cédula ya existe
            $exists = DB::table('tenants')
                ->whereRaw("JSON_EXTRACT(data, '$.cedula') = ?", [$cedula])
                ->exists();

            return response()->json([
                'available' => !$exists,
                'cedula' => $cedula
            ]);
        } catch (\Exception $e) {
            \Log::error('❌ Error verificando disponibilidad de cédula:', [
                'cedula' => $cedula,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'available' => true, // Asumir disponible en caso de error
                'error' => 'Error al verificar disponibilidad'
            ]);
        }
    }

    /**
     * Verificar disponibilidad de email
     */
    public function checkEmailAvailability($email)
    {
        try {
            $email = urldecode($email);
            $emailExists = false;

            // Revisar en todos los tenants
            $tenants = Tenant::all();

            foreach ($tenants as $t) {
                $t->run(function() use ($email, &$emailExists) {
                    if (DB::table('users')->where('email', $email)->exists()) {
                        $emailExists = true;
                    }
                });
                if ($emailExists) break;
            }

            return response()->json([
                'available' => !$emailExists,
                'email' => $email
            ]);
        } catch (\Exception $e) {
            \Log::error('❌ Error verificando disponibilidad de email:', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'available' => true, // Asumir disponible en caso de error
                'error' => 'Error al verificar disponibilidad'
            ]);
        }
    }

    /**
     * Actualizar datos generales de un tenant (plan, status, business_name)
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
            \Log::error('❌ Error actualizando tenant:', [
                'tenant_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la tienda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar fechas de suscripción de un tenant
     */
    public function updateTenantSubscription(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'subscription_start' => 'required|date',
                'subscription_end' => 'required|date'
            ]);

            $tenant = Tenant::find($id);
            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant no encontrado'
                ], 404);
            }

            // stancl/tenancy permite asignar propiedades directamente y se guardan en el JSON data
            $tenant->subscription_start = $validated['subscription_start'];
            $tenant->subscription_end = $validated['subscription_end'];
            $tenant->save();


            return response()->json([
                'success' => true,
                'message' => 'Fechas actualizadas correctamente',
                'data' => [
                    'subscription_start' => $validated['subscription_start'],
                    'subscription_end' => $validated['subscription_end']
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('❌ Error actualizando suscripción:', [
                'tenant_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar fechas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar estado de un tenant (active, paused, suspended)
     */
    public function updateTenantStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:active,paused,suspended'
            ]);

            $tenant = Tenant::find($id);
            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant no encontrado'
                ], 404);
            }

            // stancl/tenancy permite asignar propiedades directamente y se guardan en el JSON data
            $tenant->status = $validated['status'];
            $tenant->save();


            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente',
                'data' => [
                    'status' => $validated['status']
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('❌ Error actualizando estado:', [
                'tenant_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🗑️ Eliminar un tenant y su base de datos
     *
     * DELETE /admin/api/tenants/{id}
     */
    public function deleteTenant($id)
    {
        try {
            $tenant = Tenant::find($id);
            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant no encontrado'
                ], 404);
            }

            $tenantDomain = $tenant->domains->first()->domain ?? 'unknown';
            $tenantData = $tenant->data ?? [];


            // Eliminar el tenant (esto también elimina su base de datos gracias al paquete)
            $tenant->delete();


            return response()->json([
                'success' => true,
                'message' => 'Tenant eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            \Log::error('❌ Error eliminando tenant:', [
                'tenant_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar tenant: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener usuarios de un tenant
     */
    public function getTenantUsers($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            $tenantDbName = 'tenant' . $tenant->id;

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

            return response()->json(['success' => true, 'data' => $users]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar usuarios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener productos de un tenant
     */
    public function getTenantProducts($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            $tenantDbName = 'tenant' . $tenant->id;

            $products = DB::connection('mysql')
                ->table($tenantDbName . '.products')
                ->select('id', 'name', 'price', 'stock', 'image_url', 'barcode', 'category_id', 'active', 'created_at')
                ->orderBy('name', 'asc')
                ->get();

            return response()->json(['success' => true, 'data' => $products]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar productos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resetear contraseña de un usuario de un tenant
     */
    public function resetUserPassword($tenantId, $userId, Request $request)
    {
        try {
            $tenant = Tenant::findOrFail($tenantId);
            $newPassword = $request->input('password');

            if (!$newPassword || strlen($newPassword) < 6) {
                return response()->json([
                    'success' => false,
                    'message' => 'La contraseña es requerida y debe tener al menos 6 caracteres'
                ], 400);
            }

            $tenantDbName = 'tenant' . $tenant->id;

            DB::connection('mysql')
                ->table($tenantDbName . '.users')
                ->where('id', $userId)
                ->update(['password' => Hash::make($newPassword)]);

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
     * Actualizar un producto de un tenant
     */
    public function updateTenantProduct($tenantId, $productId, Request $request)
    {
        try {
            $tenant = Tenant::findOrFail($tenantId);
            $tenantDbName = 'tenant' . $tenant->id;

            $updateData = [];
            if ($request->has('name')) $updateData['name'] = $request->input('name');
            if ($request->has('price')) $updateData['price'] = $request->input('price');
            if ($request->has('stock')) $updateData['stock'] = $request->input('stock');
            if ($request->has('active')) $updateData['active'] = $request->input('active');

            if (empty($updateData)) {
                return response()->json(['success' => false, 'message' => 'No hay datos para actualizar'], 400);
            }

            $updateData['updated_at'] = now();

            DB::connection('mysql')
                ->table($tenantDbName . '.products')
                ->where('id', $productId)
                ->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un producto de un tenant
     */
    public function deleteTenantProduct($tenantId, $productId)
    {
        try {
            $tenant = Tenant::findOrFail($tenantId);
            $tenantDbName = 'tenant' . $tenant->id;

            DB::connection('mysql')
                ->table($tenantDbName . '.products')
                ->where('id', $productId)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un usuario de un tenant (toggle active status)
     */
    public function updateTenantUser($tenantId, $userId, Request $request)
    {
        try {
            $tenant = Tenant::findOrFail($tenantId);
            $tenantDbName = 'tenant' . $tenant->id;

            $updateData = [];
            if ($request->has('active')) $updateData['active'] = $request->input('active') ? 1 : 0;
            if ($request->has('name')) $updateData['name'] = $request->input('name');
            if ($request->has('email')) $updateData['email'] = $request->input('email');

            if (empty($updateData)) {
                return response()->json(['success' => false, 'message' => 'No hay datos para actualizar'], 400);
            }

            $updateData['updated_at'] = now();

            DB::connection('mysql')
                ->table($tenantDbName . '.users')
                ->where('id', $userId)
                ->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== TENANT ERROR LOGS ====================

    /**
     * Get errors for a specific tenant.
     */
    public function getTenantErrors(Request $request, $tenantId)
    {
        try {
            $includeResolved = $request->boolean('include_resolved', false);
            $logger = new \App\Services\TenantErrorLoggerService();
            $errors = $logger->getTenantErrors($tenantId, $includeResolved);

            return response()->json([
                'success' => true,
                'data' => $errors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener logs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resolve (mark as fixed) a specific error.
     */
    public function resolveError(Request $request, $tenantId, $errorId)
    {
        try {
            $logger = new \App\Services\TenantErrorLoggerService();
            $resolved = $logger->resolveError((int)$errorId);

            return response()->json([
                'success' => $resolved,
                'message' => $resolved ? 'Error marcado como resuelto' : 'Error no encontrado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Trigger AI analysis for a specific error.
     */
    public function analyzeError(Request $request, $tenantId, $errorId)
    {
        try {
            $logger = new \App\Services\TenantErrorLoggerService();
            $summary = $logger->analyzeWithAI((int)$errorId);

            return response()->json([
                'success' => $summary !== null,
                'data' => ['ai_summary' => $summary],
                'message' => $summary ? 'Análisis completado' : 'No se pudo analizar'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al analizar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Trigger AI analysis for all pending errors of a tenant.
     */
    public function analyzeAllErrors(Request $request, $tenantId)
    {
        try {
            $logger = new \App\Services\TenantErrorLoggerService();
            $analyzed = $logger->analyzeAllPending($tenantId, 10);

            return response()->json([
                'success' => true,
                'data' => ['analyzed_count' => $analyzed],
                'message' => "Se analizaron {$analyzed} errores"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}