<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/test', function () {
    return response()->json(['message' => 'buen dia']);
}); 

Route::post('/register', [RegisterController::class, 'register']);