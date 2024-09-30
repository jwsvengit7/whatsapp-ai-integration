<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerUtils
{
    public static function calculateDiscount($amount, $percentage): float|int
    {
        return $amount - ($amount * $percentage / 100);
    }

    /**
     * Validate the incoming customer data.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public  static function validateCustomerData(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($request->all(), [
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required|string|min:10|unique:customers,phone',
            'name'  => 'required|string|max:255',
        ]);
    }
}
