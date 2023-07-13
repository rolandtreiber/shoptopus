<?php

namespace App\Observers;

use App\Models\FileContent;
use App\Models\Product;
use App\Traits\ProcessRequest;
use Illuminate\Support\Facades\DB;

class FileContentObserver
{
    use ProcessRequest;

    public function deleting(FileContent $fileContent): void
    {
        $this->deleteCurrentFile($fileContent->file_name);

        if ($fileContent->fileable_type === Product::class) {
            $product = Product::find($fileContent->fileable_id);
            if ($product->cover_photo && $product->cover_photo->url === $fileContent->url) {
                DB::table('products')->where('id', $fileContent->fileable_id)->update(['cover_photo' => null]);
            }
        }
    }
}
