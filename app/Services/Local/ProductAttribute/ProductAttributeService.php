<?php

namespace App\Services\Local\ProductAttribute;

use App\Services\Local\ModelService;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Repositories\Local\ProductAttribute\ProductAttributeRepositoryInterface;

class ProductAttributeService extends ModelService implements ProductAttributeServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, ProductAttributeRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'product_attribute');
    }
}
