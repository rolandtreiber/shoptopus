<?php

namespace App\Repositories\Local\Product;

interface ProductRepositoryInterface {

//    /**
//     * Get the discount rules for the given products
//     *
//     * @param array $productIds
//     * @return array
//     */
//    public function getDiscountRules(array $productIds = []) : array;
//
//    /**
//     * Get the product categories for the given products
//     *
//     * @param array $productIds
//     * @return array
//     * @throws \Exception
//     */
//    public function getProductCategories(array $productIds = []) : array;

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
