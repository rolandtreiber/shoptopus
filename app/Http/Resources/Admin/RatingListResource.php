<?php

namespace App\Http\Resources\Admin;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Rating
 */
class RatingListResource extends JsonResource
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
            'user_name' => $this->user->name,
            'rating' => $this->rating,
            'language' => $this->language_prefix,
            'title' => $this->title,
            'description' => $this->description,
            'ratable_type' => str_replace("App\Models\\", '', $this->ratable_type),
            'ratable_id' => $this->ratable_id,
            'verified' => $this->verified,
            'enabled' => $this->enabled,
            'left_at' => $this->created_at,
        ];
    }
}
