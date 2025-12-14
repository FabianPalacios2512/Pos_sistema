<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Services\EmailService;
use App\Models\User;
use App\Models\Tenant;

/**
 * 🔐 Controlador de Recuperación de Contraseña (Multi-tenant)
 *
 * Gestiona todo el flujo de password reset en entorno multi-tenant:
 * 1. Busca el usuario en todos los tenants
 * 2. Genera código de 6 dígitos
 * 3. Envía email con código
 * 4. Valida código
 * 5. Cambia contraseña
 */
class PasswordResetController extends Controller
{
    /**
     * 📧 Solicitar recuperación de contraseña
     *
     * POST /api/password/forgot
     * Body: { "email": "usuario@example.com" }
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Email inválido',
                'errors' => $validator->errors()
            ], 400);
        }

        $email = $request->email;
        $userFound = false;
        $userName = 'Usuario';
        $tenantId = null;

        // 🔍 Buscar usuario en TODOS los tenants
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $tenant->run(function () use ($email, &$userFound, &$userName, &$tenantId, $tenant) {
                $user = User::where('email', $email)->first();

                if ($user) {
                    $userFound = true;
                    $userName = $user->name;
                    $tenantId = $tenant->id;
                    return false; // Detener búsqueda
                }
            });

            if ($userFound) {
                break;
            }
        }

        if (!$userFound) {
            // Por seguridad, NO revelar si el email existe
            return response()->json([
                'success' => true,
                'message' => 'Si el email existe en nuestro sistema, recibirás instrucciones para recuperar tu contraseña.'
            ], 200);
        }

        // Rate limiting: máximo 5 intentos por hora (usar DB central)
        $recentAttempts = DB::connection('mysql')->table('password_reset_tokens')
            ->where('email', $email)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($recentAttempts >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Demasiados intentos. Por favor, intenta de nuevo en 1 hora.'
            ], 429);
        }

        // 🎲 Generar código de 6 dígitos
        $code = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(15); // 15 minutos de validez

        // Guardar código en base de datos CENTRAL
        DB::connection('mysql')->table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $code,
            'tenant_id' => $tenantId, // Guardar tenant_id para saber dónde está el usuario
            'expires_at' => $expiresAt,
            'used' => false,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 📧 Enviar email con código
        $emailSent = EmailService::sendPasswordResetEmail([
            'email' => $email,
            'name' => $userName,
            'code' => $code,
            'expires_at' => $expiresAt
        ]);

        if (!$emailSent) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el email. Por favor, contacta a soporte.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Si el email existe en nuestro sistema, recibirás instrucciones para recuperar tu contraseña.'
        ], 200);
    }

    /**
     * ✅ Validar código de 6 dígitos
     *
     * POST /api/password/validate-code
     * Body: { "code": "123456", "email": "usuario@example.com" }
     */
    public function validateCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6',
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Código de 6 dígitos y email requeridos',
                'errors' => $validator->errors()
            ], 400);
        }

        $resetToken = DB::connection('mysql')->table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$resetToken) {
            return response()->json([
                'success' => false,
                'message' => 'Código inválido o expirado'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Código válido'
        ], 200);
    }

    /**
     * 🔓 Resetear contraseña
     *
     * POST /api/password/reset
     * Body: {
     *   "code": "123456",
     *   "email": "usuario@example.com",
     *   "password": "nueva_contraseña",
     *   "password_confirmation": "nueva_contraseña"
     * }
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        // Verificar código en DB central
        $resetToken = DB::connection('mysql')->table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$resetToken) {
            return response()->json([
                'success' => false,
                'message' => 'Código inválido o expirado'
            ], 400);
        }

        // Obtener el tenant del usuario
        $tenantId = $resetToken->tenant_id;

        if (!$tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'Error: Tenant no encontrado'
            ], 500);
        }

        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Error: Tenant inválido'
            ], 500);
        }

        // Cambiar contraseña dentro del contexto del tenant
        $passwordChanged = false;
        $tenant->run(function () use ($request, &$passwordChanged) {
            $user = User::where('email', $request->email)->first();

            if ($user) {
                $user->password = Hash::make($request->password);
                $user->save();
                $passwordChanged = true;
            }
        });

        if (!$passwordChanged) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado en el tenant'
            ], 404);
        }

        // Marcar código como usado en DB central
        DB::connection('mysql')->table('password_reset_tokens')
            ->where('id', $resetToken->id)
            ->update([
                'used' => true,
                'used_at' => now(),
                'updated_at' => now()
            ]);

        // Invalidar todos los demás códigos del usuario
        DB::connection('mysql')->table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('id', '!=', $resetToken->id)
            ->update([
                'used' => true,
                'updated_at' => now()
            ]);

        // Enviar email de confirmación
        EmailService::sendPasswordChangedEmail([
            'email' => $request->email,
            'changed_at' => now()->format('d/m/Y H:i')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada exitosamente'
        ], 200);
    }

    /**
     * 🗑️ Limpiar tokens expirados (ejecutar en cron)
     *
     * GET /api/password/cleanup-tokens
     */
    public function cleanupExpiredTokens()
    {
        $deleted = DB::connection('mysql')->table('password_reset_tokens')
            ->where('expires_at', '<', now())
            ->orWhere('used', true)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Se eliminaron {$deleted} tokens expirados"
        ], 200);
    }
}
