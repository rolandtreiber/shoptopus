<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Local\User\UserServiceInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Favorite a single model
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function favorites(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->getResponse([], $this->userService->favorites(), $request));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }
}
