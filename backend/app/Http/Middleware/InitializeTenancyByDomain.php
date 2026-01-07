<?php

namespace App\Http\Middleware;

use Closure;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain as BaseInitializeTenancyByDomain;

/**
 * Middleware personalizado para inicializar tenancy por dominio,
 * pero omitiendo rutas admin marcadas como "exempt".
 */
class InitializeTenancyByDomain extends BaseInitializeTenancyByDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Si la ruta está marcada como exempt, saltar tenancy
        if ($request->attributes->get('tenancy_exempt')) {
            return $next($request);
        }

        // Ejecutar tenancy normalmente
        return parent::handle($request, $next);
    }
}
