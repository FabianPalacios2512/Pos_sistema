<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SecurityService;

class SecurityRateLimit
{
    /**
     * Verifica si la IP está bloqueada antes de procesar login.
     * Aplica rate limiting básico sin dependencias externas.
     */
    public function handle(Request $request, Closure $next)
    {
        // Solo aplicar a rutas de login
        if (!$this->isLoginRoute($request)) {
            return $next($request);
        }

        $ip = $request->ip();

        try {
            $security = new SecurityService();

            // 1. Verificar si la IP está bloqueada
            if ($security->isIpBlocked($ip)) {
                return response()->json([
                    'success'    => false,
                    'blocked'    => true,
                    'block_type' => 'ip',
                    'message'    => 'Acceso temporalmente restringido desde tu ubicación. Intenta nuevamente en unos minutos.',
                ], 429);
            }

            // 2. Si se proporciona email, verificar si el usuario está bloqueado
            $email = $request->input('email');
            if ($email && $security->isUserBlocked($email)) {
                return response()->json([
                    'success'    => false,
                    'blocked'    => true,
                    'block_type' => 'account',
                    'message'    => 'Tu cuenta ha sido bloqueada por seguridad debido a múltiples intentos fallidos de inicio de sesión.',
                    'support_url' => 'https://wa.me/573217355070?text=' . urlencode('Hola, mi cuenta fue bloqueada en 105POS, necesito ayuda para recuperar el acceso.'),
                ], 423); // 423 Locked
            }

        } catch (\Exception $e) {
            // Si las tablas no existen aún, dejar pasar
            \Log::warning('SecurityRateLimit: ' . $e->getMessage());
        }

        return $next($request);
    }

    private function isLoginRoute(Request $request): bool
    {
        $path = $request->path();
        return $request->isMethod('POST') && (
            str_contains($path, 'login') ||
            str_contains($path, 'central/login')
        );
    }
}
