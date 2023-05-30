<?php

namespace App\Repositories\Local\Product;

use App\Models\Product;

interface ProductRepositoryInterface
{
    /**
     * Save product to favorites
     *
     *
     * @throws \Exception
     */
    public function favorite(string $productId): array;

    /**
     * Get the product attributes for the given products
     *
     *
     * @throws \Exception
     */
    public function getProductAttributes(array $productIds = []): array;

    /**
     * Get the discount rules for the given products
     */
    public function getDiscountRules(array $productIds = []): array;

    /**
     * Get the product categories for the given products
     */
    public function getProductCategories(array $productIds = []): array;

    /**
     * Get the product tags for the given products
     */
    public function getProductTags(array $productIds = []): array;

    /**
     * Get the product variants
     *
     *
     * @throws \Exception
     */
    public function getProductVariants(array $productIds = []): array;

    /**
     * Get the required related models for the given parent
     */
    public function getTheResultWithRelationships($result, array $excludeRelationships = []): array;

    /**
     * Get the columns for selection
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true): array;

    /**
     * Calculate the final price
     *
     * @param  bool  $price
     */
    public function calculateFinalPrice(array $product, $price = false): string;

    /**
     * @return mixed
     */
    public function getAvailableAttributeOptions(Product $product, array $selectedAttributeOptionIds = []);
}
