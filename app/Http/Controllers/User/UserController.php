<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserAccountRequest;
use App\Models\User;
use App\Services\Local\User\UserServiceInterface;
use App\Traits\ProcessRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ProcessRequest;
    private UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Favorite a single model
     */
    public function favorites(Request $request): JsonResponse
    {
        try {
            return response()->json($this->getResponse([], $this->userService->favorites(), $request));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Return the user's account details
     */
    public function getAccountDetails(Request $request): JsonResponse
    {
        try {
            return response()->json($this->getResponse([], $this->userService->getAccountDetails(), $request));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }


    /**
     * Update the user's account details
     */
    public function updateAccountDetails(UpdateUserAccountRequest $request): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth()->user();
            $data = $this->getProcessed($request->only([
                "first_name", "last_name", "phone", "avatar", "prefix", "password", "password_confirmation"
            ]), [], []);
            if (array_key_exists("email", $data)) {
                unset($data['email']);
            }
            isset($user->avatar) && $this->deleteCurrentFile($user->avatar->file_name);
            if (array_key_exists('password', $data) && array_key_exists('password_confirmation', $data)) {
                if ($data['password'] === $data['password_confirmation']) {
                    $data['password'] = Hash::make($data['password']);
                }
            }
            $user->fill($data);
            $request->hasFile('avatar') && $user->avatar = $this->saveFileAndGetUrl($request->avatar, config('shoptopus.user_avatar_dimensions')[0], config('shoptopus.user_avatar_dimensions')[1]);
            $user->save();

            return response()->json($this->getResponse([], $this->userService->getAccountDetails(), $request));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Soft delete the user's own account
     */
    public function deleteAccount(): JsonResponse
    {
        try {

            /** @var User $user */
            $user = Auth()->user();
            $user->delete();
            return response()->json(
                ['message' => 'User account deleted']
            );

        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }

    }

}
