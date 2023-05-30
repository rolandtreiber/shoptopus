<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class ApiValidationFailedException extends Exception
{
    protected $message;

    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request): JsonResponse
    {
        $message = json_decode($this->message);
        if (! $message) {
            $message = $this->message;
        }
        $result = [
            'error' => [
                'type' => 'Invalid Parameters',
                'details' => $message,
            ],
        ];

        return response()->json($result, 422);
    }
}
