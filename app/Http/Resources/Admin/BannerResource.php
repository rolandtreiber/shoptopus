<?php

namespace App\Http\Resources\Admin;

use App\Models\Banner;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Banner
 */
class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->getTranslations('title'),
            'description' => $this->getTranslations('description'),
            'button_text' => $this->getTranslations('button_text'),
            'button_url' => $this->button_url,
            'background_image' => $this->background_image,
            'total_clicks' => $this->total_clicks,
            'enabled' => $this->enabled
        ];
    }
}
