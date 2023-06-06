<?php

namespace App\Repositories\Local\ProductAttribute;

interface ProductAttributeRepositoryInterface
{
    /**
     * Get all models for a specific product category
     */
    public function getAllForProductCategory(string $product_category_id, array $page_formatting = []): array;

    /**
     * Get the products for the given product categories
     */
    public function getProductIds(array $productAttributeIds = []): array;

    /**
     * Get the required related models for the given parent
     *
     *
     * @throws \Exception
     */
    public function getTheResultWithRelationships($result, array $excludeRelationships = []): array;

    /**
     * Get the columns for selection
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true): array;
}
