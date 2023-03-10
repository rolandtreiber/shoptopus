<?php

namespace App\Services\Local\ProductAttribute;

interface ProductAttributeServiceInterface
{
    /**
     * Get all models for a specific product category
     *
     * @param  string  $product_category_id
     * @param  array  $page_formatting
     * @return array
     *
     * @throws \Exception
     */
    public function getAllForProductCategory(string $product_category_id, array $page_formatting = []): array;
}
