<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class Customer extends User
{
    public static function all($columns = ['*'])
    {
        $role = Role::findByName(UserRole::Customer);
        $result = DB::table('users')
            ->join('model_has_roles', 'users.id', 'model_has_roles.model_id')
            ->where('model_has_roles.model_type', \App\Models\User::class)
            ->where('model_has_roles.role_id', $role->id)
            ->get();

        return self::hydrate($result->toArray());
    }
}
