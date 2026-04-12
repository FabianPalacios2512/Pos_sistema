<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogPaymentRequests
{
    /**
     * Middleware para loguear todas las requests relacionadas con pagos.
     * Esto ayuda a detectar si Wompi está redirigiendo a URLs inesperadas.
     */
    public function handle(Request $request, Closure $next)
    {
        // Solo loguear si contiene "payment" en la URL
        if (str_contains($request->path(), 'payment')) {
        }

        return $next($request);
    }
}