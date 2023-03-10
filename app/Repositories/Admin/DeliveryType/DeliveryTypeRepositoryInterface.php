<?php

namespace App\Repositories\Admin\DeliveryType;

interface DeliveryTypeRepositoryInterface
{
    public function bulkUpdateAvailability(array $ids, bool $availability): bool;

    public function bulkDelete(array $ids): bool;
}
