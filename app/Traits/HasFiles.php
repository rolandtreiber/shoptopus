<?php

namespace App\Traits;

use App\Models\FileContent;
use App\Models\PaidFileContent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property Collection<FileContent> $images
 * @property Collection<PaidFileContent> $paidFileContents
 */
trait HasFiles
{
    public function deleteWithAttachments()
    {
        foreach ($this->fileContents as $fileContent) {
            $fileContent->delete();
        }
        $this->delete();
    }

    public function paidFileContents(): MorphMany
    {
        return $this->morphMany(PaidFileContent::class, 'fileable');
    }

    public function fileContents(): MorphMany
    {
        return $this->morphMany(FileContent::class, 'fileable');
    }

    public function images(): Collection
    {
        return $this->morphMany(FileContent::class, 'fileable')->image()->get();
    }

    public function audioFiles(): Collection
    {
        return $this->morphMany(FileContent::class, 'fileable')->audio()->get();
    }

    public function videoFiles(): Collection
    {
        return $this->morphMany(FileContent::class, 'fileable')->video()->get();
    }

    public function pdfs(): Collection
    {
        return $this->morphMany(FileContent::class, 'fileable')->pdf()->get();
    }

    public function spreadsheets(): Collection
    {
        return $this->morphMany(FileContent::class, 'fileable')->spreadsheet()->get();
    }

    public function documents(): Collection
    {
        return $this->morphMany(FileContent::class, 'fileable')->document()->get();
    }

    public function otherFiles(): Collection
    {
        return $this->morphMany(FileContent::class, 'fileable')->other()->get();
    }

    public function nonImageFiles(): Collection
    {
        return $this->morphMany(FileContent::class, 'fileable')->nonimage()->get();
    }

}
