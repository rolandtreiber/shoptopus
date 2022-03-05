<?php

namespace App\Repositories\Admin\Banner;

interface BannerRepositoryInterface {

    public function bulkUpdateAvailability(array $ids, bool $availability): bool;

}
