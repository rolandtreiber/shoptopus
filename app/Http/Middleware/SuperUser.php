<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class SuperUser
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth()->user();
        if ($user->hasRole(['super_admin', 'store_manager'])) {
            return $next($request);
        } else {
            throw new AuthenticationException();
        }
    }
}
