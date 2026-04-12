<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para omitir inicialización de tenancy en rutas de super admin.
 *
 * Este middleware se ejecuta ANTES que InitializeTenancyByDomain y permite
 * que las rutas /api/admin/* sean accesibles desde el dominio central
 * sin necesidad de identificar un tenant.
 */
class SkipTenancyForAdminRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Lista de rutas que NO requieren tenancy
        $exemptRoutes = [
            'api/admin/login',
            'api/admin/*',
            'api/debug/*',
        ];


        foreach ($exemptRoutes as $pattern) {
            // Convertir patrón en regex
            $regex = '#^' . str_replace(['*', '/'], ['.*', '\/'], $pattern) . '$#';

            if (preg_match($regex, $request->path())) {
                // Marcar request como "exempt from tenancy"
                $request->attributes->set('tenancy_exempt', true);
                break;
            }
        }

        return $next($request);
    }
}