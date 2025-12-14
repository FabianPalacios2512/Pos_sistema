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
            'subscription',
            'payment',
            'api/check-trial-status',
        ];

        // Verificar si la ruta actual está en la lista de permitidas
        foreach ($allowedRoutes as $route) {
            if ($request->is($route) || $request->is("api/{$route}") || $request->is("*/{$route}")) {
                return $next($request);
            }
        }

        // Planes de pago activos (no requieren validación de trial)
        $paidPlans = ['emprendedor', 'negocio_pro', 'premium', 'enterprise'];

        // Si tiene plan de pago, permitir acceso sin restricciones
        if (in_array($tenant->plan, $paidPlans)) {
            return $next($request);
        }

        // Si es trial_express, verificar fecha de expiración
        if ($tenant->plan === 'trial_express' && $tenant->subscription_ends_at) {
            $now = now();
            $expiresAt = \Carbon\Carbon::parse($tenant->subscription_ends_at);

            // Trial expirado
            if ($now->isAfter($expiresAt)) {
                // Para peticiones API, devolver JSON
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'trial_expired' => true,
                        'message' => 'Tu periodo de prueba ha finalizado. Actualiza tu plan para continuar.',
                        'days_remaining' => 0,
                        'upgrade_url' => '/upgrade',
                    ], 403);
                }

                // Para peticiones web, redirigir a /upgrade
                return redirect('/upgrade')->with('trial_expired', true);
            }

            // Trial activo: agregar info de días restantes al request
            $daysRemaining = $now->diffInDays($expiresAt, false);
            $request->attributes->set('trial_days_remaining', max(0, ceil($daysRemaining)));
        }

        return $next($request);
    }
}
