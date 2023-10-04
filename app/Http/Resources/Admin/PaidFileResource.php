<?php

namespace App\Http\Resources\Admin;

use App\Models\PaidFileContent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PaidFileContent
 */
class PaidFileResource extends JsonResource
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
            'file_name' => $this->file_name,
            'url' => $this->url,
            'title' => $this->getTranslations('title'),
            'description' => $this->getTranslations('description')
        ];
    }
}
