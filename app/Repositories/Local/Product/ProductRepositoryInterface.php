<?php

namespace App\Repositories\Local\Product;

use App\Models\Product;

interface ProductRepositoryInterface
{
    /**
     * Save product to favorites
     *
     * @param  string  $productId
     * @return array
     *
     * @throws \Exception
     */
    public function favorite(string $productId): array;

    /**
     * Get the product attributes for the given products
     *
     * @param  array  $productIds
     * @return array
     *
     * @throws \Exception
     */
    public function getProductAttributes(array $productIds = []): array;

    /**
     * Get the discount rules for the given products
     *
     * @param  array  $productIds
     * @return array
     */
    public function getDiscountRules(array $productIds = []): array;

    /**
     * Get the product categories for the given products
     *
     * @param  array  $productIds
     * @return array
     */
    public function getProductCategories(array $productIds = []): array;

    /**
     * Get the product tags for the given products
     *
     * @param  array  $productIds
     * @return array
     */
    public function getProductTags(array $productIds = []): array;

    /**
     * Get the product variants
     *
     * @param  array  $productIds
     * @return array
     *
     * @throws \Exception
     */
    public function getProductVariants(array $productIds = []): array;

    /**
     * Get the required related models for the given parent
     *
     * @param $result
     * @param  array  $excludeRelationships
     * @return array
     */
    public function getTheResultWithRelationships($result, array $excludeRelationships = []): array;

    /**
     * Get the columns for selection
     *
     * @param  bool  $withTableNamePrefix
     * @return array
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true): array;

    /**
     * Calculate the final price
     *
     * @param  array  $product
     * @param  bool  $price
     * @return string
     */
    public function calculateFinalPrice(array $product, $price = false): string;

    /**
     * @param  Product  $product
     * @param  array  $selectedAttributeOptionIds
     * @return mixed
     */
    public function getAvailableAttributeOptions(Product $product, array $selectedAttributeOptionIds = []);
}
