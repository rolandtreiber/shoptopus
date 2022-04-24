<?php

namespace App\Repositories\Local\ProductAttribute;

interface ProductAttributeRepositoryInterface {

    /**
     * Get the products for the given product categories
     *
     * @param array $productAttributeIds
     * @return array
     */
    public function getProductIds(array $productAttributeIds = []) : array;

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
