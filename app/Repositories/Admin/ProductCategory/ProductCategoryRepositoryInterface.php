<?php

namespace App\Repositories\Admin\ProductCategory;

interface ProductCategoryRepositoryInterface {

    public function bulkUpdateAvailability(array $ids, bool $availability): bool;
    public function bulkDelete(array $ids): bool;

}
