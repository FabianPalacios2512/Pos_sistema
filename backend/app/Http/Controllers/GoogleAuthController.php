<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class GoogleAuthController extends Controller
{
    /**
     * Redirigir al usuario a Google para autenticación
     */
    public function redirectToGoogle(Request $request)
    {
        $params = [
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => config('services.google.redirect_uri'),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'online', // Cambiar a 'online' para experiencia más fluida
            // ⚠️ 'prompt' removido: Google decide automáticamente (mejor UX)
            // Solo mostrará "Selecciona cuenta" si hay múltiples, sin pantalla extra de confirmación
            // Guardar datos de registro temporales en state (opcional)
            'state' => base64_encode(json_encode($request->all()))
        ];

        $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);

        return response()->json([
            'success' => true,
            'url' => $url
        ]);
    }    /**
     * Callback de Google - Manejar el código de autorización
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $code = $request->input('code');

            if (!$code) {
                // 🎯 Si viene desde AJAX (frontend pidiendo datos)
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se recibió el código de autorización'
                    ], 400);
                }

                // Redirigir al registro con error
                $frontendUrl = config('app.frontend_url', 'https://105pos.pro');
                return redirect($frontendUrl . '/register?error=no_code');
            }

            // Intercambiar código por token de acceso (con timeout extendido)
            $tokenResponse = Http::timeout(30)
                ->asForm()
                ->post('https://oauth2.googleapis.com/token', [
                    'code' => $code,
                    'client_id' => config('services.google.client_id'),
                    'client_secret' => config('services.google.client_secret'),
                    'redirect_uri' => config('services.google.redirect_uri'),
                    'grant_type' => 'authorization_code'
                ]);

            if (!$tokenResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener token de Google',
                    'error' => $tokenResponse->json()
                ], 400);
            }

            $accessToken = $tokenResponse->json()['access_token'];

            // Obtener información del usuario de Google (con timeout extendido)
            $userResponse = Http::timeout(30) // Aumentar timeout a 30 segundos
                ->withToken($accessToken)
                ->get('https://www.googleapis.com/oauth2/v2/userinfo');

            if (!$userResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener información del usuario'
                ], 400);
            }

            $googleUser = $userResponse->json();

            // Decodificar state si existe (datos de registro)
            $registrationData = [];
            if ($request->has('state')) {
                $decodedState = json_decode(base64_decode($request->input('state')), true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedState)) {
                    $registrationData = $decodedState['state'] ?? $decodedState;

                    // Si es string, volver a decodificar
                    if (is_string($registrationData)) {
                        $registrationData = json_decode($registrationData, true) ?? [];
                    }
                }
            }


            // 🎯 PRIMERO: Verificar si el usuario YA EXISTE (buscar en todos los tenants)
            $userTenant = null;
            $existingUser = null;

            // Buscar en todos los tenants
            $tenants = \App\Models\Tenant::all();
            foreach ($tenants as $tenant) {
                try {
                    // Ejecutar en el contexto del tenant
                    $tenant->run(function () use ($googleUser, &$existingUser, &$userTenant, $tenant) {
                        $user = \DB::table('users')
                            ->where('email', $googleUser['email'])
                            ->first();

                        if ($user) {
                            $existingUser = $user;
                            $userTenant = $tenant;
                        }
                    });

                    // Si encontramos el usuario, salir del loop
                    if ($existingUser) {
                        break;
                    }
                } catch (\Exception $e) {
                    // Si hay error con este tenant, continuar con el siguiente
                    continue;
                }
            }

            if ($existingUser && $userTenant) {
                // 🔥 USUARIO EXISTENTE - Hacer login automático

                // Generar token de sesión
                $token = \Str::random(60);

                // Guardar sesión temporal en cache (5 minutos para que el frontend la capture)
                \Cache::put('google_login_' . $token, [
                    'user_id' => $existingUser->id,
                    'tenant_id' => $userTenant->id,
                    'email' => $existingUser->email,
                    'name' => $existingUser->name,
                ], now()->addMinutes(5));

                // Redirigir al frontend del tenant con el token de sesión
                // 🔥 Usar el dominio real del tenant desde la tabla domains
                $tenantDomain = $userTenant->domains->first();

                if (!$tenantDomain) {
                    \Log::error('❌ Tenant sin dominio asignado', ['tenant_id' => $userTenant->id]);
                    return redirect()->away('https://105pos.pro/login?error=tenant_no_domain');
                }

                if (app()->environment('local')) {
                    // En local: http://subdominio.localhost:3000/login?google_login_token=XXX
                    $redirectUrl = 'http://' . $tenantDomain->domain . ':3000/login?google_login_token=' . $token;
                } else {
                    // En producción: https://SUBDOMINIO.105pos.pro/login?google_login_token=XXX
                    $redirectUrl = 'https://' . $tenantDomain->domain . '/login?google_login_token=' . $token;
                }


                return redirect()->away($redirectUrl);
            }

            // 🎯 SI NO EXISTE EL USUARIO Y NO HAY DATOS DE FORMULARIO → Redirigir al registro
            $hasCompanyData = !empty($registrationData['company_name']) && !empty($registrationData['subdomain']);

            if (!$hasCompanyData) {

                // Generar token temporal único
                $tempToken = Str::random(64);

                // Guardar datos de Google en cache temporal (5 minutos)
                \Cache::put('google_auth_' . $tempToken, [
                    'google_id' => $googleUser['id'],
                    'email' => $googleUser['email'],
                    'name' => $googleUser['name'],
                    'picture' => $googleUser['picture'] ?? null,
                    'access_token' => $accessToken,
                ], now()->addMinutes(5));

                // 🔥 Usar URL fija en producción para evitar problemas de proxy
                $frontendUrl = config('app.frontend_url', 'https://105pos.pro');
                $redirectUrl = $frontendUrl . '/register?google_token=' . $tempToken;


                return redirect()->away($redirectUrl);
            }

            // 🎯 SI HAY DATOS COMPLETOS → Continuar con creación de tenant
            DB::beginTransaction();

            try {
                // Crear nuevo tenant (usando patrón de Stancl Tenancy)
                $companyName = $registrationData['company_name'] ?? explode('@', $googleUser['email'])[0];
                $subdomainBase = $registrationData['subdomain'] ?? Str::slug($companyName);

                // Detectar dominio base (igual que TenantRegisterController)
                $baseDomain = app()->environment('local') ? 'localhost' : (config('tenancy.central_domains')[0] ?? request()->getHost());
                $domainToCreate = $subdomainBase . '.' . $baseDomain;

                // Verificar si el dominio ya existe
                if (\Stancl\Tenancy\Database\Models\Domain::where('domain', $domainToCreate)->exists()) {
                    // Si el dominio existe, agregar contador
                    $counter = 1;
                    do {
                        $domainToCreate = $subdomainBase . $counter . '.' . $baseDomain;
                        $counter++;
                    } while (\Stancl\Tenancy\Database\Models\Domain::where('domain', $domainToCreate)->exists());

                    // Actualizar subdomainBase
                    $subdomainBase = $subdomainBase . ($counter - 1);
                }

                // Reemplazar guiones por guiones bajos para el ID (nombre de BD)
                $tenantId = str_replace('-', '_', $subdomainBase);

                // Limpiar base de datos huérfana si existe
                $tenantDbName = 'tenant' . $tenantId;
                $orphanDbExists = \DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$tenantDbName]);
                if (!empty($orphanDbExists)) {
                    \Log::warning("🧹 Base de datos huérfana detectada: {$tenantDbName}. Eliminando...");
                    \DB::statement("DROP DATABASE IF EXISTS `{$tenantDbName}`");
                }

                // 🎯 PASO 1: Crear tenant usando el modelo Tenant (esto dispara seeders automáticamente)
                $tenant = \App\Models\Tenant::create([
                    'id' => $tenantId,
                    'business_name' => $companyName,
                    'plan' => 'trial_express',
                    'subscription_ends_at' => now()->addDays(7),
                    'data' => json_encode([
                        'trial_started_at' => now()->toDateTimeString(),
                        'trial_days' => 7,
                        'payment_status' => 'trial',
                        'plan_pending' => false,
                        'google_user_id' => $googleUser['id'],
                        'google_email' => $googleUser['email']
                    ]),
                ]);

                // 🎯 PASO 2: Crear dominio asociado
                $tenant->domains()->create([
                    'domain' => $domainToCreate
                ]);

                // 🎯 PASO 3: Actualizar usuario admin dentro del contexto del tenant
                // Los seeders ya crearon el tenant con usuario admin por defecto
                $tenant->run(function () use ($googleUser, $companyName) {
                    try {
                        // El seeder ya creó un usuario admin con role_id 1
                        $adminUser = \App\Models\User::where('role_id', 1)->first();

                        if ($adminUser) {
                            $adminUser->update([
                                'name' => $googleUser['name'],
                                'email' => $googleUser['email'],
                                'password' => bcrypt(Str::random(32)), // Password aleatorio
                                'google_id' => $googleUser['id'], // Guardar Google ID
                            ]);

                        }

                        // Actualizar system_settings con datos del registro
                        \DB::table('system_settings')->where('id', 1)->update([
                            'company_name' => $companyName,
                            'company_email' => $googleUser['email'],
                            'onboarding_completed' => false, // 🔥 IMPORTANTE: forzar onboarding para nuevos usuarios
                            'updated_at' => now(),
                        ]);

                    } catch (\Exception $e) {
                        \Log::error('❌ Error al actualizar datos del tenant desde Google', [
                            'error' => $e->getMessage()
                        ]);
                        throw $e;
                    }
                });

                DB::commit();

                // Generar token temporal para el login
                $loginToken = Str::random(64);

                // Guardar datos de sesión temporalmente
                \Cache::put('google_login_' . $loginToken, [
                    'tenant_id' => $tenantId,
                    'user_id' => $newUser->id,
                    'email' => $googleUser['email'],
                    'tenant' => $tenant,
                ], now()->addMinutes(5));

                // Preparar URL de redirección al subdominio del tenant con token
                if (app()->environment('local')) {
                    // En local: http://subdominio.localhost:3000/login?google_token=...
                    $redirectUrl = 'http://' . $domainToCreate . ':3000/login?google_token=' . $loginToken;
                } else {
                    // En producción: https://subdominio.105pos.pro/login?google_token=...
                    $redirectUrl = 'https://' . $domainToCreate . '/login?google_token=' . $loginToken;
                }


                return redirect()->away($redirectUrl);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en autenticación con Google',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login con Google (para usuarios existentes)
     */
    public function loginWithGoogle(Request $request)
    {
        // Similar al callback pero solo para login
        return $this->handleGoogleCallback($request);
    }

    /**
     * Obtener datos temporales de Google usando token
     */
    public function getGoogleUserData(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token no proporcionado'
            ], 400);
        }

        $googleData = \Cache::get('google_auth_' . $token);

        if (!$googleData) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o expirado. Vuelve a autenticarte con Google.'
            ], 404);
        }

        // Eliminar token después de usarlo (one-time use)
        \Cache::forget('google_auth_' . $token);

        return response()->json([
            'success' => true,
            'user' => [
                'google_id' => $googleData['google_id'],
                'email' => $googleData['email'],
                'name' => $googleData['name'],
                'picture' => $googleData['picture']
            ]
        ]);
    }

    /**
     * Obtener sesión de login con Google usando token
     */
    public function getGoogleLoginSession(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token no proporcionado'
            ], 400);
        }

        $sessionData = \Cache::get('google_login_' . $token);

        if (!$sessionData) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o expirado. Vuelve a autenticarte con Google.'
            ], 404);
        }

        // Eliminar token después de usarlo (one-time use)
        \Cache::forget('google_login_' . $token);

        try {
            // 🔥 IMPORTANTE: Buscar el tenant y ejecutar en su contexto
            $tenant = \App\Models\Tenant::find($sessionData['tenant_id']);

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant no encontrado'
                ], 404);
            }

            // Ejecutar en contexto del tenant para generar token Sanctum
            $authToken = null;
            $userData = null;

            $tenant->run(function () use ($sessionData, &$authToken, &$userData) {
                // Buscar usuario en el tenant con rol y permisos
                $user = \App\Models\User::with('role')->find($sessionData['user_id']);

                if (!$user) {
                    throw new \Exception('Usuario no encontrado en el tenant');
                }

                // 🔥 Generar token Sanctum real
                $authToken = $user->createToken('google-auth-token')->plainTextToken;

                // 🎯 IMPORTANTE: Incluir TODOS los permisos del rol
                $roleData = null;
                if ($user->role) {
                    $roleData = [
                        'id' => $user->role->id,
                        'name' => $user->role->name,
                        'description' => $user->role->description,
                        'permissions' => $user->role->permissions ?? [], // ✅ Incluir permisos
                        'active' => $user->role->active
                    ];
                }

                // Preparar datos del usuario
                $userData = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'cc' => $user->cc,
                    'phone' => $user->phone,
                    'active' => $user->active,
                    'role_id' => $user->role_id,
                    'role' => $roleData, // ✅ Rol completo con permisos
                    'tenant_id' => $sessionData['tenant_id']
                ];
            });

            // 🔥 VERIFICAR SI EL TENANT TIENE UN PLAN VÁLIDO
            $validPlans = ['basic', 'premium', 'enterprise', 'free_trial'];
            $needsPlanSelection = false;
            $tenantInfo = null;
            
            if ($tenant) {
                $planType = $tenant->plan ?? 'pending';
                $subscriptionStatus = 'pending';
                
                if ($tenant->subscription_ends_at && now()->isBefore($tenant->subscription_ends_at)) {
                    $subscriptionStatus = 'active';
                } elseif ($tenant->subscription_ends_at && now()->isAfter($tenant->subscription_ends_at)) {
                    $subscriptionStatus = 'expired';
                }
                
                // Si no tiene plan válido, enviar señal al frontend
                if (!in_array($planType, $validPlans) || $subscriptionStatus === 'pending') {
                    $needsPlanSelection = true;
                    $tenantInfo = [
                        'id' => $tenant->id,
                        'business_name' => $tenant->business_name,
                        'plan_type' => $planType,
                        'subscription_status' => $subscriptionStatus
                    ];
                }
            }


            return response()->json([
                'success' => true,
                'needs_plan_selection' => $needsPlanSelection, // 🔥 NUEVO FLAG
                'tenant' => $tenantInfo, // 🔥 INFO DEL TENANT SI NECESITA PLAN
                'data' => [
                    'token' => $authToken,
                    'user' => $userData
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error generando sesión de Google', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al generar sesión: ' . $e->getMessage()
            ], 500);
        }
    }
}