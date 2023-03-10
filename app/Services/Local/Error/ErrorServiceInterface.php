<?php

namespace App\Services\Local\Error;

interface ErrorServiceInterface
{
    /**
     * Log an exception
     *
     * @param  \Exception  $exception
     * @param  bool  $critical
     */
    public function logException(\Exception $exception, bool $critical = false);
}
