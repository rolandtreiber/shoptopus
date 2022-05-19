<?php

namespace App\Services\Local\User;

use App\Services\Local\ModelService;
use Illuminate\Support\Facades\Config;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Repositories\Local\User\UserRepositoryInterface;

class UserService extends ModelService implements UserServiceInterface
{
    public function __construct(ErrorServiceInterface $errorService, UserRepositoryInterface $modelRepository)
    {
        parent::__construct($errorService, $modelRepository, 'user');
    }

    /**
     * Get the currently authenticated user instance
     *
     * @param bool $returnAsArray
     * @return mixed
     * @throws \Exception
     */
    public function getCurrentUser(bool $returnAsArray = true) : mixed
    {
        try {
            return $this->modelRepository->getCurrentUser($returnAsArray);
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.user.getCurrentUser'));
        }
    }

    /**
     * Get the currently authenticated user's favorited products
     *
     * @return array
     * @throws \Exception
     */
    public function favorites() : array
    {
        try {
            return $this->modelRepository->favorites();
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.user.favorites'));
        }
    }

    /**
     * Get the currently authenticated user's favorited product ids
     *
     * @return array
     * @throws \Exception
     */
    public function getFavoritedProductIds() : array
    {
        try {
            return $this->modelRepository->getFavoritedProductIds();
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.user.getFavoritedProductIds'));
        }
    }
}
