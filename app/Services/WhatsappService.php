<?php

namespace App\Services;

use App\Helpers\CustomerUtils;
use App\Helpers\ResponseUtils;
use App\Models\Conversation;
use App\Models\Customer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\ConnectionException;
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

     * @throws Exception
     */
    public function receiveMessage(Request $request)
    {
        if ($request->isMethod('get')) {
            $verifyToken = env('WEBHOOK_VERIFY_TOKEN');
            $hubVerifyToken = $request->input('hub.verify_token');
            $hubChallenge = $request->input('hub.challenge');

            if ($verifyToken === $hubVerifyToken) {
                return response($hubChallenge, 200);
            }

            return response('Invalid verify token', 403);
        }


        if ($request->isMethod('post')) {
            $data = $request->all();

            $validator = CustomerUtils::validateWebhookData($request);
            if ($validator->fails()) {
                return response($validator,404);
            }
            $phoneNumber = "";
            $entry = $request->input('entry');
            if (!isset($entry[0]['changes'][0]['value']['messages'][0])) {
                Log::error('Invalid webhook structure.');
                return response('Invalid webhook structure.', Response::HTTP_BAD_REQUEST);
            }


            $data = $entry[0]['changes'][0]['value']['messages'][0];
            $customer = Customer::firstOrCreate(
                ['phone' => $phoneNumber],
                ['email' => 'noreply@govt.com', 'phone' => $phoneNumber]
            );
            $message = $data['text']['body'] ?? null;
            $sender = $data['from'];
            $messageId = $data['id'];
//
            Log::info('Received WhatsApp message from ' . $sender . ': ' . $message);

            $aiResponse = $this->generateAIResponse($message);
            $this->sendMessage($phoneNumber, $aiResponse);
            $this->saveMessage($customer->id, $message);

            return response($aiResponse,
            Response::HTTP_OK);
        }
    }

    /**
     * Generate a response using the OpenAI API.
     *
     * @param Request $message
     * @return string
     * @throws ConnectionException
     */
    public function generateAIResponse(Request $message): string
    {
        $url = 'https://api.openai.com/v1/chat/completions';
        $apiKey = env('OPENAI_API_KEY');
        $data = [
            'model' => env('OPENAI_API_MODEL'),
            'prompt' => ["role"=> "user", "content"=> $message->input("message")],
            'max_tokens' => 150,
            'temperature' => 0.7,
        ];
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, $data);

        if ($response->successful()) {
            $responseBody = $response->json();
            return $responseBody['choices'][0];
        } else {
            Log::error('Failed to get response from OpenAI: ' . $response->body());
            return $response->body();
        }
    }

    /**
     * Send a WhatsApp message using the Meta Business API.
     *
     * @param string $to
     * @param string $message
     * @return void
     * @throws ConnectionException
     */
    private function sendMessage(string $to, string $message): void
    {
        $url = 'https://graph.facebook.com/20.0/' . env('WHATSAPP_BUSINESS_ID') . '/messages';
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
