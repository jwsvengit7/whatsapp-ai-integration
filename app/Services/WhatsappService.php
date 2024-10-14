<?php

namespace App\Services;

use App\Helpers\AIHelpers;
use App\Models\Conversation;
use App\Models\Customer;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    /**
     * Handle the reception of a new message.
     *
     * @param Request $request
     * @return ResponseFactory|Application|Response|void
     * @throws ConnectionException
     * @throws Exception
     */
    public function receiveMessage(Request $request) {
        if ($request->isMethod('get')) {
            Log::info('Webhook Verification Request:', $request->all());

            $verifyToken = env("WEBHOOK_VERIFY_TOKEN");
            $hubVerifyToken = $request->input('hub_verify_token');
            $hubChallenge = $request->input('hub_challenge');

            Log::info('verifyToken Verification Request:', ['token' => $verifyToken]);
            Log::info('hubVerifyToken Verification Request:', ['hubVerifyToken' => $hubVerifyToken]);
            Log::info('hubChallenge Request:', ['hubChallenge' => $hubChallenge]);

            if ($verifyToken === $hubVerifyToken) {
                return response($hubChallenge, 200);
            }
            return response('Invalid verify token', 403);
        }

        if ($request->isMethod('post')) {
            Log::info('POST Webhook Message Request:', $request->all());

            $entry = $request->input('entry');
            foreach ($entry as $event) {
                $changes = $event['changes'];
                foreach ($changes as $change) {
                    $from = $change['value']['messages'][0]['from'] ?? null;
                    $incomingMessage = $change['value']['messages'][0]['text']['body'] ?? null;

                    Log::info('Incoming message:', ['from' => $from, 'message' => $incomingMessage]);

                    if ($incomingMessage) {
                        $aiMessage = $this->generateAIResponse($incomingMessage);
                        $this->sendMessage($from, $aiMessage);
                        if (!$this->findCustomerByPhone($from)->exists()) {
                            $this->saveCustomerInformation($from);
                        }
                    }
                }
            }

            return response('Method not allowed', 405);
        }
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    private function sendMessage(string $to, string $message): void {
        $url = 'https://graph.facebook.com/v12.0/' . env('WHATSAPP_BUSINESS_ID') . '/messages';
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
        $customer =$this->findCustomerByPhone($to);
        $this->saveMessage($customer->id,$message);
    }



    /**
     * Generate a response using the OpenAI API.
     *
     * @param string $message
     * @return string
     * @throws Exception
     */
    public function generateAIResponse(string $message): string {
        $context = AIHelpers::AIContext();

        $url = env("HUGGING_FACE_URL");
        $apiKey = env('HUGGING_KEY');

        $data = [
            'model' => env('HUGGING_MODEL'),
            'messages' => [
                ["role" => "system", "content" => $context],
                ["role" => "user", "content" => $message]
            ],
            'max_tokens' => 500,
            'stream' => false,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, $data);

        if ($response->successful()) {
            $responseBody = $response->json();
            return $responseBody['choices'][0]['message']['content'] ?? 'No response generated.';
        } else {
            Log::error('Failed to get response from OpenAI: ' . $response->body());
            return 'Error generating response.';
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
    protected function saveMessage(int $customerId, string $message): void {
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

    /**
     * @throws Exception
     */
    protected function saveCustomerInformation(int $phone): void {
        try {
            Customer::create([
                'phone' => $phone
            ]);
        } catch (Exception $e) {
            Log::error('Failed to save customer: ' . $e->getMessage());
            throw new Exception('Failed to save customer: ' . $e->getMessage());
        }
    }


    protected function findCustomerByPhone($phone){
        return Customer::where('phone', $phone);
    }

}
