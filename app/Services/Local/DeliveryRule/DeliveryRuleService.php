<?php

namespace App\Services\Local\DeliveryRule;

use App\Services\Local\ModelService;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Repositories\Local\DeliveryRule\DeliveryRuleRepositoryInterface;

class DeliveryRuleService extends ModelService implements DeliveryRuleServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, DeliveryRuleRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'delivery_rule');
    }
}
