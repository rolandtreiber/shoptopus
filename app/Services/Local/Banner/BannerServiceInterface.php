<?php

namespace App\Services\Local\Banner;

interface BannerServiceInterface
{
    public function getAll(array $page_formatting = [], array $filters = [], array $excludeRelationships = []): array;

}
