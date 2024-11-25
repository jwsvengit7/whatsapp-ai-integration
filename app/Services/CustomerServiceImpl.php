<?php


namespace App\Services;

use App\Enums\UserRole;
use App\Helpers\CustomerUtils;
use App\Helpers\ResponseUtils;
use App\Models\Customer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CustomerServiceImpl implements CustomerService
{
    /**
     * Handle the creation of a new customer.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function customerCreationImpl(Request $request): JsonResponse
    {
        $validator = CustomerUtils::validateCustomerData($request);

        if ($validator->fails()) {
            return ResponseUtils::respondWithValidationErrors($validator);
        }

        try {
            $this->saveCustomer($request);
            return ResponseUtils::respondWithSuccess('Customer created successfully', Response::HTTP_CREATED);

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
    protected function saveCustomer(Request $request): void
    {
        try {
            Customer::create([
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'name' => $request->input('name'),
            ]);
        } catch (Exception $e) {
            throw new Exception('Failed to save customer: ' . $e->getMessage());
        }
    }


    public function getCustomerById($id): JsonResponse
    {
        try {
            $customer = Customer::findOrFail($id);
            return ResponseUtils::respondWithSuccess(
                $customer, Response::HTTP_OK);

        } catch (ModelNotFoundException | Exception $e) {
            return ResponseUtils::respondWithError($e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function fetchAllCustomer(): JsonResponse
    {
        try{
        $user= CustomerUtils::getJWTUser();
        if($user==null){
            return ResponseUtils::respondWithError("User not found.", 404);
        }
         if ($user->role !== UserRole::SUPER_ADMIN && $user->role !== UserRole::ADMIN) {
                return ResponseUtils::respondWithError("User does not have permissions.", 403);
            }
         else {
            $customer = Customer::all();
            return ResponseUtils::respondWithSuccess(
                $customer, Response::HTTP_OK);
        }
    }catch (Exception $e){
            Log::error('Failed to fetch customer: ' . $e->getMessage());
            return ResponseUtils::respondWithError('An error occurred while fetching the customer',
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
