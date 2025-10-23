<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;


Route::get('/test', function () {
    return response()->json(['message' => 'buen dia']);
}); 

Route::post('/register', [RegisterController::class, 'register']);

Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);

Route::post('/messages/send', [\App\Http\Controllers\MessageController::class, 'send']);