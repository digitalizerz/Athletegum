<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminSubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Ensure this middleware only applies to the admin subdomain
        if ($request->getHost() !== 'admin.athletegum.com') {
            abort(404);
        }

        // Allow admin authentication routes
        if ($request->routeIs('admin.login', 'admin.logout')) {
            return $next($request);
        }

        return $next($request);
    }
}
