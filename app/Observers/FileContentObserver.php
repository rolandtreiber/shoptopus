<?php

namespace App\Observers;

use App\Models\FileContent;
use App\Traits\ProcessRequest;

class FileContentObserver
{
    use ProcessRequest;

    /**
     * @param FileContent $fileContent
     */
    public function deleting(FileContent $fileContent): void
    {
        $this->deleteCurrentFile($fileContent->file_name);
    }
}
