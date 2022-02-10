<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Throwable;
use App\Traits\APIControllerTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    use APIControllerTrait;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $e
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $e)
    {
        $error_message = $e instanceof \Illuminate\Validation\ValidationException
            ? $e->validator->getMessageBag()
            : $e->getMessage();

        Log::error('Exception Class:'.get_class($e).' Error:'.$error_message.' File:'.$e->getFile().' Line:'.$e->getLine());
        //Log::channel('logstash')->debug('Exception Class:'.get_class($exception).' Error:'.$exception->getMessage().' File:'.$exception->getFile().' Line:'.$exception->getLine());
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        switch (get_class($e)) {
            case AuthenticationException::class:
                $status = 401;
                break;
            case AuthorizationException::class:
            case UnauthorizedException::class:
                $status = 403;
                break;
            case ValidationException::class:
                $status = 422;
                break;
            default:
                $status = 500;
        }
        return $this->errorResponse($e, __("error_messages.0000"), null, $status);
    }
}
