<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Helpers\ResponseUtils;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

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

        $auth = Auth::user();
        $id = $auth->getAuthIdentifier();
        $user = User::where("id", $id)->first();

        if (!$user) {
            return ResponseUtils::respondWithError("User not found. ", 404);
        }

        // Check if the user has the correct role
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


    public function fetchProduct(): \Illuminate\Http\JsonResponse
    {
        try {
            $auth = Auth::user();
            $email = $auth->getAuthIdentifier();
            $user = User::where("email",$email);
            if(!$user->exist()){
                return ResponseUtils::respondWithError("User not found.", 404);
            }
            $product = Product::all();
            return ResponseUtils::respondWithSuccess(
                $product, Response::HTTP_OK);

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
