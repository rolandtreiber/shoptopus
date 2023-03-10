<?php

namespace App\Services\Local\ProductCategory;

use App\Repositories\Local\ProductCategory\ProductCategoryRepositoryInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\ModelService;

class ProductCategoryService extends ModelService implements ProductCategoryServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, ProductCategoryRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'product_category');
    }
}
