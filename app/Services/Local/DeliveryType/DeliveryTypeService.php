<?php

namespace App\Services\Local\DeliveryType;

use App\Services\Local\ModelService;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Repositories\Local\DeliveryType\DeliveryTypeRepositoryInterface;

class DeliveryTypeService extends ModelService implements DeliveryTypeServiceInterface
{

    public function __construct(ErrorServiceInterface $errorService, DeliveryTypeRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'delivery_type');
    }
}
