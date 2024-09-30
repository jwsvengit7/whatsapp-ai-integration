<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
 * WEBHOOK API
 *
 *
 */



/**
 * Handle the creation of a new user.
 *
 * This route allows clients to create a new customer by sending a POST request
 * to /create with the required customer data (email, phone, and name).
 * to /fetch{id} with the required the path variable user id .

 */

    Route::post('/create', [CustomerController::class, 'createUser']);

// Route for fetching a customer by ID
    Route::get('/fetch/{id}', [CustomerController::class, 'fetchCustomer']);

