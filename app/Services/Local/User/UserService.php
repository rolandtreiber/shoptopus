<?php

namespace App\Services\Local\User;

use App\Repositories\Local\User\UserRepositoryInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\ModelService;
use Illuminate\Support\Facades\Config;

class UserService extends ModelService implements UserServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, UserRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'user');
    }

    /**
     * Get the currently authenticated user instance
     *
     *
     * @throws \Exception
     */
    public function getCurrentUser(bool $returnAsArray = true): mixed
    {
        try {
            return $this->modelRepository->getCurrentUser($returnAsArray);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.user.getCurrentUser'));
        }
    }

    /**
     * Get the currently authenticated user's favorited products
     *
     *
     * @throws \Exception
     */
    public function favorites(): array
    {
        try {
            return $this->modelRepository->favorites();
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.user.favorites'));
        }
    }

    /**
     * Get the currently authenticated user's favorited product ids
     *
     *
     * @throws \Exception
     */
    public function getFavoritedProductIds(): array
    {
        try {
            return $this->modelRepository->getFavoritedProductIds();
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.user.getFavoritedProductIds'));
        }
    }

    /**
     * @throws \Exception
     */
    public function getAccountDetails(): array
    {
        return $this->getCurrentUser(true);
    }
}
