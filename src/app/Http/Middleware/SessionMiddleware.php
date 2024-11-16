<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user && $user->hasRole('superadmin')) {
            config(['session.lifetime' => 525600]); // Set session lifetime to 1 year (525600 minutes)
            return $next($request);
        }
    
        if (!$user) {
            abort(401, "You are not authenticated");
        }
    
        abort(403, "You are not authorized to access this page");
    }
}
