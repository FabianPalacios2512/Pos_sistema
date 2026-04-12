<?php

namespace App\Http\Middleware;

use Closure;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain as BaseInitializeTenancyByDomain;

/**
 * Middleware personalizado para inicializar tenancy por dominio,
 * pero omitiendo rutas admin y dominios centrales.
 */
class InitializeTenancyByDomain extends BaseInitializeTenancyByDomain
{
    public function handle($request, Closure $next)
    {
        // Si la ruta está marcada como exempt, saltar tenancy
        if ($request->attributes->get('tenancy_exempt')) {
            return $next($request);
        }

        // Si el dominio es central (localhost, 127.0.0.1, etc), saltar tenancy
        $hostname = $request->getHost();
        $centralDomains = config('tenancy.central_domains', []);
        if (in_array($hostname, $centralDomains)) {
            return $next($request);
        }

        // Si la ruta es de admin, saltar tenancy
        $path = $request->path();
        if (str_starts_with($path, 'api/admin/') || str_starts_with($path, 'admin/api/')) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
