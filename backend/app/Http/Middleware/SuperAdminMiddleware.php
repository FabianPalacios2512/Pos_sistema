<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Este middleware verifica que el usuario tenga acceso al panel central
     * Implementa tu lógica de autenticación central aquí
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si estamos en dominio central
        $centralDomains = config('tenancy.central_domains', ['127.0.0.1', 'localhost']);

        if (!in_array($request->getHost(), $centralDomains)) {
            abort(403, 'Acceso denegado: No estás en el dominio central');
        }

        // TODO: Implementar autenticación de super admin
        // Por ahora, permitimos acceso en desarrollo
        // En producción, verifica contra una tabla de admins centrales

        // Ejemplo de verificación básica:
        // if (!auth()->check() || !auth()->user()->is_super_admin) {
        //     return redirect()->route('login')->with('error', 'Acceso no autorizado');
        // }

        return $next($request);
    }
}
