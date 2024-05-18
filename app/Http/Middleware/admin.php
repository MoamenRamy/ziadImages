<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated and is an admin
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        // User is not authenticated or is not an admin
        // You can handle this case as needed, for example, redirect or return a response
        return response()->json(['message' => 'Unauthorized'], 403);
    }
}
