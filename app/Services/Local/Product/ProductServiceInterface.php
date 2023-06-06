<?php

namespace App\Services\Local\Product;

interface ProductServiceInterface
{
    /**
     * Save product to favorites
     *
     *
     * @throws \Exception
     */
    public function favorite(string $productId): array;
}
