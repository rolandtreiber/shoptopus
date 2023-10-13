<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\CannotDeleteRoleException;
use App\Exceptions\CannotDeleteSuperAdminRoleException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Resources\Admin\PermissionListResource;
use App\Http\Resources\Admin\RoleListResource;
use App\Http\Resources\Admin\UserListResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function listRoles(): AnonymousResourceCollection
    {
        return RoleListResource::collection(Role::all());
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function listPermissions(): AnonymousResourceCollection
    {
        return PermissionListResource::collection(Permission::all());
    }

    /**
     * @param Role $role
     * @return AnonymousResourceCollection
     */
    public function getPermissionsForRole(Role $role): AnonymousResourceCollection
    {
        return PermissionListResource::collection($role->permissions()->get());
    }

    /**
     * @param Role $role
     * @return AnonymousResourceCollection
     */
    public function getUsersWithRole(Role $role): AnonymousResourceCollection
    {
        return UserListResource::collection($role->users()->get());
    }

    /**
     * @param StoreRoleRequest $request
     * @return RoleListResource
     */
    public function createRole(StoreRoleRequest $request): RoleListResource
    {
        $role = new Role();
        $role->name = $request->name;
        $role->guard_name = 'api';
        $role->save();
        return new RoleListResource($role);
    }

    /**
     * @param Role $role
     * @param StoreRoleRequest $request
     * @return RoleListResource
     */
    public function updateRole(Role $role, StoreRoleRequest $request): RoleListResource
    {
        $role->name = $request->name;
        $role->save();
        return new RoleListResource($role);
    }

    /**
     * @param Role $role
     * @return string[]
     * @throws CannotDeleteRoleException
     * @throws CannotDeleteSuperAdminRoleException
     */
    public function deleteRole(Role $role): array
    {
        if ($role->id === 1) {
            throw new CannotDeleteSuperAdminRoleException();
        }
        if ($role->users()->count() !== 0) {
            throw new CannotDeleteRoleException();
        }

        $role->permissions()->detach();
        $role->delete();
        return ['status' => 'Success'];
    }

    /**
     * @param Role $role
     * @param Permission $permission
     * @return AnonymousResourceCollection
     */
    public function assignPermissionToRole(Role $role, Permission $permission): AnonymousResourceCollection
    {
        $role->givePermissionTo($permission);
        return PermissionListResource::collection($role->permissions()->get());
    }

    /**
     * @param Role $role
     * @param Permission $permission
     * @return AnonymousResourceCollection
     */
    public function removePermissionFromRole(Role $role, Permission $permission): AnonymousResourceCollection
    {
        $role->revokePermissionTo($permission);
        return PermissionListResource::collection($role->permissions()->get());
    }

    /**
     * @param Role $role
     * @param User $user
     * @return AnonymousResourceCollection
     */
    public function assignRoleToUser(Role $role, User $user): AnonymousResourceCollection
    {
        $role->users()->attach($user);
        return UserListResource::collection($role->users()->get());
    }

    /**
     * @param Role $role
     * @param User $user
     * @return AnonymousResourceCollection
     */
    public function removeRoleFromUser(Role $role, User $user): AnonymousResourceCollection
    {
        $role->users()->detach($user);
        return UserListResource::collection($role->users()->get());
    }

}
