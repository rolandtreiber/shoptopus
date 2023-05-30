<?php

namespace App\Http\Resources\Common;

use App\Models\FileContent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin FileContent
 */
class FileContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'title' => $this->getTranslations('title'),
            'file_name' => $this->file_name,
            'description' => $this->getTranslations('description'),
            'type' => $this->type,
        ];
    }
}
