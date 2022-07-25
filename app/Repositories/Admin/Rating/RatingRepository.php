<?php

namespace App\Repositories\Admin\Rating;

use Illuminate\Support\Facades\DB;

class RatingRepository implements RatingRepositoryInterface
{

    /**
     * @param array $ids
     * @param bool $verifiedStatus
     * @return bool
     */
    public function bulkUpdateAVerifiedStatus(array $ids, bool $verifiedStatus): bool
    {
        try {
            DB::table('ratings')->whereIn('id', $ids)->update(['verified' => $verifiedStatus]);
            return true;
        } catch(\Exception $exception) {
            return false;
        }
    }

    /**
     * @param array $ids
     * @param bool $availability
     * @return bool
     */
    public function bulkUpdateAvailability(array $ids, bool $availability): bool
    {
        try {
            DB::table('ratings')->whereIn('id', $ids)->update(['enabled' => $availability]);
            return true;
        } catch(\Exception $exception) {
            return false;
        }
    }
}
