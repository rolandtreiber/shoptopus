<?php

namespace App\Http\Resources\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'avatar' => $this->avatar,
            'name' => $this->name,
            'prefix' => $this->prefix,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'initials' => $this->initials,
            'email' => $this->email,
            'email_verified' => $this->email_verified_at !== null,
            'roles' => $this->getRoleNames(),
        ];
    }
}
