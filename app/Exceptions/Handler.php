<?php

namespace App\Exceptions;


use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() ?: 'An error occurred.',
            ], $this->getHttpStatusCode($e));
        }

        return parent::render($request, $e->getMessage());
    }

    /**
     * Get the HTTP status code for the exception.
     *
     * @param Exception $exception
     * @return int
     */
    protected function getHttpStatusCode(Exception $exception): int
    {
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
