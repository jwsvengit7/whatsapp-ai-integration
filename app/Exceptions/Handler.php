<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        // Your exception classes here
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @return ResponseAlias
     * @throws \Throwable
     */
    public function render($request, Exception|\Throwable $e): ResponseAlias
    {
        // Check if the request expects a JSON response
        if ($request->expectsJson()) {
            // Customize the response based on exception type
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() ?: 'An error occurred.',
            ], $this->getHttpStatusCode($e));
        }

        // Default to the parent render method for other types of requests
        return parent::render($request, $e);
    }

    /**
     * Get the HTTP status code for the exception.
     *
     * @param Exception $exception
     * @return int
     */
    protected function getHttpStatusCode(Exception $exception): int
    {
        // Return appropriate HTTP status codes based on the exception
        if ($exception instanceof ModelNotFoundException) {
            return ResponseAlias::HTTP_NOT_FOUND;
        }

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            return $exception->getStatusCode();
        }

        // Return a generic server error for all other exceptions
        return ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
    }
}
