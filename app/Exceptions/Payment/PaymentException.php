<?php

namespace App\Exceptions\Payment;

use Throwable;

class PaymentException extends \Exception
{
    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Flare, Sentry, Bugsnag, etc.
     */
    public function report(Throwable $exception): void
    {
        // @phpstan-ignore-next-line
        parent::report($exception);
    }
}
