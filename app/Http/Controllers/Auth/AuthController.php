<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\GetOAuthProviderTargetUrlRequest;
use App\Http\Requests\Auth\HandleOAuthProviderCallbackRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendVerificationRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\Admin\UserDetailResource;
use App\Services\Local\Auth\AuthServiceInterface;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Login
     */
    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->login($request->validated()));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Register
     */
    public function register(RegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $role = $validatedData['role'] ?? null; // Access 'role' as an array key and default to null if not set
            return response()->json($this->authService->register($validatedData, $role)); // passing optional param role, as It's expected in register.
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Get the authenticated user details'
     */
    public function details(): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->details());
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Resend the verification email
     */
    public function resendVerification(ResendVerificationRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->resendVerification($request->validated()));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Verify the user's email
     */
    public function verify(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->verify($request, $id));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Logout api
     */
    public function logout(): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->logout());
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Send password reset email
     */
    public function sendPasswordReset(PasswordResetRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->sendPasswordReset($request->validated()));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(ResetPasswordRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->resetPassword($request->validated()));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Get the target url to the Auth provider's authentication page
     */
    public function getOAuthProviderTargetUrl(GetOAuthProviderTargetUrlRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->getOAuthProviderTargetUrl($request->validated()));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Obtain the user information from the Auth provider
     */
    public function handleOAuthProviderCallback(HandleOAuthProviderCallbackRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->handleOAuthProviderCallback($request->validated()));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    public function getAuthenticatedUser(): UserDetailResource
    {
        return new UserDetailResource(Auth()->user());
    }
}
