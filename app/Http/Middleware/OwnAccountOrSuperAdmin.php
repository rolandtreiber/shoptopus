<?php

namespace App\Http\Middleware;

use App\Models\User;
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
        /** @var User $user */
        $user = $request->route('user');
        /** @var User $authUser */
        $authUser = Auth()->user();
        if ($user->id === $authUser->id || $authUser->hasRole('super_admin')) {
            return $next($request);
        }
        throw new AuthorizationException();
    }
}
