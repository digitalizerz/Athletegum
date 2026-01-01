<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     * Ensures user is authenticated and is an admin.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Enforce domain check - must be on admin subdomain
        $host = $request->getHost();
        if (strpos($host, 'admin.') !== 0) {
            abort(403, 'Admin routes are only accessible on admin.athletegum.com');
        }

        if (!Auth::check() || !Auth::user()->is_admin) {
            // Redirect to admin login if not authenticated or not admin
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
