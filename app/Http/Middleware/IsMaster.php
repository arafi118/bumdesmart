<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsMaster
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guard('central')->check()) {
            return $next($request);
        }

        if (auth()->check() && auth()->user()->is_master) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
