<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class LogAfterRequest
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle($request, \Closure  $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        Log::info('app.requests', ['request' => $request->all(), 'response' => $response]);
    }
}
