<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Helpers\AIHelpers;
use App\Helpers\CustomerUtils;
use App\Helpers\HttpUtils;
use App\Helpers\ImageGenerator;
use App\Helpers\ResponseUtils;
use App\Models\Conversation;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductQuestion;
use App\Models\ScheduledMessage;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class WhatsappService
{
    protected array $processedMessageIds = [];


    public function __construct()
    {

    }

    /**
     * Handle the reception of a new message.
     *
     * @param Request $request
     * @throws ConnectionException
     * @throws Exception
     */
    public function receiveMessage(Request $request): Application|\Illuminate\Http\Response|ResponseFactory
    {
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
    public function sendMessage(string $to, string $message, $id, array $buttons,string $mediaUrl = null): void {
        $url = 'https://graph.facebook.com/v12.0/' . env('WHATSAPP_BUSINESS_ID') . '/messages';
        $token = env('WHATSAPP_API_TOKEN');

        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $to
        ];
        if ($mediaUrl) {
            // Sending an image
            $data['type'] = 'image';
            $data['image'] = [
                'link' => $mediaUrl,
                'caption' => $message // Optional: You can add a caption for the image
            ];
        }

       elseif (!empty($buttons)) {
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
    public function generateAIResponse(string $message,bool $status=false): string {
        $context = AIHelpers::AIContext($this->displayProductQuestions());

if($status){
    $context = AIHelpers::AIContextAfterPrediction($this->displayProductQuestions());
}
        Log::info('Message: ' . $context);

        $url = env("OPENAI_API_URL");
        $apiKey = env('OPENAI_API_KEY');

        $data = [
            'model' => env('OPENAI_API_MODEL'),
            'messages' => [
                ["role" => "system", "content" => $context],
                ["role" => "user", "content" => $context.'\n'.$message]
            ],
            'max_tokens' => 1200,
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
     * @throws Exception
     */

    protected function handleConversation(Customer $customer, string $incomingMessage): void
    {
        $this->handleNameInput($customer, $incomingMessage);

        try {
            $stage = $customer->conversation_stage ?? 0;
            $conversation_data = $customer->message_json ?? "";

            if ($customer->stopChat) {
                return;
            }

            if ($customer->completed_onboarding) {
                $data = "\n\n" . $incomingMessage;
                $conversation_data .= $data;
                $this->saveMessage($customer->id, $conversation_data, "received", time());
                $aiMessage = $this->generateAIResponse($conversation_data,true);
                $this->sendMessage($customer->phone, $aiMessage, $customer->id, []);
                return;
            }

            $products = Product::all();
            Log::info('Products: ' . json_encode($products));

            $productData = [];
            $messageLower = strtolower(trim($incomingMessage));

            if (str_contains($messageLower, 'here are the available cooking fuel we have:')) {
                Log::info('messageLower: ' . $messageLower);
                $selectedProduct = null;
                foreach ($products as $product) {
                    $productData[] = $product->name;

                    if (str_contains($messageLower, strtolower($product->name))) {
                        $selectedProduct = $product->name;
                        Log::info('Selected Product: ' . $selectedProduct);
                        break;
                    }
                }

                if ($selectedProduct) {
                    $conversation_data .= "\nSelected Product: " . $selectedProduct;

                    $aiMessage = $this->generateAIResponse($conversation_data);

                    $this->sendMessage($customer->phone, $aiMessage, $customer->id, $productData);

                    $customer->update([
                        'conversation_stage' => $stage + 1,
                        'message_json' => $conversation_data,
                    ]);

                    return;
                }

            }





            $data = "\n\n" . $incomingMessage;
            $conversation_data .= $data .AIHelpers::Tips();
            $predictionKeyword = "Based on the information you provided";
            $requiredEmojis = "😊🌸";



            $aiMessage = $this->generateAIResponse($conversation_data);
            $this->sendMessage($customer->phone, $aiMessage, $customer->id, []);
            if (str_contains($aiMessage, $predictionKeyword) && str_contains($aiMessage, $requiredEmojis)) {
                $customer->update([
                    'conversation_stage' => $customer->conversation_stage+1,
                    'message_json' => $conversation_data,
                    "completed_onboarding"=>true
                ]);
            }else {


                $customer->update([
                    'conversation_stage' => $customer->conversation_stage + 1,
                    'message_json' => $conversation_data,
                ]);
            }

                }
            catch (Exception $e) {
                $customer->update([
                    'conversation_stage' => 0,
                    'message_json' => null,
                ]);
                    Log::error('Error handling conversation: ' . $e->getMessage());

                }
    }

    protected function handleNameInput(Customer $customer, string $incomingMessage): void
    {
        $message = strtolower(trim($incomingMessage));
        $patterns = [
            '/my name is (\w+)/i',
            '/i am (\w+)/i',
            '/it\'s (\w+)/i',
        ];

        $extractedName = null;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $extractedName = $matches[1];
                break;
            }
        }

        if (!$extractedName) {
            $extractedName = $incomingMessage;
        }
        $isValidName = preg_match('/^[a-zA-Z\s]{2,50}$/', $extractedName);

        if ($isValidName) {

            $customer->update(['name' => ucfirst($extractedName)]);

        }
    }



//    protected function handleConversation(Customer $customer, string $incomingMessage): void {
//        try {
//            $stage = $customer->conversation_stage ?? 0;
//            $conversation_data = $customer->message_json ?? "";
//
//            if ($customer->stopChat) {
//                return;
//            }else {
//                if ($customer->completed_onboarding) {
//                    $this->saveMessage($customer->id, $incomingMessage, "received", time());
//                    $aiMessage = $this->generateAIResponse($incomingMessage);
//                    $this->sendMessage($customer->phone, $aiMessage, $customer->id, []);
//                    return;
//                }
//                switch($stage){
//                    case 0:
//
//
//                        $data ="\n\n\n\n".$incomingMessage;
//                        $context = AIHelpers::AIContext($this->displayProductQuestions()).$data;
//                        $aiMessage= $this->generateAIResponse($context);
//
//                        $this->sendMessage($customer->phone, $aiMessage, $customer->id, []);
//
//                        $customer->update(['conversation_stage' => 1,
//                            'current_question_index' => 2,
//                            'questions_json' => $incomingMessage,
//                            "message_json" => $conversation_data,]);
//                        break;
//                    case 1:
//
//                        $data ="\n\n\n\n".$incomingMessage;
//                        $context = AIHelpers::AIContext($this->displayProductQuestions()).$data;
//                        $aiMessage= $this->generateAIResponse($context);
//
//                        $this->sendMessage($customer->phone, $aiMessage, $customer->id, []);
//                        break;
//
//
//
//
//
////                  else{
////                    $conversation_data .= "$incomingMessage\n";
////                    $this->saveMessage($customer->id, $incomingMessage, "received", time());
////                    $aiMessage = $this->generateAIResponse($conversation_data);
////                    $this->sendMessage($customer->phone, $aiMessage, $customer->id, []);
////                    $pattern = '/\b(January|February|March|April|May|June|July|August|September|October|November|December)\s\d{1,2},\s\d{4}\b/';
////                    preg_match_all($pattern, $aiMessage, $matches);
////                    $dates = $matches[0];
////                    $res = implode(", ", $dates);
////                    if (!empty($dates)) {
////                        Log::info("Extracted dates: " . $res);
////                    } else {
////                        Log::info("No dates found in AI message.");
////                    }
////                    $month = Date("m");
////                    $year = Date("Y");
////                    Log::info(" dates *** " . $month);
////                    Log::info(" dates *** " . $year);
////                    $dates_param = http_build_query(['text' => $dates]);
////                    $APIUrl = "https://halimaxcraft.ng/ai/?month=" . $month . "&year=" . $year . "&" . $dates_param;
////
////                    Log::info("APIUrl " . $APIUrl);
////                    $response = Http::withHeaders([
////                        'Content-Type' => 'application/json',
////                    ])->get($APIUrl);
////                    if ($response->successful()) {
////                        $responseBody = $response->json();
////                        $url = $responseBody['url'];
////                        $this->sendMessage($customer->phone, "Here is your prediction image", $customer->id, [], $url);
////                    }
////                    $customer->update([
////                        'conversation_stage' => 0,
////                        'extractedDate' => $dates,
////                        'completed_onboarding' => true,
////                        'questions_json' => null,
////                        'current_question_index' => null,
////                        'message_json' => null,
////                    ]);
//             }
////
//      }
//        } catch (Exception $e) {
//            Log::error('Error handling conversation: ' . $e->getMessage());
//            $customer->update([
//                'conversation_stage' => 0,
//                'questions_json' => null,
//                'current_question_index' => null,
//            ]);
//        }
//    }

    protected function displayProductQuestions(): string
    {
        $products = Product::with('questions')->get();

        $output = '';

        foreach ($products as $product) {
            // Product name
            $output .= $product->name . "\n\n";

            // Questions with roman numeral formatting
            foreach ($product->questions as $index => $question) {
                $romanIndex = $this->toRomanNumerals($index + 1);
                $output .= "{$romanIndex} {$question->question}\n";
            }

            $output .= "\n";
        }

        return $output;
    }

// Helper function to convert numbers to Roman numerals
    protected function toRomanNumerals(int $number): string
    {
        $map = [
            'I', 'II', 'III', 'IV', 'V',
            'VI', 'VII', 'VIII', 'IX', 'X'
        ];

        return $map[$number - 1] ?? (string)$number;
    }


    /**
     * @throws ConnectionException
     */
    public function sendUserMessage(Request $request): \Illuminate\Http\JsonResponse
    {
   try {
       $message = $request->input("message");

       $customer = Customer::where('id', $request->input("customer_id"))->first();
       $message_json = $customer->message_json . ' ' . $message;
       $this->sendMessage($customer->phone, $message, $customer->id, []);
       $customer->update([
           'message_json' => $message_json,
       ]);
       return ResponseUtils::respondWithSuccess("",ResponseAlias::HTTP_OK);
   }catch (Exception $e){
       return ResponseUtils::respondWithError($e->getMessage(), ResponseAlias::HTTP_OK);
   }
    }

    public function stopAiMessage(Request $request): JsonResponse
    {
        try {

            $customer = Customer::where('id', $request->input('customer_id'))->first();

            if ($customer === null) {
                return ResponseUtils::respondWithError(
                    'Customer not found with ID: ' . $request->input('customer_id'),
                    ResponseAlias::HTTP_NOT_FOUND
                );
            }
            $customer->stopChat = true;
            $customer->save();

            return ResponseUtils::respondWithSuccess(
                "AI Chat has been stopped for customer: " . $customer->name,
                ResponseAlias::HTTP_OK
            );

        } catch (Exception $e) {
            Log::error('Error in stopping AI chat: ' . $e->getMessage(), ['exception' => $e]);

            return ResponseUtils::respondWithError(
                'An error occurred while stopping AI chat: ' . $e->getMessage(),
                ResponseAlias::HTTP_INTERNAL_SERVER_ERROR
            );
        }

    }

    /**
     * @throws ConnectionException
     */
    public function cron_job(): string
    {
        $today = Carbon::today()->toDateString();
        $scheduledMessages = ScheduledMessage::whereDate('scheduled_date', $today)->get();
        $customers = Customer::all();

        Log::info("Today's Date: $today");
        Log::info("Scheduled Messages for Today", $scheduledMessages->toArray());
        Log::info("Customer List", $customers->toArray());

        foreach ($scheduledMessages as $scheduledMessage) {
            foreach ($customers as $customer) {

                if ($customer->completed_onboarding) {
                    Log::info("Incomming message to {$customer->phone}");
                    $this->sendMessage($customer->phone, $scheduledMessage->message_content, $customer->id, []);
                    Log::info("Message sent to {$customer->phone}");
                    $scheduledMessage->status = 'sent';
                    $scheduledMessage->save();

                    Log::info("Message sent to {$customer->phone}");
                }
            }
        }
        return "Cron has run";
    }

    /**
     * @throws ConnectionException
     */
    public function cron_job_prediction(): string
    {
        $customers = Customer::all();
        $users = User::all();
        $today = date("F j, Y");

        foreach ($customers as $customer) {
            $datesJson = $customer->extractedDate;
            $dates = json_decode($datesJson, true);


            if ($dates) {
                foreach ($dates as $date) {
                    if ($date === $today) {
                        if ($customer->completed_onboarding) {
                            Log::info("Sending message to {$customer->phone}");
                            $this->sendMessage($customer->phone, "Your next refill is expected today!", $customer->id, []);
                            foreach($users as $user){
                                if ($user->role == UserRole::VENDOR) {
                                    $location = $user->address;
                                    $normalizedLocation = $this->normalizeAddress($location);
                                    $normalizedCustomerAddress = $this->normalizeAddress($customer->location);

                                    $locationWords = preg_split('/\s+/', $normalizedLocation);
                                    $customerWords = preg_split('/\s+/', $normalizedCustomerAddress);
                                    $similarWords = [];

                                    foreach ($locationWords as $locationWord) {
                                        foreach ($customerWords as $customerWord) {
                                            $distance = levenshtein($locationWord, $customerWord);
                                            if ($distance <= 2) {
                                                $similarWords[] = [
                                                    'locationWord' => $locationWord,
                                                    'customerWord' => $customerWord,
                                                    'distance' => $distance
                                                ];
                                            }
                                        }
                                    }

                                    if (!empty($similarWords)) {
                                        $message = "Here is the closest vendor for your product if you still need any of our service \nName: " . $user->name . "\nAddress: " . $user->address . "\nPhone: " . $user->phone;
                                        Log::info("Message: {$message}");

                                        $this->sendMessage($customer->phone, $message, $customer->id, []);
                                    }
                                }

                            }
                            Log::info("Message sent to {$customer->phone}");
                        }
                    }
                }
            } else {
                Log::info("No extracted dates for customer {$customer->id}");
            }
        }

        return "Cron job completed";
    }


  public  function normalizeAddress($address):string {
        return strtolower(preg_replace('/[^a-z0-9\s]/', '', $address));
    }

}
