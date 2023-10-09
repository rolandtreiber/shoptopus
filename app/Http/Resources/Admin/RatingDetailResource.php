<?php

namespace App\Http\Resources\Admin;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin Rating
 */
class RatingDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'name' => $this->user->name,
                'client_ref' => $this->user->client_ref,
                'avatar' => $this->user->avatar,
                'email' => $this->user->last_seen,
                'slug' => $this->user->slug,
                'created_at' => $this->user->created_at
            ],
            'rated' => $this->rated(),
            'ratable' => [
                $this->ratable
            ],
            'rating' => $this->rating,
            'language_prefix' => $this->language_prefix,
            'title' => $this->title,
            'description' => $this->description,
            'enabled' => $this->enabled,
            'verified' => $this->verified,
            'slug' => $this->slug,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'files' => $this->fileContents
        ];
    }
}
