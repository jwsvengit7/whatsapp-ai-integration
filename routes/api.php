<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppController;



/**
 * Handle the creation of a new user.
 * This route allows clients to create a new customer by sending a POST request
 * to /create with the required customer data (email, phone, and name).
 * to /fetch{id} with the required the path variable user id .

 */

    Route::post('/create', [CustomerController::class, 'createUser']);
    Route::get('/fetch/{id}', [CustomerController::class, 'fetchCustomer']);

    /**
     * WEBHOOK API
     *
     */
    Route::get('/webhook/', [WhatsAppController::class, 'receiveMessage']);


