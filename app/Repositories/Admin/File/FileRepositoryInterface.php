<?php

namespace App\Repositories\Admin\File;

interface FileRepositoryInterface {

    public function bulkDelete(array $ids): bool;

}
