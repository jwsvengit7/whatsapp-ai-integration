<?php

namespace App\Services;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Helpers\CustomerUtils;
use App\Helpers\Response as LoginResponse;
use App\Helpers\ResponseUtils;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;


class UserServiceImpl implements UserService
{
    /**
     * Handle the creation of a new customer.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function userSignup(Request $request): JsonResponse
    {
        $validator = CustomerUtils::validateUserData($request);

        if ($validator->fails()) {
            return ResponseUtils::respondWithValidationErrors($validator);
        }
        if ($this->findUserByEmail($request->input("email"))->exists()) {
            return ResponseUtils::respondWithError("Email Already Exist", Response::HTTP_CONFLICT);
        }
        $otp = CustomerUtils::generateOTP();

        try {
            $this->saveUser($request, $otp);
            return ResponseUtils::respondWithSuccess('User created successfully', Response::HTTP_CREATED);

        } catch (Exception $e) {
            Log::error('Customer creation failed: ' . $e->getMessage());
            return ResponseUtils::respondWithError('An error occurred while creating the customer', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Save the customer to the database.
     *
     * @param Request $request
     * @return void
     * @throws Exception
     */
    protected function saveUser(Request $request, string $otp): void
    {
        try {
            CustomerUtils::sendOTEmail($request->input('email'),$otp,$request->input('name'));
            User::create([
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'name' => $request->input('name'),
                'role' => $request->input('role'),
                'status' => Status::INACTIVE->value,
                'otp' => $otp,
                'otp_date'=> now(),
                'password' => $request->input('password'),
            ]);
        } catch (Exception $e) {
            throw new Exception('Failed to save User: ' . $e->getMessage());
        }
    }


    public function getUserById($id): JsonResponse
    {
        try {
            $customer = User::all();
            return ResponseUtils::respondWithSuccess(
                $customer, Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {
            return ResponseUtils::respondWithError('User not found',
                Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            Log::error('Failed to fetch User: ' . $e->getMessage());
            return ResponseUtils::respondWithError('An error occurred while fetching the User',
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function userLogin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return ResponseUtils::respondWithError($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $user = $this->findUserByEmail($request->input("email"))->first();

        if ($user==null) {
            return ResponseUtils::respondWithError('User not found ', Response::HTTP_NOT_FOUND);
        }
        if ($user->status === Status::INACTIVE || $user->status === Status::DELETED) {
            return ResponseUtils::respondWithError('User is Currently '.$user->status->name, 401);
        }

        $expiresAt = Carbon::now()->addHour()->timestamp;

        $customClaims = [
            'email' => $user->email,
            'status' =>$user->status,
            'exp' => $expiresAt,
        ];
        $token = JWTAuth::claims($customClaims)->attempt($validator->validated());
        return ResponseUtils::respondWithSuccess(
            LoginResponse::loginResponse($user,$token)
        , Response::HTTP_OK);
    }

    public function changePassword(Request $request) : JsonResponse
    {
        return ResponseUtils::respondWithSuccess("",7);
    }


    public function confirmAccount(Request $request): JsonResponse
    {
        $otp = $request->input("otp");
        $user = $this->findUserByEmail($request->input("email"))->first();


        if ($user==null) {
            return ResponseUtils::respondWithError('User not found ', Response::HTTP_NOT_FOUND);
        }
        if($otp==$user->otp){
            if ($user->otp_date->addMinutes(5) < now()) {
                return ResponseUtils::respondWithError('OTP has expired', Response::HTTP_CONFLICT);
            }

        if ($user->status === Status::INACTIVE && $user->status !== Status::DELETED) {

            $user->status = Status::ACTIVE->value;
            $user->save();
            return ResponseUtils::respondWithSuccess($user, Response::HTTP_OK);
        }

        }
        return ResponseUtils::respondWithSuccess("OTP is invalid",Response::HTTP_CONFLICT);
    }

    public function resendVerification(Request $request):JsonResponse
    {
        return ResponseUtils::respondWithSuccess("",7);
    }



    protected function findUserByEmail($email)
    {
        return User::where('email', $email);
    }


}
