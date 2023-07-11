<?php

namespace App\Services\Local\ProductCategory;

interface ProductCategoryServiceInterface
{
    public function getAll(array $page_formatting = [], array $filters = [], array $excludeRelationships = []): array;

    public function get($value, string $key = 'id', array $excludeRelationships = []): array;

    public function getBySlug(string $slug): array;
}
