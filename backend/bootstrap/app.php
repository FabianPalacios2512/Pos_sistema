<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware global: seguridad en rutas de login
        $middleware->append(\App\Http\Middleware\SecurityRateLimit::class);

        // Registrar middleware de super admin, AI limits y trial status
        $middleware->alias([
            'superadmin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'ai.limit' => \App\Http\Middleware\CheckAiUsageLimit::class,
            'trial' => \App\Http\Middleware\CheckTrialStatus::class,
        ]);

        // Excluir rutas de admin de verificación CSRF
        $middleware->validateCsrfTokens(except: [
            'admin/api/*',
            'api/admin/*',
        ]);

        // NOTA: SkipTenancyForAdminRoutes se registra en TenancyServiceProvider
        // con máxima prioridad para ejecutarse antes que los middlewares de tenancy
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (\Throwable $e) {
            try {
                $tenantId = null;
                
                if (function_exists('tenant') && tenant()) {
                    $tenantId = tenant()->id ?? tenant()->getTenantKey();
                } elseif (request()->header('X-Tenant-Id')) {
                    $tenantId = request()->header('X-Tenant-Id');
                } elseif (request()->route()) {
                    $route = request()->route();
                    if (str_contains(request()->path(), 'tenant')) {
                        $tenantId = $route->parameter('tenantId') ?? $route->parameter('id');
                    }
                }

                // Skip common noise that doesn't indicate real problems
                $skip = [
                    \Illuminate\Validation\ValidationException::class,
                    \Illuminate\Auth\AuthenticationException::class,
                    \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class,
                ];
                if (in_array(get_class($e), $skip)) return;

                // For 404s: only skip if NOT in tenant context (central 404s are noise)
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException && !$tenantId) {
                    return;
                }

                if ($tenantId) {
                    $logger = new \App\Services\TenantErrorLoggerService();
                    $logger->record($tenantId, $e, [
                        'url' => request()->fullUrl(),
                        'method' => request()->method(),
                        'user_id' => auth()->id(),
                        'user_agent' => request()->userAgent(),
                        'ip' => request()->ip(),
                    ]);
                    // Prevent double-logging: tenant errors go to DB only, not laravel.log
                    return false;
                }
            } catch (\Throwable $logError) {
                // Never break the app because of logging
            }
        });
    })->create();
