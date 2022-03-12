<?php

namespace App\Observers;

use App\Models\FileContent;
use App\Models\Product;
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

        if ($fileContent->fileable_type === Product::class) {
            $product = Product::find($fileContent->fileable_id);
            if ($product->cover_photo && $product->cover_photo->url === $fileContent->url) {
                $product->cover_photo = null;
                $product->save();
            }
        }
    }
}
