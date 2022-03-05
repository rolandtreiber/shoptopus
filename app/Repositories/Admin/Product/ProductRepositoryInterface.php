<?php

namespace App\Repositories\Admin\Product;

interface ProductRepositoryInterface {

    public function bulkDelete(array $ids): bool;
    public function bulkArchive(array $ids): bool;

}
