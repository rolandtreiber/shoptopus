<?php

namespace App\Repositories\Local\Banner;

interface BannerRepositoryInterface
{
    public function getAll(array $page_formatting = [], array $filters = [], array $excludeRelationships = []): array;

}
