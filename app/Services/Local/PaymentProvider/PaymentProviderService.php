<?php

namespace App\Services\Local\PaymentProvider;

use App\Services\Local\ModelService;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Repositories\Local\PaymentProvider\PaymentProviderRepositoryInterface;

class PaymentProviderService extends ModelService implements PaymentProviderServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, PaymentProviderRepositoryInterface $modelRepository) {
        parent::__construct($errorService, $modelRepository, 'payment_provider');
    }
}
