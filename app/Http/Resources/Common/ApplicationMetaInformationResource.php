<?php

namespace App\Http\Resources\Common;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationMetaInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray(Request $request): array
    {
        return [
            'locales' => config('app.locales_supported'),
            'default_locale' => config('app.locale'),
            'default_currency' => config('app.default_currency'),
            'google_maps_api_key' => config('app.google_maps_api_key'),
        ];
    }
}
