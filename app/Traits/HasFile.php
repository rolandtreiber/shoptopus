<?php

namespace App\Traits;

use App\Models\FileContent;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @method morphOne(string $string, string $string1)
 */
trait HasFile
{
    /**
     * @return MorphOne
     */
    public function fileContent(): MorphOne
    {
        return $this->morphOne(FileContent::class, 'fileable');
    }
}
