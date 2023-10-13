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
        // @phpstan-ignore-next-line
        return $this->morphMany(FileContent::class, 'fileable')->image()->get();
    }

    public function audioFiles(): Collection
    {
        // @phpstan-ignore-next-line
        return $this->morphMany(FileContent::class, 'fileable')->audio()->get();
    }

    public function videoFiles(): Collection
    {
        // @phpstan-ignore-next-line
        return $this->morphMany(FileContent::class, 'fileable')->video()->get();
    }

    public function pdfs(): Collection
    {
        // @phpstan-ignore-next-line
        return $this->morphMany(FileContent::class, 'fileable')->pdf()->get();
    }

    public function spreadsheets(): Collection
    {
        // @phpstan-ignore-next-line
        return $this->morphMany(FileContent::class, 'fileable')->spreadsheet()->get();
    }

    public function documents(): Collection
    {
        // @phpstan-ignore-next-line
        return $this->morphMany(FileContent::class, 'fileable')->document()->get();
    }

    public function otherFiles(): Collection
    {
        // @phpstan-ignore-next-line
        return $this->morphMany(FileContent::class, 'fileable')->other()->get();
    }

    public function nonImageFiles(): Collection
    {
        // @phpstan-ignore-next-line
        return $this->morphMany(FileContent::class, 'fileable')->nonimage()->get();
    }

}
