<?php

namespace App\Http\Controllers;

use App\Helpers\HttpUtils;
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
     *
     * Handle the creation of a new user.
     * @param Request $request
     * @return JsonResponse
     */
    public function createProduct(Request $request): JsonResponse
    {
        return $this->service->createProduct($request);
    }
    public function createAdmin(Request $request): JsonResponse
    {
        return $this->service->createAdmin($request);
    }


    public function fetchAllUsers(): JsonResponse
    {
        return $this->service->fetchAllUsers();
    }

    /**
     * Fetch a Conversation by ID.

     * @return JsonResponse
     */
    public function fetchConversation(Request $request): JsonResponse
    {
        return $this->service->fetchConversation($request);
    }


}
