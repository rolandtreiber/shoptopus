<?php

namespace App\Services\Local\Product;

use App\Services\Local\ModelService;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Repositories\Local\Product\ProductRepositoryInterface;

class ProductService extends ModelService implements ProductServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, ProductRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'product');
    }
}
