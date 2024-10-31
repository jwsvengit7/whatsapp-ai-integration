<?php

namespace App\Services;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Helpers\CustomerUtils;
use App\Helpers\ResponseUtils;
use App\Models\Conversation;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AdminServiceImpl implements AdminService
{


    /**
     * @throws Exception
     */
    public function createProduct($request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
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

        $uploadPath = public_path('images'); // This gets the full path to public/uploads
        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->move($uploadPath, $filename);
        }
        $questions = [
            "What is the warranty period for this product?",
            "Is this product available in other colors?",
            "Does the product support international shipping?"
        ];



        $product = Product::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'image' => $imagePath,
            'user_id' => $user->id,
        ]);
        // Iterate over each question and create a new ProductQuestion
        foreach ($questions as $question) {
            $product->questions()->create([
                'question' => $question,
                'product_id' => $product->id,
            ]);
        }

        return ResponseUtils::respondWithSuccess(['product' => $product], 201);
    }


    /**
     * @throws Exception
     */
    public function createAdmin(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = CustomerUtils::validateAdminData($request);

        if ($validator->fails()) {
            return ResponseUtils::respondWithValidationErrors($validator);
        }

       $user= CustomerUtils::getJWTUser();


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
                'image'=> 'user/default.png',
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

           if ($user->role != UserRole::SUPER_ADMIN && $user->role != UserRole::ADMIN) {
               return ResponseUtils::respondWithError("User does not have permissions.", Response::HTTP_UNAUTHORIZED);
           }
           $users = User::all();
           return ResponseUtils::respondWithSuccess(
               $users, Response::HTTP_OK);

       } catch (ModelNotFoundException | Exception $e) {
           return ResponseUtils::respondWithError($e->getMessage(),
               Response::HTTP_INTERNAL_SERVER_ERROR);
       }
    }

    /**
     * @throws Exception
     */
    public function fetchConversation(Request $request): \Illuminate\Http\JsonResponse
    {
        $id = $request->query('id');
        $user = CustomerUtils::getJWTUser();

        if ($user == null) {
            return ResponseUtils::respondWithError("User not found.", Response::HTTP_NOT_FOUND);
        }

        if ($user->role == UserRole::SUPER_ADMIN && $user->role == UserRole::ADMIN) {
            return ResponseUtils::respondWithError("You don't have permissions to access conversations", Response::HTTP_UNAUTHORIZED);
        }
        if ($id) {
            $conversations = Conversation::where("customer_id", $id)->get();
            if ($conversations->isEmpty()) {
                return ResponseUtils::respondWithError("Conversations not found.", Response::HTTP_NOT_FOUND);
            }
            return ResponseUtils::respondWithSuccess($conversations->toArray(), Response::HTTP_OK);
        }

        $conversations = Conversation::all();
        return ResponseUtils::respondWithSuccess($conversations->toArray(), Response::HTTP_OK);
    }

}
