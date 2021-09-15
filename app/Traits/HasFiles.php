<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\FileContent;

/**
 * @method morphMany(string $string, string $string1)
 */
trait HasFiles {

    /**
     * @return MorphMany
     */
    public function fileContents(): MorphMany
    {
        return $this->morphMany(FileContent::class, 'fileable');
    }

}
