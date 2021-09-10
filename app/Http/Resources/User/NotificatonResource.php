<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class NotificatonResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'timestamp' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'notifiable' => $this->notifiable
        ];
    }
}
