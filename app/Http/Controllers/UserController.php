<?php

namespace App\Http\Controllers;

use App\Helpers\HttpUtils;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class UserController extends BaseController
{

    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service=$service;
        $this->middleware('auth:api')->except(HttpUtils::URLs());

    }

    /**
     * Handle the creation of a new user.
     * @param Request $request
     * @return JsonResponse
     */
    public function createAccount(Request $request): JsonResponse
    {
        return $this->service->userSignup($request);
    }
    public function refererLink($link)
    {
        return $this->service->refererLink($link);

    }

    /**
     * Handle user login.
     * @param Request $request
     * @return JsonResponse
     */
    public function loginAuth(Request $request): JsonResponse
    {
        return $this->service->userLogin($request);
    }

    /**
     * Handle user confirm status.
     * @param Request $request
     * @return JsonResponse
     */
    public function confirmAccount(Request $request): JsonResponse
    {
        return $this->service->confirmAccount($request);
    }
    public function verifyLink($token): JsonResponse
    {
        return $this->service->verifyLink($token);
    }

    /**
     * Handle user confirm status.
     * @param Request $request
     * @return JsonResponse
     */
    public function changePassword(Request $request): JsonResponse
    {
        return $this->service->changePassword($request);
    }

    public function  forgetPassword(Request $request): JsonResponse
    {
        return $this->service->forgetPassword($request);
    }

    /**
     * Handle user confirm status.
     * @param Request $request
     * @return JsonResponse
     */
    public function resendVerification(Request $request): JsonResponse
    {
        return $this->service->resendVerification($request);
    }


    public function updateAccount(Request $request): JsonResponse
    {
        return $this->service->updateAccount($request);
    }

    /**
     * Fetch a user by ID.

     * @return JsonResponse
     */
    public function fetchUser(): JsonResponse
    {
        return $this->service->getUserById();
    }

}
