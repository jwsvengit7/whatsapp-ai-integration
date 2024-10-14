<?php

namespace App\Helpers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Random\RandomException;

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
    public  static function validateUserData(Request $request): \Illuminate\Contracts\Validation\Validator
    {

        return Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|min:10|unique:users,phone',
            'name'  => 'required|string|max:255',
            'role'  => ['required', function ($attribute, $value, $fail) {
                if (!in_array($value, UserRole::getValues())) {
                    $fail("The $attribute must be a valid role.");
                }
            }],
            'password' => [
                'required',
                'string',
                'regex:/[a-z]/',               // At least one lowercase letter
                'regex:/[A-Z]/',               // At least one uppercase letter
                'regex:/[0-9]/',               // At least one number

            ],
        ]);
    }
    public  static function validateAdminData(Request $request): \Illuminate\Contracts\Validation\Validator
    {

        return Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|min:10|unique:users,phone',
            'name'  => 'required|string|max:255',
            'address'  => 'required|string|max:999',
            'role'  => ['required', function ($attribute, $value, $fail) {
                if (!in_array($value, UserRole::getValues())) {
                    $fail("The $attribute must be a valid role.");
                }
            }],
            'password' => [
                'required',
                'string',
                'regex:/[a-z]/',               // At least one lowercase letter
                'regex:/[A-Z]/',               // At least one uppercase letter
                'regex:/[0-9]/',               // At least one number

            ],
        ]);
    }
    public static function validateWebhookData(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($request->all(), [
            'message' => 'required',
        ]);
    }

    /**
     * @throws RandomException
     */
    public static function generateOTP($length = 4): string
    {
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= random_int(0, 9);
        }
        return $otp;
    }


    public static function sendOTEmail($email, $otp,$userName): \Illuminate\Http\JsonResponse
    {
        Cache::put('otp_' . $email, $otp, now()->addMinutes(10));
        try {
            Mail::to($email)->send(new \App\Services\Mail\EmailNotification($otp,$email,$userName));
            return response()->json(['message' => 'OTP sent successfully']);
        } catch (\Exception $e) {
            Log::error('Error sending OTP email: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send OTP email'.$e->getMessage()], 500);
        }
    }

    public static function sendAdminEmail($user,$rawPassword): \Illuminate\Http\JsonResponse
    {
        try {
            Mail::to($user->email)->send(new \App\Services\Mail\EmailAdminNotification($user,$rawPassword));
            return response()->json(['message' => 'OTP sent successfully']);
        } catch (\Exception $e) {
            Log::error('Error sending OTP email: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send OTP email'.$e->getMessage()], 500);
        }
    }


    /**
     * @throws \Exception
     */
    public static function getJWTUser(): User
    {
        $user = Auth::user();

        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        Log::info("Token " . json_encode($user));
        Log::info("Email " . $user->email);
        return  User::where("email",$user->email)->first();
    }

}
