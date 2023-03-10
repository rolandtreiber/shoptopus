<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next, string $redirectToRoute = null): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $notVerifiedErrorCode = Config::get('api_error_codes.services.auth.not_verified');

        if (! $request->user() ||
            ($request->user() instanceof MustVerifyEmail &&
                ! $request->user()->hasVerifiedEmail())) {
            return $request->expectsJson()
                ? response()->json([
                    'error_code' => $notVerifiedErrorCode,
                    'user_message' => 'Your email address is not verified.',
                ], 403)
                : Redirect::route($redirectToRoute ?: 'verification.notice');
        }

        return $next($request);
    }
}
