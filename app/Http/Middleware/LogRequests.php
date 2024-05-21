<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRequests
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('Incoming request', [
            'method' => $request->getMethod(),
            'url' => $request->fullUrl(),
            'params' => $request->all(),
        ]);

        return $next($request);
    }
}
