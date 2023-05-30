<?php

namespace App\Repositories\Local\ProductCategory;

interface ProductCategoryRepositoryInterface
{
    /**
     * Get the discount rules for the given product categories
     */
    public function getDiscountRules(array $productCategoryIds = []): array;

    /**
     * Get the products for the given product categories
     */
    public function getProductIds(array $productCategoryIds = []): array;

    /**
     * Get the subcategories
     *
     *
     * @throws \Exception
     */
    public function getSubcategories(array $productCategoryIds = []): array;

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
