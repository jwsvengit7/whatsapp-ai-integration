<?php

namespace App\Services;
use App\Models\AI;
use Illuminate\Support\Facades\Storage;
use App\Enums\Status;
use App\Enums\UserRole;
use App\Helpers\CustomerUtils;
use App\Helpers\ResponseUtils;
use App\Models\Conversation;
use App\Models\Product;
use App\Models\ScheduledMessage;
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

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
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
                'image'=> 'images/default.png',
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


    /**
     * @throws Exception
     */
    public function createContext(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = CustomerUtils::getJWTUser();

        if ($user == null) {
            return ResponseUtils::respondWithError("User not found.", Response::HTTP_NOT_FOUND);
        }

        if ($user->role == UserRole::SUPER_ADMIN) {
            return ResponseUtils::respondWithError("You don't have permissions to access conversations", Response::HTTP_UNAUTHORIZED);
        }

        $existingAi = AI::where('context', '')->first();

        if ($existingAi) {
            $existingAi->context = $request->input("context");
            $existingAi->save();
            return ResponseUtils::respondWithSuccess($existingAi, Response::HTTP_OK);
        } else {
            $ai = AI::create([
                "context" => $request->input("context"),
            ]);
            return ResponseUtils::respondWithSuccess($ai, Response::HTTP_CREATED);
        }
    }

    /**
     * @throws Exception
     */
    public function getContext(): \Illuminate\Http\JsonResponse
    {
        $user = CustomerUtils::getJWTUser();

        if ($user == null) {
            return ResponseUtils::respondWithError("User not found.", Response::HTTP_NOT_FOUND);
        }

        if ($user->role == UserRole::SUPER_ADMIN) {
            return ResponseUtils::respondWithError("You don't have permissions to access conversations", Response::HTTP_UNAUTHORIZED);
        }

        $existingAi = AI::where('context', '')->first();

        if ($existingAi) {
            return ResponseUtils::respondWithSuccess($existingAi, Response::HTTP_OK);
        } else {

            return ResponseUtils::respondWithSuccess("No Context", Response::HTTP_CREATED);
        }
    }


    public function createScheduledMessage(Request $request): \Illuminate\Http\JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'product_name'=>'required|string',

            'message_content' => 'required|string',
            'scheduled_date' => 'required|date',
        ]);
        try{
            $user= CustomerUtils::getJWTUser();


            if ($validator->fails()) {
            return ResponseUtils::respondWithValidationErrors($validator);
        }
        if ($user->role == UserRole::SUPER_ADMIN && $user->role == UserRole::ADMIN) {
            return ResponseUtils::respondWithError("You don't have permissions to access conversations", Response::HTTP_UNAUTHORIZED);
        }
        $product = Product::where("name",$request->input("product_name"))->first();
        if($product==null){
            return ResponseUtils::respondWithError(
"Product does not found"
                , Response::HTTP_NOT_FOUND);
        }


        $scheduledMessage = ScheduledMessage::create([
            'product_id' => $product->id,
            'message_content' => $request->input('message_content'),
            'scheduled_date' => $request->input('scheduled_date'),
            'status' => 'pending',
        ]);

        return ResponseUtils::respondWithSuccess(

             $scheduledMessage
        , Response::HTTP_OK);
    } catch (Exception $e) {
            return ResponseUtils::respondWithError($e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        }




    public function generateCalendarGif()
    {
        $width = 300;
        $height = 300;

        // Create a blank image
        $image = imagecreatetruecolor($width, $height);

        // Set up colors
        $background = imagecolorallocate($image, 255, 255, 255);
        $textColor = imagecolorallocate($image, 0, 0, 0);

        // Fill the background color
        imagefilledrectangle($image, 0, 0, $width, $height, $background);

        // Add month and year at the top
        $fontSize = 5;
        $margin = 20;
        $monthYear = date("F Y");
        imagestring($image, $fontSize, ($width / 2) - (strlen($monthYear) * imagefontwidth($fontSize) / 2), $margin, $monthYear, $textColor);

        // Days of the week
        $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $dayWidth = ($width - 2 * $margin) / 7;

        // Display day names
        foreach ($daysOfWeek as $i => $day) {
            imagestring($image, $fontSize, $margin + $i * $dayWidth, $margin + 30, $day, $textColor);
        }

        // Calculate the first day of the month and total days in the month
        $firstDayOfMonth = date('w', strtotime(date('Y-m-01')));
        $daysInMonth = date('t');

        $day = 1;
        $yOffset = $margin + 50;
        for ($i = 0; $i < 6; $i++) { // Maximum 6 rows
            for ($j = 0; $j < 7; $j++) {
                if ($i === 0 && $j < $firstDayOfMonth || $day > $daysInMonth) {
                    continue;
                }
                imagestring($image, $fontSize, $margin + $j * $dayWidth, $yOffset + $i * 20, $day, $textColor);
                $day++;
            }
        }

        // File path
        $filePath = storage_path('app/public/calendar.gif');
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true); // Create directory if it doesn't exist
        }
        imagegif($image, $filePath);

        // Clean up
        imagedestroy($image);

        return response()->download($filePath, 'calendar.gif', [
            'Content-Type' => 'image/gif'
        ]);
    }


}
