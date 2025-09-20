<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user() || !$request->user()->hasRole($role)) {
            // If user doesn't have the required role, redirect to their dashboard
            return redirect()->route($request->user()?->getDashboardRoute() ?? 'home');
        }

        return $next($request);
    }
}