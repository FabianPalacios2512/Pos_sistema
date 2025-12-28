<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
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
            // 🔍 PASO 1: Buscar en qué tenant(s) existe este email
            $tenantDomains = $this->findUserTenants($request->email);

            if (empty($tenantDomains)) {
                throw ValidationException::withMessages([
                    'email' => ['No encontramos una cuenta con este correo electrónico.'],
                ]);
            }

            // Si hay múltiples tenants, tomar el primero (puedes mejorar esto después)
            $tenantDomain = $tenantDomains[0];

            // 🔐 PASO 2: Validar credenciales en el tenant encontrado
            $authResult = $this->authenticateInTenant($tenantDomain['tenant_id'], $request->email, $request->password);

            if (!$authResult['success']) {
                throw ValidationException::withMessages([
                    'email' => [$authResult['message']],
                ]);
            }

            // 🎯 PASO 3: Construir URL de redirección
            $protocol = app()->environment('local') ? (request()->secure() ? 'https://' : 'http://') : 'https://';
            $port = app()->environment('local') ? ':3000' : '';
            $redirectUrl = $protocol . $tenantDomain['domain'] . $port;

            // 📊 PASO 4: Retornar datos para el frontend
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
                    ],
                    // Token temporal para el frontend (se generará uno real en el tenant)
                    'credentials' => [
                        'email' => $request->email,
                        'password' => $request->password,
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
}
