<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;

class InitializeTenancyForLivewire
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $centralDomains = config('tenancy.central_domains', []);
        $currentHost = $request->getHost();

        // Jika domain saat ini adalah domain pusat, lewati inisialisasi tenant
        if (in_array($currentHost, $centralDomains)) {
            return $next($request);
        }

        // Jika bukan domain pusat, jalankan inisialisasi tenant standar
        return app(InitializeTenancyByDomain::class)->handle($request, $next);
    }
}
