<?php

namespace App\Repositories\Admin\Rating;

interface RatingRepositoryInterface {

    public function bulkUpdateAVerifiedStatus(array $ids, bool $verifiedStatus): bool;
    public function bulkUpdateAvailability(array $ids, bool $availability): bool;

}
