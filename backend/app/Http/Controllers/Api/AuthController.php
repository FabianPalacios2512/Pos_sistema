<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // PRIMERO: Intentar autenticar como Super Admin (Base Central)
        // Wrap todo el bloque en try-catch para manejar errores de conexión
        try {
            $centralUser = DB::connection('mysql')->table('central_users')
                ->where('email', $request->email)
                ->first();

            if ($centralUser && Hash::check($request->password, $centralUser->password)) {
                if (!$centralUser->is_active) {
                    throw ValidationException::withMessages([
                        'email' => ['Tu cuenta de super admin está desactivada.'],
                    ]);
                }

                // Crear token para super admin (sin modelo Eloquent)
                $token = bin2hex(random_bytes(40));

                // Actualizar último login
                DB::connection('mysql')->table('central_users')
                    ->where('id', $centralUser->id)
                    ->update(['updated_at' => now()]);

                return response()->json([
                    'success' => true,
                    'message' => 'Inicio de sesión exitoso como Super Admin',
                    'data' => [
                        'user' => [
                            'id' => $centralUser->id,
                            'name' => $centralUser->name,
                            'email' => $centralUser->email,
                            'phone' => null,
                            'role' => [
                                'id' => 0,
                                'name' => $centralUser->role,
                                'permissions' => ['*'] // Super admin tiene todos los permisos
                            ],
                            'is_super_admin' => true,
                            'last_login' => now()
                        ],
                        'token' => $token
                    ]
                ]);
            }
        } catch (\Exception $e) {
            // Si falla la conexión a central_users, continuar con login de tenant
            \Log::warning('Error en autenticación de super admin: ' . $e->getMessage());
            // Continuar al login normal de tenant
        }

        // SEGUNDO: Si no es super admin, buscar en tenant
        $user = User::with('role')->where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No encontramos una cuenta con este correo electrónico.',
                'errors' => [
                    'email' => ['El correo electrónico no está registrado.']
                ]
            ], 422);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'La contraseña es incorrecta.',
                'errors' => [
                    'password' => ['La contraseña ingresada no es correcta.']
                ]
            ], 422);
        }

        if (!$user->active) {
            return response()->json([
                'success' => false,
                'message' => 'Tu cuenta está desactivada. Contacta al administrador.',
                'errors' => [
                    'email' => ['Tu cuenta está desactivada.']
                ]
            ], 422);
        }

        // Actualizar último login
        $user->updateLastLogin();

        $token = $user->createToken('api-token')->plainTextToken;

        // 🔥 VERIFICAR SI EL TENANT TIENE UN PLAN VÁLIDO
        $tenant = tenant();
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
            'message' => 'Inicio de sesión exitoso',
            'needs_plan_selection' => $needsPlanSelection, // 🔥 NUEVO FLAG
            'tenant' => $tenantInfo, // 🔥 INFO DEL TENANT SI NECESITA PLAN
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'cc' => $user->cc,
                    'role' => $user->role ? [
                        'id' => $user->role->id,
                        'name' => $user->role->name,
                        'permissions' => $user->role->permissions
                    ] : null,
                    'is_super_admin' => false,
                    'last_login' => $user->last_login
                ],
                'token' => $token
            ]
        ]);
    }

    /**
     * Login específico para Super Admin (solo busca en central_users)
     */
    public function adminLogin(Request $request)
    {
        \Log::info('🔑 adminLogin: Inicio del método', [
            'email' => $request->email,
            'path' => $request->path(),
            'host' => $request->getHost()
        ]);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            // Buscar SOLO en la tabla central_users
            $centralUser = DB::connection('mysql')->table('central_users')
                ->where('email', $request->email)
                ->first();

            if (!$centralUser || !Hash::check($request->password, $centralUser->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Las credenciales de super admin son incorrectas.'],
                ]);
            }

            if (!$centralUser->is_active) {
                throw ValidationException::withMessages([
                    'email' => ['Tu cuenta de super admin está desactivada.'],
                ]);
            }

            // Crear token para super admin
            $token = bin2hex(random_bytes(40));

            // Actualizar último login
            DB::connection('mysql')->table('central_users')
                ->where('id', $centralUser->id)
                ->update(['updated_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Inicio de sesión exitoso como Super Admin',
                'data' => [
                    'user' => [
                        'id' => $centralUser->id,
                        'name' => $centralUser->name,
                        'email' => $centralUser->email,
                        'phone' => null,
                        'role' => [
                            'id' => 0,
                            'name' => $centralUser->role,
                            'permissions' => ['*']
                        ],
                        'is_super_admin' => true,
                        'last_login' => now()
                    ],
                    'token' => $token
                ]
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error en login de super admin: ' . $e->getMessage());
            throw ValidationException::withMessages([
                'email' => ['Error al conectar con el sistema de autenticación de super admin.'],
            ]);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada exitosamente'
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('role');

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'cc' => $user->cc,
                    'role' => $user->role ? [
                        'id' => $user->role->id,
                        'name' => $user->role->name,
                        'permissions' => $user->role->permissions
                    ] : null,
                    'last_login' => $user->last_login
                ]
            ]
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'role_id' => 'nullable|exists:roles,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role_id' => $request->role_id,
            'active' => true
        ]);

        $user->load('role');

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role ? [
                        'id' => $user->role->id,
                        'name' => $user->role->name,
                        'permissions' => $user->role->permissions
                    ] : null
                ],
                'token' => $token
            ]
        ], 201);
    }

    /**
     * Validar credenciales de administrador
     */
    public function validateAdmin(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string',
                'password' => 'required'
            ]);

            $email = $request->input('email');
            $password = $request->input('password');

            // Buscar usuario por email o cc
            $user = User::with('role')
                ->where(function ($query) use ($email) {
                    $query->where('email', $email)
                          ->orWhere('cc', $email);
                })
                ->first();

            if (!$user) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Verificar contraseña
            if (!Hash::check($password, $user->password)) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Contraseña incorrecta'
                ], 401);
            }

            // Verificar que sea administrador
            if (!$user->role || $user->role->name !== 'Administrador') {
                return response()->json([
                    'valid' => false,
                    'message' => 'No tiene permisos de administrador'
                ], 403);
            }

            return response()->json([
                'valid' => true,
                'message' => 'Credenciales válidas',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'cc' => $user->cc,
                    'role' => $user->role->name
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
