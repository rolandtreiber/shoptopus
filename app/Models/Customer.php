<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class Customer extends User
{
    /**
     * @param $columns
     * @return Collection
     */
    public static function all($columns = ['*']): Collection
    {
        $role = Role::findByName(UserRole::Customer);
        $result = DB::table('users')
            ->join('model_has_roles', 'users.id', 'model_has_roles.model_id')
            ->where('model_has_roles.model_type', \App\Models\User::class)
            // @phpstan-ignore-next-line
            ->where('model_has_roles.role_id', $role->id)
            ->get();

        return self::hydrate($result->toArray());
    }

    /**
     * @return MorphMany
     */
    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}
