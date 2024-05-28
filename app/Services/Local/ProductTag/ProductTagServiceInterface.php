<?php

namespace App\Services\Local\ProductTag;

interface ProductTagServiceInterface
{
    public function getAll(array $page_formatting = [], array $filters = [], array $excludeRelationships = []): array;

    public function get($value, string $key = 'id', array $excludeRelationships = []): array;

}
