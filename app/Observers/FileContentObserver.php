<?php

namespace App\Observers;

use App\Models\FileContent;
use Illuminate\Support\Facades\Storage;

class FileContentObserver
{
    /**
     * @param FileContent $fileContent
     */
    public function deleting(FileContent $fileContent): void
    {
        if (env('APP_ENV') === 'local') {
            Storage::disk('uploads')->delete($fileContent->file_name);
        } else {
            Storage::disk('uploads')->delete($fileContent->file_name);
        }
    }
}
