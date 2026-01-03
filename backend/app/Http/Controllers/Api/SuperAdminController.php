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
                        'basic' => 29,
                        'premium' => 59,
                        'enterprise' => 99
                    ];
                    $mrr += $planPrices[$tenant->plan] ?? 0;
                }
            }

            // Nuevos tenants hoy
            $newToday = Tenant::whereDate('created_at', today())->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'active_clients' => $activeTenants,
                    'total_clients' => $totalTenants,
                    'mrr' => $mrr,
                    'new_today' => $newToday,
                    'ai_tokens_month' => 0 // TODO: Implementar tracking de tokens IA
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
            $tenants = Tenant::with('domains')->get()->map(function($tenant) {
                // Convertir subscription_ends_at a Carbon si es string
                $subscriptionEnd = $tenant->subscription_ends_at
                    ? \Carbon\Carbon::parse($tenant->subscription_ends_at)
                    : null;

                $isActive = !$subscriptionEnd || $subscriptionEnd > now();

                return [
                    'id' => $tenant->id,
                    'name' => $tenant->business_name ?? $tenant->id,
                    'domain' => $tenant->domains->first()->domain ?? 'sin-dominio',
                    'plan' => $tenant->plan,
                    'status' => $isActive ? 'active' : 'expired',
                    'created_at' => $tenant->created_at->format('Y-m-d H:i:s'),
                    'subscription_end' => $subscriptionEnd ? $subscriptionEnd->format('Y-m-d H:i:s') : null,
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
            $tenant->run(function() use (&$stats) {
                $stats = [
                    'users_count' => DB::table('users')->count(),
                    'products_count' => DB::table('products')->count(),
                    'sales_count' => DB::table('invoices')->where('type', 'sale')->count(),
                    'total_revenue' => DB::table('invoices')
                        ->where('type', 'sale')
                        ->sum('total_amount')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $tenant->id,
                    'domain' => $tenant->domains->first()->domain ?? 'sin-dominio',
                    'plan' => $tenant->plan_type,
                    'status' => $tenant->subscription_status,
                    'created_at' => $tenant->created_at->format('Y-m-d H:i:s'),
                    'subscription_end' => $tenant->subscription_end_date,
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

            $fullDomain = $validated['subdomain'] . '.105pos.pro';

            // Validar que el dominio no exista
            if (DB::table('domains')->where('domain', $fullDomain)->exists()) {
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

            // Construir dominio completo
            $fullDomain = $validated['subdomain'] . '.105pos.pro';

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
                'domain' => $fullDomain
            ]);

            // 🎯 PASO 1: Ejecutar seeder manualmente (como hace TenantRegisterController)
            // El seeder crea: roles (Administrador, Vendedor), usuario admin, system_settings, etc.
            $tenant->run(function () {
                try {
                    \Log::info('🌱 SuperAdmin: Ejecutando DatabaseSeeder...');
                    $seeder = new \Database\Seeders\DatabaseSeeder();
                    $seeder->run();
                    \Log::info('✅ SuperAdmin: DatabaseSeeder ejecutado correctamente');
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

                        \Log::info('✅ SuperAdmin: Usuario admin actualizado', [
                            'name' => $validated['owner_name'],
                            'email' => $validated['admin_email']
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

                    \Log::info('✅ SuperAdmin: Configuración del sistema actualizada');

                } catch (\Exception $e) {
                    \Log::error('❌ SuperAdmin: Error actualizando datos del tenant: ' . $e->getMessage());
                    throw $e;
                }
            });

            \Log::info("✅ Tenant creado manualmente por super admin", [
                'tenant_id' => $tenantId,
                'business_name' => $validated['business_name'],
                'domain' => $fullDomain,
                'plan' => $validated['plan'],
                'owner' => $validated['owner_name'],
                'cedula' => $validated['cedula']
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Tenant creado exitosamente',
                'data' => [
                    'tenant_id' => $tenant->id,
                    'business_name' => $validated['business_name'],
                    'owner_name' => $validated['owner_name'],
                    'cedula' => $validated['cedula'],
                    'domain' => $fullDomain,
                    'plan' => $validated['plan'],
                    'subscription_end' => $tenant->subscription_ends_at->format('Y-m-d H:i:s'),
                    'login_url' => 'https://' . $fullDomain . '/login',
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
}
