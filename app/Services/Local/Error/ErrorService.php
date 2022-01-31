<?php

namespace App\Services\Local\Error;

use Illuminate\Support\Facades\Log;

class ErrorService implements ErrorServiceInterface
{
    public function __construct() {
        //ready for injections
    }

    /**
     * Log an exception
     *
     * @param $exception
     * @param bool $critical
     * @todo - write critical error code
     */
    public function logException($exception, bool $critical = false) {
        Log::error('Exception Class:'.get_class($exception).' Error:'.$exception->getMessage().' File:'.$exception->getFile().' Line:'.$exception->getLine());
        //Log::channel('logstash')->debug('Exception Class:'.get_class($exception).' Error:'.$exception->getMessage().' File:'.$exception->getFile().' Line:'.$exception->getLine());
        if($critical) {
            $this->alertCriticalError($exception);
        }
    }


    private function alertCriticalError($exception) {
        //@todo
        //so something with critical errors, send emails, post to slack, sms? etc..
        //pull in notifications system for multiple channels
    }
}
