<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\GetOAuthProviderTargetUrlRequest;
use App\Http\Requests\Auth\HandleOAuthProviderCallbackRequest;
use App\Http\Resources\Admin\UserDetailResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Services\Local\Auth\AuthServiceInterface;
use App\Http\Requests\Auth\ResendVerificationRequest;

class AuthController extends Controller
{
    private AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Login
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request) : \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->login($request->validated()));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * Register
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request) : \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->register($request->validated()));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * Get the authenticated user details'
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function details() : \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->details());
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * Resend the verification email
     *
     * @param ResendVerificationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendVerification(ResendVerificationRequest $request) : \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->resendVerification($request->validated()));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * Verify the user's email address
     *
     * @param \Illuminate\Http\Request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request, string $id) : \Illuminate\Http\RedirectResponse
    {
        try {
            $uri = 'login';
            $message = 'Email has been verified.';
            $statusCode = 200;

            if (!$request->hasValidSignature()) {
                $uri = 'verify';
                $message = "Invalid/Expired url provided.";
                $statusCode = 401;
            }

            $this->authService->verify($id);

            return redirect()->away(
                Config::get('app.frontend_url_public') . "/{$uri}?message=" . urlencode($message) . "&status=" . urlencode($statusCode)
            );
        } catch (\Exception | \Error $e) {
            // User not found
            return redirect()->away(
                Config::get('app.frontend_url_public') . "/verify?message=" . urlencode("Invalid/Expired url provided.") . "&status=401"
            );
        }
    }

    /**
     * Logout api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() : \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->logout());
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * Send password reset email
     *
     * @param PasswordResetRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendPasswordReset(PasswordResetRequest $request) : \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->sendPasswordReset($request->validated()));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * Reset password
     *
     * @param ResetPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request) : \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->resetPassword($request->validated()));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * Get the target url to the Auth provider's authentication page
     *
     * @param GetOAuthProviderTargetUrlRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOAuthProviderTargetUrl(GetOAuthProviderTargetUrlRequest $request) : \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->getOAuthProviderTargetUrl($request->validated()));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * Obtain the user information from the Auth provider
     *
     * @param HandleOAuthProviderCallbackRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleOAuthProviderCallback(HandleOAuthProviderCallbackRequest $request) : \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->handleOAuthProviderCallback($request->validated()));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * @return UserDetailResource
     */
    public function getAuthenticatedUser(): UserDetailResource
    {
        return new UserDetailResource(Auth()->user());
    }

}
