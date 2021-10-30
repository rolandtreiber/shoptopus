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
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'locales' => config('app.locales_supported'),
            'default_locale' => config('app.locale'),
            'default_currency' => config('app.default_currency')
        ];
    }
}
