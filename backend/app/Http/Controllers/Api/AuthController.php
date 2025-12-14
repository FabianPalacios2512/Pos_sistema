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

        // SEGUNDO: Si no es super admin, buscar en tenant
        $user = User::with('role')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        if (!$user->active) {
            throw ValidationException::withMessages([
                'email' => ['Tu cuenta está desactivada.'],
            ]);
        }

        // Actualizar último login
        $user->updateLastLogin();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
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
                    ] : null,
                    'is_super_admin' => false,
                    'last_login' => $user->last_login
                ],
                'token' => $token
            ]
        ]);
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
