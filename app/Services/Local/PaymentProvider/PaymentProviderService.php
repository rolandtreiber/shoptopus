<?php

namespace App\Services\Local\PaymentProvider;

use App\Repositories\Local\PaymentProvider\PaymentProviderRepositoryInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\ModelService;

class PaymentProviderService extends ModelService implements PaymentProviderServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, PaymentProviderRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'payment_provider');
    }
}
