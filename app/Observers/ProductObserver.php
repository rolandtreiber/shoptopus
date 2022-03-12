<?php

namespace App\Observers;

use App\Models\FileContent;
use App\Models\Product;

class ProductObserver
{

    /**
     * @param Product $product
     * @return void
     */
    public function saving(Product $product)
    {
        /** @var FileContent $firstImage */
        $firstImage = $product->images()->first();
        if ($firstImage && (!$product->cover_photo || $product->cover_photo->file_name !== $firstImage->file_name)) {
            $product->cover_photo = [
                'file_name' => $firstImage->file_name,
                'url' => $firstImage->url
            ];
            $product->save();
        }
    }

}
