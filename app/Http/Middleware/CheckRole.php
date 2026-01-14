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
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Jika user tidak authenticated, redirect ke login
        if (!auth()->check()) {
            return redirect('/login');
        }

        // Check if user's role is in allowed roles
        if (in_array(auth()->user()->role, $roles)) {
            return $next($request);
        }

        // Unauthorized access
        return response()->view('errors.403', [], 403);
    }
}
