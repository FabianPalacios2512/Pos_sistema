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
        //
    })->create();
