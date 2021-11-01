<?php

namespace App\Traits;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\FileContent;
use Illuminate\Support\Facades\DB;

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

    /**
     * @return string|null
     */
    public function coverImage(): ?string
    {
        $imagesCount = DB::table('file_contents')->where('fileable_type', get_class($this))->where('fileable_id', $this->id)->count();
        if ($imagesCount) {
            $img = FileContent::where('fileable_type', ProductVariant::class)->where('fileable_id', $this->id)->first();
            return $img->url;
        } else {
            return null;
        }
    }

}
