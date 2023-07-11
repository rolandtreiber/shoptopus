<?php

namespace App\Services\Local\ProductAttribute;

interface ProductAttributeServiceInterface
{
    /**
     * Get all models for a specific product category
     *
     *
     * @throws \Exception
     */
    public function getAllForProductCategory(string $product_category_id, array $page_formatting = []): array;

    public function getAll(array $page_formatting = [], array $filters = [], array $excludeRelationships = []): array;

    public function get($value, string $key = 'id', array $excludeRelationships = []): array;

}
