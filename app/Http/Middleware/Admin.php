<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws AuthorizationException
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth()->user();
        if ($user->hasRole(['admin', 'super_admin', 'store_manager', 'store_assistant', 'auditor'])) {
            return $next($request);
        } else {
            throw new AuthorizationException();
        }
    }
}
