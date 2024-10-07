<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class CustomerController extends BaseController {
    /**
     * @var CustomerService
     */
    private CustomerService $customerService;
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Handle the creation of a new user.
     *
     * @param Request $request
     * @return JsonResponse
     */

    public function createCustomer(Request $request): JsonResponse
    {
        return $this->customerService->customerCreationImpl($request);
    }

    public function fetchCustomer($id): JsonResponse
    {
        return $this->customerService->getCustomerById($id);
    }


}
