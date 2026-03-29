<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
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

    public function render($request, Throwable $e)
    {
        // If the request expects JSON (e.g., API requests), return a JSON response
        if ($request->expectsJson()) {
            $status = 500;
            if ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
            }
            if ($e instanceof ModelNotFoundException) {
                $status = 404;
            }
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => $status,
                    'message' => $this->getJsonMessage($status) ?: 'Server Error'
                ]
            ], $status);
        }
        // For non-JSON requests, use the default rendering (HTML error pages)
        return parent::render($request, $e);
    }

    /**
     * Get a user-friendly error message for a given HTTP status code.
     *
     * @param int $status
     * @return string
     */
    protected function getJsonMessage($status)
    {
        $messages = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            422 => 'Unprocessable Entity',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            503 => 'Service Unavailable',
        ];
        return $messages[$status] ?? 'An error occurred.';
    }
}
