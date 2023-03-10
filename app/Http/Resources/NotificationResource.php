<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
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
