<?php

namespace App\Repositories\Admin\Interfaces;

interface EventLogRepositoryInterface
{
    public function create(string $modelClass,
                           $model,
                           int $type,
                           $data);
}
