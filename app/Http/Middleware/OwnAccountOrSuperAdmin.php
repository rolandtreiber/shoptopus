<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OwnAccountOrSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     *
     * @throws AuthorizationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->route('user');
        $authUser = Auth()->user();
        if ($user->id === $authUser->id || $authUser->hasRole('super_admin')) {
            return $next($request);
        }
        throw new AuthorizationException();
    }
}
