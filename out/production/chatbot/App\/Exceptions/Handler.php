<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;

class Handler extends ExceptionHandler
{
    public function render($request, Exception|\Throwable $exception): \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        if ($exception instanceof AuthenticationException) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized: Token has expired'], 401);
            }
        }

        return parent::render($request, $exception);
    }
}
