<?php

namespace App\Http\Controllers;

use App\Helpers\HttpUtils;
use App\Models\Product;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class AdminController extends BaseController
{

    protected AdminService $service;

    public function __construct(AdminService $service)
    {
        $this->service=$service;
        $this->middleware('auth:api')->except(HttpUtils::URLs());
    }

    /**
     * Handle the creation of a new user.
     * @param Request $request
     * @return JsonResponse
     */
    public function createProduct(Request $request): JsonResponse
    {
        return $this->service->createProduct($request);
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
