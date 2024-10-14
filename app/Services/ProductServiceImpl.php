<?php

namespace App\Services;

use App\Helpers\CustomerUtils;
use App\Helpers\ResponseUtils;
use App\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProductServiceImpl implements ProductService
{

    public function fetchProduct(): \Illuminate\Http\JsonResponse
    {
        try {
            $user= CustomerUtils::getJWTUser();
            if($user==null){
                return ResponseUtils::respondWithError("User not found.", 404);
            }
            $product = Product::all();
            return ResponseUtils::respondWithSuccess(
                $product, Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {
            return ResponseUtils::respondWithError('User not found',
                Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            Log::error('Failed to fetch User: ' . $e->getMessage());
            return ResponseUtils::respondWithError('An error occurred while fetching the User',
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addProduct(\Illuminate\Http\Request $request)
    {

    }
}
