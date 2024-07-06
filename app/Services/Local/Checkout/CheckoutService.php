<?php

namespace App\Services\Local\Checkout;

use App\Repositories\Local\Checkout\CheckoutRepositoryInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\ModelService;
use Exception;
use Illuminate\Support\Facades\Config;

class CheckoutService extends ModelService implements CheckoutServiceInterface
{
    private CheckoutRepositoryInterface $checkoutRepository;
    public function __construct(ErrorServiceInterface $errorService, CheckoutRepositoryInterface $checkoutRepository)
    {
        parent::__construct($errorService, $checkoutRepository, 'order');
        $this->checkoutRepository = $checkoutRepository;
    }


    /**
     * @throws Exception
     */
    public function createPendingOrderFromCart(array $payload): array
    {
        try {
            return $this->checkoutRepository->createPendingOrderFromCart($payload);
        } catch (Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new Exception($e->getMessage(), Config::get('api_error_codes.services.checkout.getPendingOrderFromCart'));
        }
    }

    /**
     * @throws Exception
     */
    public function revertOrder(array $payload): array
    {
        try {
            return $this->checkoutRepository->revertOrder($payload);
        } catch (Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new Exception($e->getMessage(), Config::get('api_error_codes.services.checkout.revertOrder'));
        }
    }

    /**
     * @throws Exception
     */
    public function getAvailableDeliveryTypesForAddress(array $payload): array
    {
        try {
            return $this->checkoutRepository->getAvailableDeliveryTypesForAddress($payload);
        } catch (Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new Exception($e->getMessage(), Config::get('api_error_codes.services.checkout.getAvailableDeliveryTypesForAddress'));
        }
    }

}
