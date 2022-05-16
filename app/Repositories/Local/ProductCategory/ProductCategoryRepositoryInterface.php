<?php

namespace App\Repositories\Local\ProductCategory;

interface ProductCategoryRepositoryInterface {

    /**
     * Get the discount rules for the given product categories
     *
     * @param array $productCategoryIds
     * @return array
     */
    public function getDiscountRules(array $productCategoryIds = []) : array;

    /**
     * Get the products for the given product categories
     *
     * @param array $productCategoryIds
     * @return array
     */
    public function getProductIds(array $productCategoryIds = []) : array;

    /**
     * Get the subcategories
     *
     * @param array $productCategoryIds
     * @return array
     * @throws \Exception
     */
    public function getSubcategories(array $productCategoryIds = []) : array;

    /**
     * Get the required related models for the given parent
     *
     * @param $result
     * @param array $excludeRelationships
     * @return array
     * @throws \Exception
     */
    public function getTheResultWithRelationships($result, array $excludeRelationships = []) : array;

    /**
     * Get the columns for selection
     *
     * @param bool $withTableNamePrefix
     * @return array
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true) : array;

}
