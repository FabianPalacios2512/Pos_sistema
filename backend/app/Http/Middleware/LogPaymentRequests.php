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
            Log::info('=== PAYMENT REQUEST DETECTED ===', [
                'path' => $request->path(),
                'full_url' => $request->fullUrl(),
                'method' => $request->method(),
                'query_string' => $request->query(),
                'all_params' => $request->all(),
                'headers' => [
                    'user_agent' => $request->header('User-Agent'),
                    'referer' => $request->header('Referer'),
                    'origin' => $request->header('Origin'),
                ],
                'timestamp' => now()->toIso8601String(),
            ]);
        }

        return $next($request);
    }
}
