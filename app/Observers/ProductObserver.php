<?php

namespace App\Observers;

use App\Models\FileContent;
use App\Models\Product;
use App\Repositories\Admin\Product\ProductRepository;
use App\Repositories\Admin\Product\ProductRepositoryInterface;

class ProductObserver
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function saving(Product $product): void
    {
        /** @var FileContent|null $firstImage */
        $firstImage = $product->images()->first();
        if ($firstImage && (! $product->cover_photo || $product->cover_photo->file_name !== $firstImage->file_name)) {
            $product->cover_photo = [
                'file_name' => $firstImage->file_name,
                'url' => $firstImage->url,
            ];
            $product->save();
        }
    }

    public function saved(Product $product)
    {
        if ($product->stock < 5) {
            if ($product->stock == 0) {
                $this->productRepository->triggerOutOfStockNotification($product);
            } else {
                $this->productRepository->triggerRunningLowNotification($product);
            }
        }
    }
}
