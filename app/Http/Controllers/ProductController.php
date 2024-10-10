<?php

namespace App\Http\Controllers;

use App\Helpers\HttpUtils;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class ProductController extends BaseController
{

    protected ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service=$service;
        $this->middleware('auth:api')->except(HttpUtils::URLs());
    }

    /**
     * Handle the creation of a new user.
     * @param Request $request
     * @return JsonResponse
     */
    public function addProduct(Request $request): JsonResponse
    {
        return $this->service->addProduct($request);
    }

    /**
     * Handle user login.
     * @return JsonResponse
     */
    public function fetchProduct(): JsonResponse
    {
        return $this->service->fetchProduct();
    }


}
