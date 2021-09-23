<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\FileContent;
use Illuminate\Support\Str;

/**
 * @method morphMany(string $string, string $string1)
 */
trait HasFiles {

    public function deleteWithAttachments()
    {
        foreach ($this->fileContents as $fileContent) {
            $fileContent->delete();
        }
        $this->delete();
    }

    /**
     * @return MorphMany
     */
    public function fileContents(): MorphMany
    {
        return $this->morphMany(FileContent::class, 'fileable');
    }

}
