<?php

namespace App\Observers;

use App\Models\ProductTag;
use App\Traits\ProcessRequest;

class ProductTagObserver
{
    use ProcessRequest;

    public function deleting(ProductTag $productTag): void
    {
        $productTag->badge && $this->deleteCurrentFile($productTag->badge->file_name);
    }
}
