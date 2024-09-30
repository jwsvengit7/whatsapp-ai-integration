<?php

namespace App\Services;

use App\Helpers\ResponseUtils;
use App\Models\Conversation;
use App\Models\Customer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WhatsappService
{
    /**
     * Handle the reception of a new message.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function receiveMessage(Request $request): JsonResponse
    {

        $incomingMessage = $request->input('Body');
        $phoneNumber = $request->input('From');

        $customer = Customer::firstOrCreate(
            ['phone' => $phoneNumber],
            ['phone' => $phoneNumber, 'email' => '']
        );

        try {
            // Save the incoming message to the conversation
            $this->saveMessage($customer->id, $incomingMessage);
            return ResponseUtils::respondWithSuccess('Message received successfully', Response::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error('Failed to save incoming message: ' . $e->getMessage());
            return ResponseUtils::respondWithError('An error occurred while processing the message', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Save the incoming message to the database.
     *
     * @param int $customerId
     * @param string $message
     * @return void
     * @throws Exception
     */
    protected function saveMessage(int $customerId, string $message): void
    {
        try {
            Conversation::create([
                'customer_id' => $customerId,
                'message' => $message,
                'is_from_customer' => true,
            ]);
        } catch (Exception $e) {
            throw new Exception('Failed to save message: ' . $e->getMessage());
        }
    }

    /**
     * Send a message to a customer.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function sendMessage(int $id): JsonResponse
    {
        try {
            $customer = Customer::findOrFail($id);
            return ResponseUtils::respondWithSuccess($customer, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return ResponseUtils::respondWithError('Customer not found', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            Log::error('Failed to fetch customer: ' . $e->getMessage());
            return ResponseUtils::respondWithError('An error occurred while fetching the customer', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
