<?php

namespace App\Services\Local\Error;

interface ErrorServiceInterface
{
    /**
     * Log an exception
     */
    public function logException(\Exception $exception, bool $critical = false);
}
