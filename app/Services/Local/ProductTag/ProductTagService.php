<?php

namespace App\Services\Local\ProductTag;

use App\Repositories\Local\ProductTag\ProductTagRepositoryInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\ModelService;

class ProductTagService extends ModelService implements ProductTagServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, ProductTagRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'product_tag');
    }

}