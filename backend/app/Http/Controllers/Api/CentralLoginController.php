<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Services\SecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * 🔐 Controlador de Login Centralizado
 *
 * Permite que los usuarios inicien sesión desde el dominio principal (105pos.com)
 * sin necesidad de saber su subdominio. El sistema automáticamente:
 * 1. Busca el tenant al que pertenece el email
 * 2. Valida las credenciales
 * 3. Retorna la URL del tenant para redirección
 */
class CentralLoginController extends Controller
{
    /**
     * 🎯 Login Centralizado Inteligente
     *
     * POST /api/central/login
     *
     * @param Request $request {
     *   email: string (requerido)
     *   password: string (requerido)
     * }
     *
     * @return JsonResponse {
     *   success: boolean,
     *   message: string,
     *   data: {
     *     tenant_domain: string (ej: "miempresa.105pos.com"),
     *     redirect_url: string (URL completa con protocolo),
     *     user: object (info básica del usuario),
     *     token: string (token de autenticación)
     *   }
     * }
     */
    public function centralLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            // 👑 SUPER ADMIN: Detectar si es el super admin (admin@superadmin)
            if ($request->email === 'admin@superadmin' && $request->password === '1001504182') {
                $protocol = app()->environment('local') ? 'http://' : 'https://';
                $domain = app()->environment('local') ? 'localhost:3000' : '105pos.pro';
                $redirectUrl = $protocol . $domain . '/admin/god-mode';

                // Generar token especial para super admin (no expira, solo para identificación)
                $superAdminToken = base64_encode('superadmin:' . time() . ':' . config('app.key'));


                return response()->json([
                    'success' => true,
                    'message' => 'Bienvenido Super Admin',
                    'data' => [
                        'is_super_admin' => true,
                        'redirect_url' => $redirectUrl,
                        'token' => $superAdminToken,
                        'user' => [
                            'id' => 0,
                            'email' => 'admin@superadmin',
                            'name' => 'Super Admin',
                            'role' => 'superadmin'
                        ]
                    ]
                ]);
            }

            // 🔍 PASO 1: Buscar en qué tenant(s) existe este email
            $tenantDomains = $this->findUserTenants($request->email);

            if (empty($tenantDomains)) {
                $security = new SecurityService();
                $security->recordAttempt($request->email, $request->ip(), $request->userAgent() ?? '', 'fail', 'user_not_found');
                $security->analyzeAfterFailedAttempt($request->email, $request->ip());
                return response()->json([
                    'success' => false,
                    'message' => 'No encontramos una cuenta asociada a este correo electrónico.',
                    'errors' => [
                        'email' => ['El correo electrónico no está registrado en ninguna tienda.']
                    ]
                ], 422);
            }

            // Si hay múltiples tenants, tomar el primero (puedes mejorar esto después)
            $tenantDomain = $tenantDomains[0];

            // 🔐 PASO 2: Validar credenciales en el tenant encontrado
            $authResult = $this->authenticateInTenant($tenantDomain['tenant_id'], $request->email, $request->password);

            if (!$authResult['success']) {
                $security = new SecurityService();
                $security->recordAttempt($request->email, $request->ip(), $request->userAgent() ?? '', 'fail', 'wrong_password');
                $actions = $security->analyzeAfterFailedAttempt($request->email, $request->ip());

                if (in_array('account_blocked', $actions)) {
                    return response()->json([
                        'success' => false,
                        'blocked' => true,
                        'block_type' => 'account',
                        'message' => 'Tu cuenta ha sido bloqueada por seguridad debido a múltiples intentos fallidos de inicio de sesión.',
                        'support_url' => 'https://wa.me/573217355070?text=' . urlencode('Hola, mi cuenta fue bloqueada en 105POS, necesito ayuda para recuperar el acceso.'),
                    ], 423);
                }

                return response()->json([
                    'success' => false,
                    'message' => $authResult['message'],
                    'errors' => [
                        'password' => [$authResult['message']]
                    ]
                ], 422);
            }

            // 🎯 PASO 3: Generar token temporal para auto-login cross-domain
            $security = new SecurityService();
            $security->recordAttempt($request->email, $request->ip(), $request->userAgent() ?? '', 'success', null, $tenantDomain['tenant_id']);
            $security->analyzeSuccessfulLogin($request->email, $request->ip());

            $tempToken = \Str::random(60);
            
            // Guardar sesión temporal en cache (5 minutos)
            \Cache::put('central_login_' . $tempToken, [
                'user_id' => $authResult['user']->id,
                'tenant_id' => $tenantDomain['tenant_id'],
                'email' => $request->email,
                'name' => $authResult['user']->name,
            ], now()->addMinutes(5));
            

            // 🎯 PASO 4: Construir URL de redirección con token
            $protocol = app()->environment('local') ? (request()->secure() ? 'https://' : 'http://') : 'https://';
            $port = app()->environment('local') ? ':3000' : '';
            $redirectUrl = $protocol . $tenantDomain['domain'] . $port . '/login?central_login_token=' . $tempToken;

            // 📊 PASO 5: Retornar datos para el frontend
            return response()->json([
                'success' => true,
                'message' => 'Credenciales válidas. Redirigiendo a tu cuenta...',
                'data' => [
                    'tenant_id' => $tenantDomain['tenant_id'],
                    'tenant_domain' => $tenantDomain['domain'],
                    'redirect_url' => $redirectUrl,
                    'user' => [
                        'email' => $request->email,
                        'name' => $authResult['user']->name,
                    ]
                ]
            ]);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('❌ Error en login centralizado', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el inicio de sesión. Intenta de nuevo.'
            ], 500);
        }
    }

    /**
     * 🔍 Buscar en qué tenants existe un email
     *
     * @param string $email
     * @return array Lista de tenants donde existe el email
     */
    private function findUserTenants(string $email): array
    {
        $tenants = Tenant::all();
        $foundTenants = [];

        foreach ($tenants as $tenant) {
            try {
                $tenant->run(function () use ($email, $tenant, &$foundTenants) {
                    $userExists = DB::table('users')->where('email', $email)->exists();

                    if ($userExists) {
                        // Obtener el dominio del tenant
                        $domain = DB::connection('mysql')
                            ->table('domains')
                            ->where('tenant_id', $tenant->id)
                            ->first();

                        if ($domain) {
                            $foundTenants[] = [
                                'tenant_id' => $tenant->id,
                                'domain' => $domain->domain,
                            ];
                        }
                    }
                });
            } catch (\Exception $e) {
                \Log::warning('⚠️ Error buscando usuario en tenant', [
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }

        return $foundTenants;
    }

    /**
     * 🔐 Validar credenciales en un tenant específico
     *
     * @param string $tenantId
     * @param string $email
     * @param string $password
     * @return array {success: bool, message: string, user: User|null}
     */
    private function authenticateInTenant(string $tenantId, string $email, string $password): array
    {
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            return [
                'success' => false,
                'message' => 'Cuenta no encontrada.',
                'user' => null
            ];
        }

        $result = ['success' => false, 'message' => '', 'user' => null];

        $tenant->run(function () use ($email, $password, &$result) {
            $user = User::where('email', $email)->first();

            if (!$user) {
                $result = [
                    'success' => false,
                    'message' => 'Usuario no encontrado.',
                    'user' => null
                ];
                return;
            }

            if (!Hash::check($password, $user->password)) {
                $result = [
                    'success' => false,
                    'message' => 'Contraseña incorrecta.',
                    'user' => null
                ];
                return;
            }

            if (!$user->active) {
                $result = [
                    'success' => false,
                    'message' => 'Tu cuenta está desactivada. Contacta al administrador.',
                    'user' => null
                ];
                return;
            }

            // Credenciales válidas
            $result = [
                'success' => true,
                'message' => 'Credenciales válidas',
                'user' => $user
            ];
        });

        return $result;
    }

    /**
     * 🔍 Verificar si un email existe en el sistema
     *
     * GET /api/central/check-email?email=user@example.com
     *
     * Útil para mostrar sugerencias en el frontend
     */
    public function checkEmailExists(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $tenants = $this->findUserTenants($request->email);

        return response()->json([
            'exists' => !empty($tenants),
            'tenant_count' => count($tenants),
            'domains' => array_column($tenants, 'domain')
        ]);
    }

    /**
     * 🆔 Verificar si un NIT/CC ya existe en el sistema
     *
     * GET /api/central/check-document?cedula=123456789
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkDocumentExists(Request $request)
    {
        $request->validate([
            'cedula' => 'required|string'
        ]);

        try {
            $cedula = $request->cedula;
            $foundTenants = [];

            // Buscar en todos los tenants si existe este NIT/CC en system_settings
            $tenants = Tenant::all();

            foreach ($tenants as $tenant) {
                try {
                    $tenant->run(function () use ($cedula, $tenant, &$foundTenants) {
                        // Buscar en system_settings (donde se guarda el NIT/CC del negocio)
                        $exists = DB::table('system_settings')
                            ->where('company_document', $cedula)
                            ->exists();

                        if ($exists) {
                            // Obtener el dominio del tenant
                            $domain = DB::connection('mysql')
                                ->table('domains')
                                ->where('tenant_id', $tenant->id)
                                ->first();

                            if ($domain) {
                                // Obtener info básica del tenant
                                $systemSettings = DB::table('system_settings')->first();

                                $foundTenants[] = [
                                    'tenant_id' => $tenant->id,
                                    'domain' => $domain->domain,
                                    'company_name' => $systemSettings->company_name ?? 'Empresa',
                                    'company_email' => $systemSettings->company_email ?? null
                                ];
                            }
                        }
                    });
                } catch (\Exception $e) {
                    \Log::warning('⚠️ Error buscando NIT/CC en tenant', [
                        'tenant_id' => $tenant->id,
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }

            if (!empty($foundTenants)) {
                return response()->json([
                    'exists' => true,
                    'message' => 'Ya existe una tienda registrada con este número de identificación.',
                    'tenants' => $foundTenants,
                    'tenant_count' => count($foundTenants)
                ]);
            }

            return response()->json([
                'exists' => false,
                'message' => 'NIT/CC disponible'
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ Error verificando NIT/CC', [
                'cedula' => $request->cedula,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al verificar NIT/CC. Por favor intenta de nuevo.'
            ], 500);
        }
    }

    /**
     * 🔐 Obtener sesión de login centralizado usando token temporal
     * 
     * GET /api/central/login-session?token=XXX
     * 
     * Este endpoint consume el token temporal generado en centralLogin()
     * y genera un token Sanctum real para el usuario en su tenant.
     */
    public function getCentralLoginSession(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token no proporcionado'
            ], 400);
        }

        $sessionData = \Cache::get('central_login_' . $token);

        if (!$sessionData) {
            \Log::warning('⚠️ Central login token inválido o expirado', [
                'token' => substr($token, 0, 10) . '...'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o expirado. Por favor, inicia sesión nuevamente.'
            ], 404);
        }

        // Eliminar token después de usarlo (one-time use)
        \Cache::forget('central_login_' . $token);

        try {
            // Buscar el tenant
            $tenant = Tenant::find($sessionData['tenant_id']);

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
                $user = User::with('role')->find($sessionData['user_id']);

                if (!$user) {
                    throw new \Exception('Usuario no encontrado en el tenant');
                }

                // Generar token Sanctum real
                $authToken = $user->createToken('central-login-token')->plainTextToken;

                // Preparar datos del rol con permisos
                $roleData = null;
                if ($user->role) {
                    $roleData = [
                        'id' => $user->role->id,
                        'name' => $user->role->name,
                        'description' => $user->role->description,
                        'permissions' => $user->role->permissions ?? [],
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
                    'role' => $roleData,
                    'tenant_id' => $sessionData['tenant_id']
                ];
            });

            // Verificar si el tenant tiene un plan válido
            $validPlans = ['basic', 'premium', 'enterprise', 'free_trial'];
            $needsPlanSelection = false;
            $tenantInfo = null;
            
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
                    'plan' => $planType,
                    'subscription_status' => $subscriptionStatus
                ];
            }


            return response()->json([
                'success' => true,
                'message' => 'Sesión iniciada correctamente',
                'data' => [
                    'token' => $authToken,
                    'user' => $userData,
                ],
                'needs_plan_selection' => $needsPlanSelection,
                'tenant' => $tenantInfo
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ Error en getCentralLoginSession', [
                'error' => $e->getMessage(),
                'tenant_id' => $sessionData['tenant_id'] ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar sesión. Intenta nuevamente.'
            ], 500);
        }
    }
}