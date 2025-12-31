<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminSubdomain
{
    /**
     * Handle an incoming request on admin subdomain.
     * Redirects non-admin users to main site.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // This middleware is only applied to admin subdomain routes via domain constraint
        // If user is authenticated but not admin, redirect to main site
        if (Auth::check() && !Auth::user()->is_admin) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('https://athletegum.com');
        }

        return $next($request);
    }
}
