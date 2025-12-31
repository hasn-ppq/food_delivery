<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        $userRole = $user->role;

        if (!$userRole) {
            abort(403);
        }

        $slug = $userRole->slug ?? null;

        if (!$slug || strtolower($slug) !== strtolower($role)) {
            abort(403);
        }

        return $next($request);
    }
}
