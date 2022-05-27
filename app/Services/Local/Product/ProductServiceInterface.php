<?php

namespace App\Services\Local\Product;

interface ProductServiceInterface {

    /**
     * Save product to favorites
     *
     * @param string $productId
     * @return array
     * @throws \Exception
     */
    public function favorite(string $productId) : array;

}
