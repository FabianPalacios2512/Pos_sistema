<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class AuthController extends Controller
{
    /**
     * Validar credenciales de administrador
     */
    public function validateAdmin(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
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
