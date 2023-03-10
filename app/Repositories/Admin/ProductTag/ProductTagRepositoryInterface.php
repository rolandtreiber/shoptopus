<?php

namespace App\Repositories\Admin\ProductTag;

interface ProductTagRepositoryInterface
{
    public function bulkUpdateAvailability(array $ids, bool $availability): bool;

    public function bulkDelete(array $ids): bool;
}
