<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsNotMaster
{
    /**
     * Handle an incoming request.
     * Block master users from accessing business operational routes.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If we are in tenant context, we don't care about the central guard check
        if (tenant()) {
            return $next($request);
        }

        if (auth()->guard('central')->check()) {
            return redirect('/master/dashboard');
        }

        if (auth()->check() && auth()->user()->is_master) {
            return redirect('/master/dashboard');
        }

        return $next($request);
    }
}
