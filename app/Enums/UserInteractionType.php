<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class UserInteractionType extends Enum
{
    const Login = 0;
    const Logout = 1;
    const Signup = 2;
    const EmailVerified = 3;
    const AddedItemToCart = 4;
    const RemovedItemFromCart = 4;
    const CheckoutSuccess = 5;
    const CheckoutFail = 6;
    const Browse = 7;
    const FavouritedProduct = 8;
    const UnfavouritedProduct = 9;
    const EmptiedCart = 10;

}
