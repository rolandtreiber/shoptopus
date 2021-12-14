<?php

namespace App\Http\Resources\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class CustomerListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'avatar' => $this->avatar,
            'name' => $this->name,
            'prefix' => $this->prefix,
            'first_name' => $this->first_name,
            'last_name' => $this->first_name,
            'phone' => $this->phone,
            'initials' => $this->initials,
            'email' => $this->email,
            'email_verified' => $this->email_verified_at !== null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'is_favorite' => $this->is_favorite
        ];
    }
}
