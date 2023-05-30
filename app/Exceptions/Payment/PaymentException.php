<?php

namespace App\Exceptions\Payment;

use Throwable;

class PaymentException extends \Exception
{
    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Flare, Sentry, Bugsnag, etc.
     *
     * @return void
     */
    public function report(Throwable $exception): void
    {
        parent::report($exception);
    }
}
