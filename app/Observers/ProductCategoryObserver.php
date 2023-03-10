<?php

namespace App\Observers;

use App\Models\ProductCategory;
use App\Traits\ProcessRequest;

class ProductCategoryObserver
{
    use ProcessRequest;

    /**
     * @param  ProductCategory  $productCategory
     */
    public function deleting(ProductCategory $productCategory): void
    {
        $productCategory->menu_image && $this->deleteCurrentFile($productCategory->menu_image->file_name);
        $productCategory->header_image && $this->deleteCurrentFile($productCategory->header_image->file_name);
        $children = ProductCategory::where('parent_id', $productCategory->id)->get();
        foreach ($children as $child) {
            $child->delete();
        }
    }

    public function saved(ProductCategory $productCategory): void
    {
        if ($productCategory->enabled === false) {
            $productCategory->children->map(function ($c) {
                $c->enabled = false;
                $c->save();
            });
        } elseif ($productCategory->parent && $productCategory->parent->enabled === false) {
            $productCategory->parent->enabled = true;
            $productCategory->parent->save();
        }
    }
}
