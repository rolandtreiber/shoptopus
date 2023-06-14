<?php

namespace App\Http\Resources\Common;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Note
 */
class NoteListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'note' => $this->note,
            'user' => $this->user ? [
                'name' => $this->user->name,
                'id' => $this->user->id
            ] : null,
            'noteable_type' => $this->noteable_type,
            'noteable_id' => $this->noteable_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
