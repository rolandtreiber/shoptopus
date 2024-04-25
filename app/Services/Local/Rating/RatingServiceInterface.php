<?php

namespace App\Services\Local\Rating;

use App\Models\Product;

interface RatingServiceInterface
{
    public function getRatingsForProduct(Product $product, array $ratings, string $languagePrefix, array $page_formatting = [], $filters = []): array;
}
