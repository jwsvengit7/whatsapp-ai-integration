<?php

namespace App\Services;

use App\Enums\Status;
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
use Random\RandomException;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;


class UserServiceImpl implements UserService
{
    /**
     * Handle the creation of a new customer.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws RandomException
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

            User::create([
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'name' => $request->input('name'),
                'role' => $request->input('role'),
                'status' => Status::INACTIVE->value,
                'otp' => $otp,
                'otp_date' => now(),
                'image'=> 'user/default.png',
                'password' => $request->input('password'),
            ]);
            CustomerUtils::sendOTEmail($request->input('email'), $otp, $request->input('name'));
        } catch (Exception $e) {
            throw new Exception('Failed to save User: ' . $e->getMessage());
        }
    }


    public function getUserById(): JsonResponse
    {
        try {
           $user= CustomerUtils::getJWTUser();
            return ResponseUtils::respondWithSuccess(
                $user, Response::HTTP_OK);

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

        if ($user == null) {
            return ResponseUtils::respondWithError('User not found ', Response::HTTP_NOT_FOUND);
        }
        if ($user->status === Status::INACTIVE || $user->status === Status::DELETED) {
            return ResponseUtils::respondWithError('User is ' . $user->status->value . " Please verify your account", 401);
        }

        $expiresAt = Carbon::now()->addHour()->timestamp;

        $customClaims = [
            'email' => $user->email,
            'status' => $user->status,
            'exp' => $expiresAt,
        ];
        $token = JWTAuth::claims($customClaims)->attempt($validator->validated());
        return ResponseUtils::respondWithSuccess(
            LoginResponse::loginResponse($user, $token)
            , Response::HTTP_OK);
    }

    public function changePassword(Request $request): JsonResponse
    {
        return ResponseUtils::respondWithSuccess("", 7);
    }


    public function confirmAccount(Request $request): JsonResponse
    {
        $otp = $request->input("otp");
        $user = $this->findUserByEmail($request->input("email"))->first();

        if ($user == null) {
            return ResponseUtils::respondWithError('User not found ', Response::HTTP_NOT_FOUND);
        }
        if ($otp == $user->otp) {
            if ($user->otp_date->addMinutes(5) < now()) {
                return ResponseUtils::respondWithError('OTP has expired', Response::HTTP_CONFLICT);
            }

            if ($user->status === Status::INACTIVE && $user->status !== Status::DELETED) {

                $user->status = Status::ACTIVE->value;
                $user->save();
                return ResponseUtils::respondWithSuccess($user, Response::HTTP_OK);
            }

        }
        return ResponseUtils::respondWithSuccess("OTP is invalid", Response::HTTP_CONFLICT);
    }


    public function updateAccount(Request $req): JsonResponse
    {

        try {
            $userAuth= CustomerUtils::getJWTUser();
            if($userAuth->email===$req->input("email")){
                return ResponseUtils::respondWithError("User not found.", 404);
            }

            $user = $this->findUserByEmail($userAuth->email)->first();

            if ($user == null) {
                return ResponseUtils::respondWithError('User not found ', Response::HTTP_NOT_FOUND);
            }

            if ($user->status === Status::INACTIVE && $user->status !== Status::DELETED) {
                $user->phone = $req->input("phone");
                $user->address = $req->input("address");
                $user->password = bcrypt($req->input('password'));
                $user->name = $req->input("name");
                $user->save();
                return ResponseUtils::respondWithSuccess($user, Response::HTTP_OK);
            }else{
                return ResponseUtils::respondWithError("Verify your account", Response::HTTP_BAD_GATEWAY);
            }
        }catch(Exception $e) {
            return ResponseUtils::respondWithError($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @throws RandomException
     */
    public function resendVerification(Request $request): JsonResponse
    {
        $user = $this->findUserByEmail($request->input("email"))->first();


        if ($user != null) {
            $otp = CustomerUtils::generateOTP();
            $user->otp =$otp;
            $user->otp_date=now();
            $user->save();
                CustomerUtils::sendOTEmail($request->input('email'), $otp, $user->name);

            return ResponseUtils::respondWithSuccess("OTP resent successfully.", 200);
        } else {
            return ResponseUtils::respondWithError("User not found.", 404);
        }
    }

    protected function findUserByEmail($email)
    {
        return User::where('email', $email);
    }


    public function forgetPassword(Request $request): JsonResponse
    {
        try {
            $user=$this->findUserByEmail($request->input("email"))->first();
        if ($user->exists()) {
            $link = CustomerUtils::generateLink();
            $user->remember_token=$link;
            $user->link_expiration=now();
            $user->save();
            CustomerUtils::sendLink($user);
            return ResponseUtils::respondWithSuccess('Link sent successfully to '.$user->email, Response::HTTP_CREATED);
        }
            return ResponseUtils::respondWithError('User not found', Response::HTTP_NOT_FOUND);

        } catch (Exception $e) {
            Log::error('Customer creation failed: ' . $e->getMessage());
            return ResponseUtils::respondWithError('An error occurred while creating the customer', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function verifyLink($token): JsonResponse
    {

        $user = User::where("remember_token",$token)->first();

        if ($user == null) {
            return ResponseUtils::respondWithError('User not found ', Response::HTTP_NOT_FOUND);
        }
        if ($token == $user->remember_token) {
            if ($user->link_expiration->addMinutes(10) < now()) {
                return ResponseUtils::respondWithError('Link has expired', Response::HTTP_CONFLICT);
            }
                return ResponseUtils::respondWithSuccess("User", Response::HTTP_OK);
        }
        return ResponseUtils::respondWithSuccess("Link is invalid", Response::HTTP_CONFLICT);
    }
}
