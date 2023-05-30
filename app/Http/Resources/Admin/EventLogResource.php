<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => $this->type,
            'message' => $this->message,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
