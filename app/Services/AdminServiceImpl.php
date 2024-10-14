<?php

namespace App\Services;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Helpers\CustomerUtils;
use App\Helpers\ResponseUtils;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\JWTGuard;

class AdminServiceImpl implements AdminService
{



    public function createProduct($request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'rate' => 'nullable|numeric|min:0|max:5',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);


        $user = CustomerUtils::getJWTUser();
        if ($user==null) {
            return ResponseUtils::respondWithError("User not found. ", 404);
        }

        if ($user->role !== UserRole::ADMIN && $user->role !== UserRole::SUPER_ADMIN) {
            return ResponseUtils::respondWithError("User does not have permissions to add product.", 403);
        }

        if ($validator->fails()) {
            return ResponseUtils::respondWithValidationErrors($validator);
        }

        // Handle image upload if present
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public'); // Save to storage
        }

        // Create the product
        $product = Product::create([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'rate' => $request->input('rate'),
            'image' => $imagePath, // Store the image path
            'user_id' => $user->id,
        ]);

        return ResponseUtils::respondWithSuccess(['product' => $product], 201);
    }




    public function createAdmin(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = CustomerUtils::validateAdminData($request);

        if ($validator->fails()) {
            return ResponseUtils::respondWithValidationErrors($validator);
        }

       $user= CustomerUtils::getJWTUser();

        if ($user==null) {
            return ResponseUtils::respondWithError("User not found. ", 404);
        }

        if ($user->role !== UserRole::SUPER_ADMIN) {
            return ResponseUtils::respondWithError("User does not have permissions to create admin.", 403);
        }

        if (User::where("email",$request->input("email"))->exists()) {
            return ResponseUtils::respondWithError("Email Already Exist", Response::HTTP_CONFLICT);
        }

        try {
            $this->saveUser($request);
            return ResponseUtils::respondWithSuccess('User created successfully', Response::HTTP_CREATED);

        } catch (Exception $e) {
            Log::error('Customer creation failed: ' . $e->getMessage());
            return ResponseUtils::respondWithError('An error occurred while creating the customer'. $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @throws Exception
     */
    protected function saveUser(Request $request): void
    {
        try {
            DB::beginTransaction();

            $user= User::create([
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'name' => $request->input('name'),
                'role' => $request->input('role'),
                'status' => Status::ACTIVE->value,
                'otp'=>"1234",
                'otp_date' => now(),
                'password' => $request->input('password'),
            ]);
            CustomerUtils::sendAdminEmail($user,$request->input('password'));
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to save User: ' . $e->getMessage());
        }
    }

    public function fetchAllUsers(): \Illuminate\Http\JsonResponse
    {
       try{
           $user= CustomerUtils::getJWTUser();
           if($user==null){
               return ResponseUtils::respondWithError("User not found.", 404);
           }
           if ($user->role != UserRole::SUPER_ADMIN && $user->role != UserRole::ADMIN) {
               return ResponseUtils::respondWithError("User does not have permissions to create admin.", 403);
           }
           $users = User::all();
           return ResponseUtils::respondWithSuccess(
               $users, Response::HTTP_OK);

       } catch (ModelNotFoundException $e) {
           return ResponseUtils::respondWithError('User not found',
               Response::HTTP_NOT_FOUND);
       } catch (Exception $e) {
           Log::error('Failed to fetch User: ' . $e->getMessage());
           return ResponseUtils::respondWithError('An error occurred while fetching the User',
               Response::HTTP_INTERNAL_SERVER_ERROR);
       }
    }
}
