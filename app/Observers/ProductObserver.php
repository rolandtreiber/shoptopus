<?php

namespace App\Observers;

use App\Enums\UserRole;
use App\Models\FileContent;
use App\Models\Product;
use App\Models\User;
use App\Notifications\ProductOutOfStock;
use App\Notifications\ProductRunningLow;
use App\Repositories\Admin\Product\ProductRepository;
use App\Repositories\Admin\Product\ProductRepositoryInterface;
use App\Services\Local\Product\ProductService;
use App\Services\Local\Product\ProductServiceInterface;

class ProductObserver
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

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

    public function saved(Product $product) {
        if ($product->stock < 5) {
            if ($product->stock == 0) {
                $this->productRepository->triggerOutOfStockNotification($product);
            } else {
                $this->productRepository->triggerRunningLowNotification($product);
            }
        }
    }
}
