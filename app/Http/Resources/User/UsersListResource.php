<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'profile_photo_path' => $this->profile_photo_path,
            'private_chat' => $this->private_chat,
            'status' => $this->status,
            'tag' => $this->tag,
            'country_code' => $this->country_code,
            'city' => $this->city,
        ];
    }
}
