<?php

namespace App\Repositories\Local\ProductTag;

interface ProductTagRepositoryInterface
{

    public function getTheResultWithRelationships($result, array $excludeRelationships = []): array;
    public function getSelectableColumns(bool $withTableNamePrefix = true): array;

}
