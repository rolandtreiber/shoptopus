<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (isset($request->lang)) {
            app()->setLocale($request->lang);
        }

        return $next($request);
    }
}
