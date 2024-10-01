<?php

namespace App\Services;

use App\Helpers\CustomerUtils;
use App\Helpers\ResponseUtils;
use App\Models\Conversation;
use App\Models\Customer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WhatsappService
{
    /**
     * Handle the reception of a new message.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function receiveMessage(Request $request): JsonResponse
    {
        $validator = CustomerUtils::validateCustomerData($request);
        if ($validator->fails()) {
            return ResponseUtils::respondWithValidationErrors($validator);
        }
        $incomingMessage = $request->input('Body');
        $phoneNumber = $request->input('From');

        // Check if the phone number is not null or empty
        if (empty($phoneNumber)) {
            Log::error('Phone number is missing from the request.');
            return ResponseUtils::respondWithError('Phone number is required.', Response::HTTP_BAD_REQUEST);
        }

        // Find or create the customer based on phone number
        $customer = Customer::firstOrCreate(
            ['phone' => $phoneNumber],
            ['email' => 'noreply@govt.com'] // Set email only on creation
        );

        // Continue processing if needed...
        $data = $request->input('entry')[0]['changes'][0]['value']['messages'][0];
        $message = $data['text']['body'] ?? null;
        $sender = $data['from'];
        $messageId = $data['id'];

        Log::info('Received WhatsApp message from ' . $sender . ': ' . $message);

        // Generate AI response (implement your AI logic here)
        $aiResponse = $this->generateAIResponse($incomingMessage);
        $this->sendMessage($phoneNumber, $aiResponse);

        // Save the incoming message to the database
        $this->saveMessage($customer->id, $incomingMessage);

        return ResponseUtils::respondWithSuccess([
            'message' => $aiResponse,
            'sender' => $phoneNumber,
        ], Response::HTTP_OK);
    }

    /**
     * Generate a response using the OpenAI API.
     *
     * @param string $message
     * @return string
     */
    private function generateAIResponse(string $message): string
    {
        $url = 'https://api.openai.com/v1/completions';
        $apiKey = env('OPENAI_API_KEY');
        $data = [
            'model' => 'gpt-4',
            'prompt' => $message,
            'max_tokens' => 150,
            'temperature' => 0.7,
        ];
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, $data);

        if ($response->successful()) {
            $responseBody = $response->json();
            return $responseBody['choices'][0]['text'] ?? 'Sorry, I did not understand that.';
        } else {
            Log::error('Failed to get response from OpenAI: ' . $response->body());
            return 'Sorry, I could not process your request at the moment.';
        }
    }

    /**
     * Send a WhatsApp message using the Meta Business API.
     *
     * @param string $to
     * @param string $message
     * @return void
     */
    private function sendMessage(string $to, string $message): void
    {
        $url = 'https://graph.facebook.com/v13.0/' . env('WHATSAPP_BUSINESS_ID') . '/messages';
        $token = env('WHATSAPP_API_TOKEN');

        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => ['body' => $message],
        ];

        $response = Http::withToken($token)->post($url, $data);
        if (!$response->successful()) {
            Log::error('Failed to send WhatsApp message: ' . $response->body());
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
            Log::error('Failed to save message: ' . $e->getMessage());
            throw new Exception('Failed to save message: ' . $e->getMessage());
        }
    }


}
