<?php

namespace App\Traits;

use App\Facades\Module;
use App\Models\Rating;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasRatings
{
    public function ratings(): ?MorphMany
    {
//        if (Module::enabled('ratings') === true) {
        return $this->morphMany(Rating::class, 'ratable');
//        }
        //return null;
    }
}
