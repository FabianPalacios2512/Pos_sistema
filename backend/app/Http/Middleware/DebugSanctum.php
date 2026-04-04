<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DebugSanctum
{
    public function handle(Request $request, Closure $next)
    {
        \Log::info('🔍 DebugSanctum', [
            'path' => $request->path(),
            'host' => $request->getHost(),
            'tenancy_initialized' => tenancy()->initialized,
            'tenant_id' => tenancy()->initialized ? tenant()->id : null,
            'db_connection' => config('database.default'),
            'has_bearer' => $request->bearerToken() ? 'YES' : 'NO',
            'bearer_preview' => $request->bearerToken() ? substr($request->bearerToken(), 0, 20) : null
        ]);
        
        return $next($request);
    }
}
