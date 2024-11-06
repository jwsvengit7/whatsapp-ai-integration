<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\UserController;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * API V1 Grouping for Customer, User, Webhook Endpoints
 */
Route::group(['middleware' => 'api', 'prefix' => 'v1'], function () {

    // Customer Endpoints
    Route::post('/create', [CustomerController::class, 'createCustomer']);
    Route::get('/fetch/{id}', [CustomerController::class, 'fetchCustomer']);

    Route::get('/all-customer', [CustomerController::class, 'fetchAllCustomer']);

    // User Endpoints
    Route::post('/create-user', [UserController::class, 'createAccount']);
    Route::get('/fetch-user', [UserController::class, 'fetchUser']);
    Route::post('/update-user', [UserController::class, 'updateAccount']);
    Route::post('/login', [UserController::class, 'loginAuth']);
    Route::post('/resend', [UserController::class, 'resendVerification']);
    Route::post('/verify-otp', [UserController::class, 'confirmAccount']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
    Route::post('/forget-password', [UserController::class, 'forgetPassword']);
    Route::get('/verify-link/{token}', [UserController::class, 'verifyLink']);
    Route::get("/referer-link/{link}",[UserController::class,'refererLink']);


    // Admin

    Route::post('/create-admin', [AdminController::class, 'createAdmin']);
    Route::get('/all-users', [AdminController::class, 'fetchAllUsers']);
    Route::post('/create-product', [AdminController::class, 'createProduct']);
    Route::post('/create-context', [AdminController::class, 'createContext']);
    Route::get('/context', [AdminController::class, 'getContext']);
    Route::get("fetch-chat",[AdminController::class,"fetchConversation"]);
    Route::post("schedule-message",[AdminController::class,"createScheduledMessage"]);
    Route::get("/image",[AdminController::class,"generateCalendarGif"]);
    Route::post("/message",[WhatsAppController::class,"sendUserMessage"]);
    Route::post("/stop-ai",[WhatsAppController::class,"stopAiMessage"]);

    // Webhook Endpoints
    Route::match(['get', 'post'], '/webhook', [WhatsAppController::class, 'handleWebhook']);
    Route::post('/sendAi', [WhatsAppController::class, 'handleOpenAI']);

    Route::get('/fetch-product', [ProductController::class, 'fetchProduct']);
    Route::post('/add-product', [ProductController::class, 'addProduct']);
    Route::put("/update-product",[ProductController::class,'updateProduct']);
    Route::delete("/delete-product",[ProductController::class,'deleteProduct']);


});
