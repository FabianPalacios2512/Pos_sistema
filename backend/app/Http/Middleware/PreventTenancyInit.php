<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventTenancyInit
{
    public function handle(Request $request, Closure $next)
    {
        // Marcar esta petición como que NO debe inicializar tenancy
        // Esto debe ejecutarse ANTES de InitializeTenancy
        tenancy()->end(); // Si ya se inicializó, terminarla

        \Log::info('🚫 PreventTenancyInit: Evitando inicialización de tenancy', [
            'path' => $request->path(),
            'initialized' => tenancy()->initialized
        ]);

        return $next($request);
    }
}
