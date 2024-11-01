<?php

namespace App\Services;

use App\Helpers\AIHelpers;
use App\Helpers\ImageGenerator;
use App\Models\Conversation;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductQuestion;
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
    protected array $processedMessageIds = [];
    private ImageGenerator $imageGenerator;

    public function __construct()
    {
        $this->imageGenerator = new ImageGenerator();
    }

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
                    $messages = $change['value']['messages'] ?? null;

                    if ($messages) {
                        foreach ($messages as $message) {
                            $interactive = $message['interactive'] ?? null;

                            $from = $message['from'] ?? null;

                            if ($interactive && $interactive['type'] === 'button_reply') {
                                $buttonId = $interactive['button_reply']['id'];
                                $buttonTitle = $interactive['button_reply']['title'];

                                Log::info('Button clicked:', [
                                    'from' => $from,
                                    'button_id' => $buttonId,
                                    'button_title' => $buttonTitle,
                                ]);
                                $customer = $this->findCustomerByPhone($from);
                                if ($customer) {
                                    $this->handleConversation($customer, $buttonId);
                                }

                            } elseif (isset($message['text']['body'])) {
                                // Handle text message
                                $incomingMessage = $message['text']['body'];
                                Log::info('Incoming message:', ['from' => $from, 'message' => $incomingMessage]);

                                $customer = $this->findCustomerByPhone($from);
                                if (!$customer) {
                                    $this->saveCustomerInformation($from);
                                }
                                $this->handleConversation($customer, $incomingMessage);
                            }
                        }
                    } else {
                        Log::warning('No messages found in change.', ['change' => $change]);
                    }
                }
            }
            return response('OK', 200);
        }

        return response('Method Not Allowed', 405);
    }



    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function sendMessage(string $to, string $message, $id, array $buttons): void {
        $url = 'https://graph.facebook.com/v12.0/' . env('WHATSAPP_BUSINESS_ID') . '/messages';
        $token = env('WHATSAPP_API_TOKEN');

        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $to
        ];

        if (!empty($buttons)) {
            $data['type'] = 'interactive';
            $data['interactive'] = [
                'type' => 'button',
                'header' => [
                    'type' => 'text',
                    'text' => $message
                ],
                'body' => [
                    'text' => $message
                ],
                'footer' => [
                    'text' => 'Your enquiry product'
                ],
                'action' => [
                    'buttons' => $buttons
                ]
            ];
        }
        else {
            $data['type'] = 'text';
            $data['text'] = ['body' => $message];
        }

        $response = Http::withToken($token)->post($url, $data);
        if (!$response->successful()) {
            Log::error('Failed to send WhatsApp message: ' . $response->body());
        } else {
            $this->saveMessage($id, $message, 'sent', time());
        }
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

        $url = env("OPENAI_API_URL");
        $apiKey = env('OPENAI_API_KEY');

        $data = [
            'model' => env('OPENAI_API_MODEL'),
            'messages' => [
                ["role" => "system", "content" => $context],
                ["role" => "user", "content" => $message]
            ],
            'max_tokens' => 500,
            'temperature' => 0.7,
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
    protected function saveMessage(int $customerId, string $message, $status,$timestamp): void {
        try {
            Conversation::create([
                'customer_id' => $customerId,
                'message' => $message,
                'is_from_customer' => true,
                'status' => $status,
                'message_date' => $timestamp,
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
        return Customer::where('phone', $phone)->first();
    }
    /**
     * @throws ConnectionException
     * @throws Exception
     */
    /**
     * @throws ConnectionException
     * @throws Exception
     */
    protected function handleConversation(Customer $customer, string $incomingMessage): void {
        try {
            $stage = $customer->conversation_stage ?? 0;
            $conversation_data = $customer->message_json ?? "";

            if ($stage === 0 && $customer->conversation_stage) {
                $this->sendMessage($customer->phone, "You can now schedule a message or chat directly with AI.", $customer->id, []);

                if (strtolower($incomingMessage) === 'chat') {
                    $aiMessage = $this->generateAIResponse("Direct conversation started.");
                    $this->sendMessage($customer->phone, $aiMessage, $customer->id, []);
                    return;
                }

                elseif (strtolower($incomingMessage) === 'schedule') {
                    $this->sendMessage($customer->phone, "What date and time would you like to schedule the message?", $customer->id, []);
                    $customer->update(['conversation_stage' => 5]); // Stage for scheduling
                    return;
                }
            }

            switch ($stage) {
                case 0:
                    $this->sendMessage($customer->phone, "Welcome! What is your name?", $customer->id, []);
                    $customer->update(['conversation_stage' => 1]);
                    break;

                case 1:
                    $conversation_data .= "Name: $incomingMessage\n";
                    $customer->name = $incomingMessage;
                    $customer->save();
                    $this->saveMessage($customer->id, $incomingMessage, "received", time());

                    $this->sendMessage($customer->phone, "Where are you chatting from?", $customer->id, []);
                    $customer->update(['conversation_stage' => 2, "message_json" => $conversation_data]);
                    break;

                case 2:
                    $conversation_data .= "Location: $incomingMessage\n";
                    $customer->location = $incomingMessage;
                    $customer->save();
                    $products = Product::all();
                    $buttons = [];

                    foreach ($products as $product) {
                        $buttons[] = [
                            'type' => 'reply',
                            'reply' => [
                                'id' => $product->id,
                                'title' => $product->name,
                            ],
                        ];
                    }
                    $this->saveMessage($customer->id, $incomingMessage, "received", time());

                    $this->sendMessage($customer->phone, "What type of stove do you use?", $customer->id, $buttons);
                    $customer->update(['conversation_stage' => 3, "message_json" => $conversation_data]);
                    break;

                case 3:
                    $productName = Product::where("id", $incomingMessage)->first()->name;
                    $conversation_data .= "Selected Product Name: $productName\n";
                    $this->saveMessage($customer->id, $productName, "received", time());
                    $customer->update(['selected_product_id' => $incomingMessage, "message_json" => $conversation_data]);

                    $questions = $this->loadProductQuestions($incomingMessage);

                    if (!empty($questions)) {
                        $nextQuestion = $questions[0]['question'];
                        $this->sendMessage($customer->phone, $nextQuestion, $customer->id, []);

                        $customer->update([
                            'conversation_stage' => 4,
                            'current_question_index' => 1,
                            'questions_json' => json_encode($questions),
                            "message_json" => $conversation_data,
                        ]);
                    } else {
                        $this->sendMessage($customer->phone, "No more questions for this product.", $customer->id, []);
                        $customer->update([
                            'conversation_stage' => 0,
                            'questions_json' => null,
                            'current_question_index' => null,
                            'message_json' => null,
                            'has_completed_onboarding' => true,
                        ]);
                    }
                    break;

                case 4:
                    $questions = json_decode($customer->questions_json, true) ?? [];
                    $currentQuestionIndex = $customer->current_question_index ?? 0;

                    if ($currentQuestionIndex < count($questions)) {
                        if ($currentQuestionIndex > 0) {
                            $question = $questions[$currentQuestionIndex - 1]['question'];
                            $conversation_data .= "$question: $incomingMessage\n";
                        }
                        $this->saveMessage($customer->id, $incomingMessage, "received", time());

                        $nextQuestion = $questions[$currentQuestionIndex]['question'];
                        $this->sendMessage($customer->phone, $nextQuestion, $customer->id, []);

                        $customer->update([
                            'conversation_stage' => $stage + 1,
                            'current_question_index' => $currentQuestionIndex + 1,
                            'message_json' => $conversation_data,
                        ]);
                    } else {
                        $conversation_data .= "$incomingMessage\n";
                        $this->saveMessage($customer->id, $incomingMessage, "received", time());

                        $aiMessage = $this->generateAIResponse($conversation_data);
                        $this->sendMessage($customer->phone, $aiMessage, $customer->id, []);

                        $customer->update([
                            'conversation_stage' => 0,
                            'questions_json' => null,
                            'current_question_index' => null,
                            'message_json' => null,
                            'has_completed_onboarding' => true,
                        ]);
                    }
                    break;

                // Scheduling stage
                case 5:
                    $conversation_data .= "Scheduled Message: $incomingMessage\n";
                    $this->saveMessage($customer->id, $incomingMessage, "scheduled", time());
                    $this->sendMessage($customer->phone, "Message scheduled successfully.", $customer->id, []);

                    $customer->update([
                        'conversation_stage' => 0,
                        'message_json' => null,
                    ]);
                    break;

                default:
                    $this->sendMessage($customer->phone, "I'm here to help! Type 'schedule' to set a message or 'chat' to talk with AI.", $customer->id, []);
            }
        } catch (Exception $e) {
            Log::error('Error handling conversation: ' . $e->getMessage());
            $customer->update([
                'conversation_stage' => 0,
                'questions_json' => null,
                'current_question_index' => null,
                'message_json' => null,
            ]);
        }
    }

    protected function loadProductQuestions($productId): array {
        // Fetch the product by ID
        $product = Product::where('id', $productId)->first();

        if (!$product) {
            return [];
        }
        return ProductQuestion::where('product_id', $product->id)->get()->toArray();
    }



}
