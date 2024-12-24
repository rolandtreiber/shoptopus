<?php

namespace App\Http\Controllers\Admin;

use _PHPStan_690619d82\Symfony\Component\Finder\Exception\AccessDeniedException;
use App\Enums\AccessTokenType;
use App\Enums\RandomStringMode;
use App\Helpers\GeneralHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InviteSystemUserRequest;
use App\Http\Requests\Admin\RegisterByInviteRequest;
use App\Http\Requests\Admin\UpdateUserProfilePhotoRequest;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\UserDetailResource;
use App\Http\Resources\Admin\UserListResource;
use App\Models\AccessToken;
use App\Models\User;
use App\Repositories\Admin\User\UserRepository;
use App\Services\Local\Auth\AuthServiceInterface;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class UserController extends Controller
{
    use ProcessRequest;

    private UserRepository $userRepository;
    private AuthServiceInterface $authService;

    public function __construct(UserRepository $userRepository, AuthServiceInterface $authService)
    {
        $this->authService = $authService;
        $this->userRepository = $userRepository;
    }

    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return UserListResource::collection(User::systemUsers()->filtered([], $request)->paginate(25));
    }

    public function show(User $user): UserDetailResource
    {
        return new UserDetailResource($user);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create(UserStoreRequest $request): UserDetailResource
    {
        $data = $this->getProcessed($request, [], []);
        $user = new User();
        $user->fill($data);
        $request->hasFile('avatar') && $user->avatar = $this->saveFileAndGetUrl($request->avatar, config('shoptopus.user_avatar_dimensions')[0], config('shoptopus.user_avatar_dimensions')[1]);
        $user->password = Hash::make(GeneralHelper::generateRandomString(8, RandomStringMode::UppercaseLowercaseAndNumbers));
        foreach ($request->roles as $role) {
            $user->assignRole($role);
        }
        $user->save();

        // TODO: send email with temporary password

        return new UserDetailResource($user);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function update(User $user, UserUpdateRequest $request): UserDetailResource
    {
        $data = $this->getProcessed($request, [], []);
        isset($user->avatar) && $this->deleteCurrentFile($user->avatar->file_name);
        $user->fill($data);
        $request->hasFile('avatar') && $user->avatar = $this->saveFileAndGetUrl($request->avatar, config('shoptopus.user_avatar_dimensions')[0], config('shoptopus.user_avatar_dimensions')[1]);
        foreach ($user->getRoleNames() as $role) {
            $user->removeRole($role);
        }
        foreach ($request->roles as $role) {
            $user->assignRole($role);
        }
        $user->save();

        return new UserDetailResource($user);
    }

    /**
     * Dedicated endpoint for updating the user avatar (and touching nothing else)
     */
    public function changeProfileImage(UpdateUserProfilePhotoRequest $request): UserDetailResource
    {
        $user = Auth()->user();
        if ($user) {
            isset($user->avatar) && $this->deleteCurrentFile($user->avatar->file_name);
            if ($request->hasFile('avatar')) {
                $user->avatar = $this->saveFileAndGetUrl($request->avatar, config('shoptopus.user_avatar_dimensions')[0], config('shoptopus.user_avatar_dimensions')[1]);
                $user->save();
            }

            return new UserDetailResource($user);
        }
        throw new ResourceNotFoundException("User not found");
    }

    /**
     * @return string[]
     */
    public function delete(User $user): array
    {
        $user->delete();

        return ['status' => 'Success'];
    }

    /**
     * @param InviteSystemUserRequest $request
     * @return string[]
     */
    public function invite(InviteSystemUserRequest $request): array
    {
        if ($this->userRepository->invite($request->email, $request->role)) {
            return [
                'status' => 'success',
                'message' => "User invited"
            ];
        } else {
            return [
                'status' => 'error',
                'message' => "User could not be invited. Please try again."
            ];
        }
    }

    /**
     * Register
     */
    public function registerByInvite(RegisterByInviteRequest $request, string $token): \Illuminate\Http\JsonResponse
    {
        /** @var AccessToken $token */
        $token = AccessToken::where('token', $token)->first();

        try {
            if (!$token || $token->type !== AccessTokenType::SignupRequest) {
                throw new AccessDeniedException("Invalid or expired token.");
            }

            $content = json_decode($token->content);

            /** @var Role $role */
            $role = Role::findByName($content->role);
            return response()->json($this->authService->register(array_merge($request->validated(), ['email' => $content->email]), $role));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }
}
