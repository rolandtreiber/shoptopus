<?php

namespace App\Repositories\Admin\ProductAttribute;

interface ProductAttributeRepositoryInterface {

    public function bulkUpdateAvailability(array $ids, bool $availability): bool;
    public function bulkDelete(array $ids): bool;

}
