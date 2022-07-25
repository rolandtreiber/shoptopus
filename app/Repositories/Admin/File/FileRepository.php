<?php

namespace App\Repositories\Admin\File;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FileRepository implements FileRepositoryInterface
{

    /**
     * @param array $ids
     * @return bool
     */
    public function bulkDelete(array $ids): bool
    {
        try {
            DB::table('file_contents')->whereIn('id', $ids)->delete();
            return true;
        } catch(\Exception $exception) {
            return false;
        }

    }
}
