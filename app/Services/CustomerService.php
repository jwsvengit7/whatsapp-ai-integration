<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface CustomerService
{

    public  function customerCreationImpl(Request $request): JsonResponse;
    public function getCustomerById($id) :JsonResponse;

}
