<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Block routes from being accessed on admin subdomain
 * This ensures web.php routes are NOT available on admin.athletegum.com
 */
class BlockAdminSubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Block all routes if accessed from admin subdomain
        if ($request->getHost() === 'admin.athletegum.com') {
            abort(404, 'Not found');
        }

        return $next($request);
    }
}

