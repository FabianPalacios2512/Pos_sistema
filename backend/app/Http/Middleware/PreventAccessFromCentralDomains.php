<?php

namespace App\Http\Middleware;

use Closure;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains as BasePreventAccessFromCentralDomains;

/**
 * Middleware personalizado para prevenir acceso desde dominios centrales,
 * pero omitiendo rutas admin marcadas como "exempt".
 */
class PreventAccessFromCentralDomains extends BasePreventAccessFromCentralDomains
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
        // Si la ruta está marcada como exempt, saltar validación
        if ($request->attributes->get('tenancy_exempt')) {
            return $next($request);
        }

        // Caso contrario, ejecutar validación normalmente
        return parent::handle($request, $next);
    }
}