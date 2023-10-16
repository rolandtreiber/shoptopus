<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $id
 * @property Carbon $created_at
 * @property mixed $data
 * @property string $type
 * @property string $read_at
 */
class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $type = str_replace('App\\Notifications\\', '', $this->type);
        $type = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $type));

        return [
            'id' => $this->id,
            'type' => $type,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'data' => $this->data,
            'read' => $this->read_at !== null,
        ];
    }
}
