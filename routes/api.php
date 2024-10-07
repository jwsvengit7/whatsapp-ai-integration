<?php

use App\Http\Controllers\CustomerController;
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

    // User Endpoints
    Route::post('/create-user', [UserController::class, 'createAccount']);
    Route::get('/fetch-user/{id}', [UserController::class, 'fetchUser']);

    Route::post('/login', [UserController::class, 'loginAuth']);
    Route::post('/resend', [UserController::class, 'resendVerification']);
    Route::post('/verify-otp', [UserController::class, 'confirmAccount']);
    Route::post('/change-password', [UserController::class, 'changePassword']);

    // Webhook Endpoints
    Route::match(['get', 'post'], '/webhook', [WhatsAppController::class, 'handleWebhook']);
    Route::post('/sendAi', [WhatsAppController::class, 'handleOpenAI']);

});
