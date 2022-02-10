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
     * @return null|mixed
     * @throws \Exception
     */
    public function getCurrentUser(bool $returnAsArray = true)
    {
        try {
            return $this->modelRepository->getCurrentUser($returnAsArray);
        } catch (\Exception $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.user.getCurrentUser'));
        } catch (\Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.user.getCurrentUser'));
        }
    }
}
