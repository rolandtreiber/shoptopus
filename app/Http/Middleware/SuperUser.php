<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class SuperUser
{
    /**
     * Handle an incoming request.
     *
     *
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth()->user();
        if ($user->hasRole(['super_admin', 'store_manager'])) {
            return $next($request);
        } else {
            throw new AuthorizationException();
        }
    }
}
