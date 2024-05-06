<?php

namespace App\Services\Local\User;

use App\Enums\RandomStringMode;
use App\Helpers\GeneralHelper;
use App\Http\Resources\Admin\UserDetailResource;
use App\Mail\UserAccountSuccessfullyDeactivatedEmail;
use App\Models\User;
use App\Repositories\Local\User\UserRepositoryInterface;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\ModelService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

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

    public function deleteAccount(User $user, bool $anonimize = false): void
    {
        if ($anonimize) {
            do {
                $emailReplacement = GeneralHelper::generateRandomString(20, RandomStringMode::UppercaseAndLowecase) . "@deactivated.com";
                $firstNameReplacement = GeneralHelper::generateRandomString(10, RandomStringMode::UppercaseAndLowecase);
                $lastNameReplacement = GeneralHelper::generateRandomString(10, RandomStringMode::UppercaseAndLowecase);
                $phoneReplacement = GeneralHelper::generateRandomString(10, RandomStringMode::UppercaseAndLowecase);
            } while (User::where('email', $emailReplacement)->first() !== null);
            Mail::to($user->email)->send(new UserAccountSuccessfullyDeactivatedEmail(
                $anonimize,
                $user->email,
                $user->first_name,
                $user->last_name,
                $user->phone,
                $emailReplacement,
                $firstNameReplacement,
                $lastNameReplacement,
                $phoneReplacement
            ));
            $user->email = $emailReplacement;
            $user->first_name = $firstNameReplacement;
            $user->last_name = $lastNameReplacement;
            $user->phone = $phoneReplacement;
        } else {
            Mail::to($user->email)->send(new UserAccountSuccessfullyDeactivatedEmail(
                $anonimize,
                $user->email,
                $user->first_name,
                $user->last_name,
                $user->phone
            ));
            $uniqueRandomPart = GeneralHelper::generateRandomString(10, RandomStringMode::UppercaseAndLowecase);
            $user->email = "DEACTIVATED-".$uniqueRandomPart."-".$user->email;
        }
        $user->save();
        $user->delete();
    }
}
