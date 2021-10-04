<?php

namespace App\Traits;

use App\Facades\Module;
use App\Models\Rating;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @method morphMany(string $string, string $string1)
 */
trait HasRatings {

    /**
     * @return MorphMany|null
     */
    public function ratings(): ?MorphMany
    {
//        if (Module::enabled('ratings') === true) {
            return $this->morphMany(Rating::class, 'ratable');
//        }
        return null;
    }

}
