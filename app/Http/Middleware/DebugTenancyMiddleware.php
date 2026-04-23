<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class DebugTenancyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::debug('--- New Request ---');
        Log::debug('Request Path: ' . $request->path());
        Log::debug('Domain: ' . $request->getHost());
        Log::debug('Tenant ID: ' . (tenant() ? tenant()->id : 'NULL'));
        Log::debug('Current Guard: ' . auth()->getDefaultDriver());
        Log::debug('Auth Check (Web): ' . (auth()->guard('web')->check() ? 'YES (ID: '.auth()->guard('web')->id().')' : 'NO'));
        Log::debug('Auth Check (Central): ' . (auth()->guard('central')->check() ? 'YES (ID: '.auth()->guard('central')->id().')' : 'NO'));
        Log::debug('Session ID: ' . session()->getId());

        $response = $next($request);

        Log::debug('Response Status: ' . $response->getStatusCode());
        if ($response->isRedirection()) {
            Log::debug('Redirected to: ' . $response->headers->get('Location'));
        }

        return $response;
    }
}
