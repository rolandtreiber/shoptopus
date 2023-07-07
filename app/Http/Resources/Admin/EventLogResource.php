<?php

namespace App\Http\Resources\Admin;

use App\Models\EventLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin EventLog
 */
class EventLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => $this->type,
            'message' => $this->message,
            'data' => $this->data,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
