<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/{any}', function () {
    return view('welcome'); // Your main Vue app view
})->where('any', '.*');


