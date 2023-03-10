<?php

namespace App\Traits;

use App\Models\FileContent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasFiles
{
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
     * @return Collection
     */
    public function images(): Collection
    {
        return $this->morphMany(FileContent::class, 'fileable')->image()->get();
    }

    /**
     * @return Collection
     */
    public function audioFiles(): Collection
    {
        return $this->morphMany(FileContent::class, 'fileable')->audio()->get();
    }

    /**
     * @return Collection
     */
    public function videoFiles(): Collection
    {
        return $this->morphMany(FileContent::class, 'fileable')->video()->get();
    }

    /**
     * @return Collection
     */
    public function pdfs(): Collection
    {
        return $this->morphMany(FileContent::class, 'fileable')->pdf()->get();
    }

    /**
     * @return Collection
     */
    public function spreadsheets(): Collection
    {
        return $this->morphMany(FileContent::class, 'fileable')->spreadsheet()->get();
    }

    /**
     * @return Collection
     */
    public function documents(): Collection
    {
        return $this->morphMany(FileContent::class, 'fileable')->document()->get();
    }

    /**
     * @return Collection
     */
    public function otherFiles(): Collection
    {
        return $this->morphMany(FileContent::class, 'fileable')->other()->get();
    }
}
