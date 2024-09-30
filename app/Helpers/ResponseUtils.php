<?php

namespace App\Helpers;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseUtils
{

    /**
     * Return a JSON success response.
     *
     * @param  $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function respondWithSuccess($message, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
        ], $statusCode);
    }

    /**
     * Return a JSON error response.
     *
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function respondWithError(string $message, int $statusCode): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
        ], $statusCode);
    }



    /**
     * Return a JSON response for validation errors.
     *
     * @param Validator $validator
     * @return JsonResponse
     */
    public static function respondWithValidationErrors(Validator $validator): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }


}
