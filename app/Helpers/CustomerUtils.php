<?php

namespace App\Helpers;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\Mail\EmailAdminNotification;
use App\Services\Mail\EmailNotification;
use App\Services\Mail\ResetPasswordNotification;
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
    public static function validateUpdateData(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($request->all(), [
            'name' => 'required|string',
            'address' => 'required|string',
            'email' => 'required|string',
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

    /**
     * @throws RandomException
     */
    public static function generateLink($length = 120): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $link = '';

        for ($i = 0; $i < $length; $i++) {
            $link .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $link.time();
    }
    /**
     * @throws RandomException
     */
    public static function generateReferLink($length = 30): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $link = '';

        for ($i = 0; $i < $length; $i++) {
            $link .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $link;
    }



    public static function sendOTEmail($email, $otp,$userName): void
    {
        Cache::put('otp_' . $email, $otp, now()->addMinutes(10));
        Log::info('sending OTP : ' . $email);
            Mail::to($email)->send(new EmailNotification($otp,$email,$userName));
            Log::info('Success Email : ' . $email);


    }

    public static function sendAdminEmail($user,$rawPassword): \Illuminate\Http\JsonResponse
    {
        try {
            Mail::to($user->email)->send(new EmailAdminNotification($user,$rawPassword));
            return response()->json(['message' => 'Mail sent successfully']);
        } catch (\Exception $e) {
            Log::error('Error sending Mail : ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send Mail email'.$e->getMessage()], 500);
        }
    }

    public static function sendLink($user): void
    {

        try {
            Mail::to($user->email)->send(new ResetPasswordNotification($user));
            Log::info(' sending Mail : ' . $user->email);
        } catch (\Exception $e) {
            Log::error('Error sending Mail : ' . $e->getMessage());
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
        Log::info("Email " . $user->email ?? "");
        return  User::where("email",$user->email)->first();
    }

}
