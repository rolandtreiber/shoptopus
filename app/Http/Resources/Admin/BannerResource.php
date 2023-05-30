<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Banner
 */
class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->getTranslations('title'),
            'description' => $this->getTranslations('description'),
            'button_text' => $this->getTranslations('button_text'),
            'button_url' => $this->button_url,
            'background_image' => $this->background_image,
            'total_clicks' => $this->total_clicks,
            'enabled' => $this->enabled,
        ];
    }
}
