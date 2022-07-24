<?php

namespace App\Repositories\Admin\Product;

use App\Models\Product;

interface ProductRepositoryInterface {

    public function bulkDelete(array $ids): bool;
    public function bulkArchive(array $ids): bool;
    public function triggerRunningLowNotification(Product $product): bool;
    public function triggerOutOfStockNotification(Product $product): bool;

}
