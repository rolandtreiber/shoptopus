<?php

namespace App\Http\Resources\Common;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteDetailResource extends JsonResource
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
            'noteable' => $this->npteable,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
