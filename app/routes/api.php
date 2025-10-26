<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MetricsController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('jwt.auth')->group(function () {
        Route::post('profile', [AuthController::class, 'profile']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
       
    });
});

Route::middleware('jwt.auth', 'daily.limit')->group(function () {
    Route::post('/messages/send', [MessageController::class, 'send']);
});

Route::middleware('jwt.auth')->group(function () {
    Route::get('/messages', [MessageController::class, 'listMessages']);
});

Route::middleware(['jwt.auth', 'admin.auth'])->group(function () {
    Route::get('/metrics', [MetricsController::class, 'daily']);
});
