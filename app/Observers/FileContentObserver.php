<?php

namespace App\Observers;

use App\Models\FileContent;
use App\Models\Product;
use App\Models\ProductVariant;
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

    public function creating(FileContent $fileContent): void
    {
        if ($fileContent->fileable_type === ProductVariant::class) {
            $fileContent->product_id = $fileContent->fileable->product_id;
        }
        if ($fileContent->fileable_type === Product::class) {
            $fileContent->product_id = $fileContent->fileable->id;
        }
    }

}
