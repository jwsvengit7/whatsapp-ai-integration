<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Helpers\CustomerUtils;
use App\Helpers\ResponseUtils;
use App\Models\Product;
use App\Models\ProductQuestion;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProductServiceImpl implements ProductService
{

    public function fetchProduct(): \Illuminate\Http\JsonResponse
    {
        try {
            $user= CustomerUtils::getJWTUser();
            if($user==null){
                return ResponseUtils::respondWithError("User not found.", 404);
            }
            $products = Product::with('questions')->get();
            return ResponseUtils::respondWithSuccess(
                $products, Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {
            return ResponseUtils::respondWithError('User not found',
                Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            Log::error('Failed to fetch User: ' . $e->getMessage());
            return ResponseUtils::respondWithError('An error occurred while fetching the User',
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addProduct(\Illuminate\Http\Request $request)
    {

    }

    /**
     * @throws Exception
     */
    public function updateProduct(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $user = CustomerUtils::getJWTUser();
        if ($user == null) {
            return ResponseUtils::respondWithError("User not found.", Response::HTTP_NOT_FOUND);
        }
        if ($user->role==UserRole::SUPER_ADMIN && $user->role===UserRole::ADMIN) {
            return ResponseUtils::respondWithError("You dont have permissions to update product", Response::HTTP_UNAUTHORIZED);
        }

        $request->validate([
            'type' => 'required|string',
            'id' => 'required|int',
            'questions' => 'required|array',
        ]);

        $product = Product::where("name", $request->input("type"))->first();
        if (!$product) {
            return ResponseUtils::respondWithError("Product not found.", Response::HTTP_NOT_FOUND);
        }


        $product->name = $request->input("type");

        $questions = $request->input('questions');

        ProductQuestion::where('product_id', $product->id)->delete();

        foreach ($questions as $questionData) {
            $question = new ProductQuestion();
            $question->product_id = $product->id;
            $question->question = $questionData['question'];
            $question->save();
        }

        return ResponseUtils::respondWithSuccess('Product updated successfully.', Response::HTTP_OK);
    }

    /**
     * @throws Exception
     */
    public function deleteProduct(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $id = $request->query('id');
        $user = CustomerUtils::getJWTUser();
        if ($user == null) {
            return ResponseUtils::respondWithError("User not found.", Response::HTTP_NOT_FOUND);
        }
        if ($user->role==UserRole::SUPER_ADMIN && $user->role===UserRole::ADMIN) {
            return ResponseUtils::respondWithError("You dont have permissions to update product", Response::HTTP_UNAUTHORIZED);
        }

        $product = Product::where("id", $id)->first();
        if (!$product) {
            return ResponseUtils::respondWithError("Product not found.", Response::HTTP_NOT_FOUND);
        }
        $product->delete();
        return ResponseUtils::respondWithSuccess('Product Deleted successfully.', Response::HTTP_OK);
    }
}
