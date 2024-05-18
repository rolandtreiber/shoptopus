<?php

namespace App\Services\Local\Product;

use Illuminate\Database\Eloquent\Collection;

interface ProductServiceInterface
{
    /**
     * Save product to favorites
     *
     *
     * @throws \Exception
     */
    public function favorite(string $productId): array;

    public function getAll(array $page_formatting = [], array $filters = [], array $excludeRelationships = []): array;

    public function get($value, string $key = 'id', array $excludeRelationships = []): array;

    public function getBySlug(string $slug): array;

    public function saveReview(string $productId, array $data): array;

    public function search(string $search, array $pageFormatting = []): array;

}
