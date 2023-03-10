<?php

namespace App\Repositories\Admin\Eventlog;

interface EventLogRepositoryInterface
{
    public function create(string $modelClass,
        $model,
        int $type,
        $data);
}
