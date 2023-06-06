<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RandomStringMode;
use App\Helpers\GeneralHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\UserDetailResource;
use App\Http\Resources\Admin\UserListResource;
use App\Models\User;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ProcessRequest;

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
     * @return string[]
     */
    public function delete(User $user): array
    {
        $user->delete();

        return ['status' => 'Success'];
    }
}
