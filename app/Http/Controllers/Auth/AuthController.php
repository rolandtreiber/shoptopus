<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendVerificationRequest;
use App\Services\Local\Auth\AuthServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * login api
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request) : \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->login($request->validated()));
        } catch (\Exception $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        } catch (\Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * register api
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request) : \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->register($request->validated()));
        } catch (\Exception $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        } catch (\Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * resend the verification email
     * @param ResendVerificationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendVerification(ResendVerificationRequest $request) : \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->authService->resendVerification($request->validated()));
        } catch (\Exception $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        } catch (\Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * Verify the user's email address
     * @param \Illuminate\Http\Request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request, int $id) : \Illuminate\Http\RedirectResponse
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
}
