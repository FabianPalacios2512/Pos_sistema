<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTrialStatus
{
    /**
     * Handle an incoming request.
     *
     * Bloquea el acceso si el trial expiró y no hay plan de pago activo.
     */
    public function handle(Request $request, Closure $next)
    {
        // Solo aplica a tenants
        if (!tenancy()->initialized) {
            return $next($request);
        }

        $tenant = tenant();

        // Rutas excluidas del bloqueo (siempre permitidas)
        $allowedRoutes = [
            'upgrade',
            'logout',
            'login',
            'register',
            'subscription',
            'payment',
            'api/check-subscription',
            'api/logout',
        ];

        // Verificar si la ruta actual está en la lista de permitidas
        foreach ($allowedRoutes as $route) {
            if ($request->is($route) || $request->is("api/{$route}") || $request->is("*/{$route}") || $request->is("api/*/{$route}")) {
                return $next($request);
            }
        }

        // ✅ VERIFICAR SUSCRIPCIÓN VENCIDA PARA TODOS LOS PLANES
        if ($tenant->subscription_ends_at) {
            $now = now();
            $expiresAt = \Carbon\Carbon::parse($tenant->subscription_ends_at);

            // Suscripción expirada - AGREGAR HEADER INFORMATIVO (NO BLOQUEAR)
            if ($now->isAfter($expiresAt)) {
                $daysExpired = abs($now->diffInDays($expiresAt, false));

                // 🔥 NO BLOQUEAR - Solo agregar información al header para que el frontend lo detecte
                $request->attributes->set('subscription_expired', true);
                $request->attributes->set('days_expired', $daysExpired);
                $request->attributes->set('expired_at', $expiresAt->format('Y-m-d H:i:s'));
                
                // Continuar con la petición normalmente
                $response = $next($request);
                
                // Agregar headers informativos a la respuesta
                if ($response instanceof \Illuminate\Http\JsonResponse) {
                    $data = $response->getData(true);
                    $data['_subscription_expired'] = true;
                    $data['_tenant_id'] = $tenant->id;
                    $data['_expired_at'] = $expiresAt->format('Y-m-d H:i:s');
                    $response->setData($data);
                }
                
                return $response;
            } else {
                // Suscripción activa: calcular días restantes
                $daysRemaining = $now->diffInDays($expiresAt, false);
                $request->attributes->set('subscription_days_remaining', max(0, ceil($daysRemaining)));

                // Alertar si quedan menos de 7 días
                if ($daysRemaining <= 7 && $daysRemaining > 0) {
                    $request->attributes->set('subscription_expiring_soon', true);
                    $request->attributes->set('days_remaining', ceil($daysRemaining));
                }
            }
        }

        return $next($request);
    }
}
