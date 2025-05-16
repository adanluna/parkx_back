<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('api')->prefix('api')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
    Route::post('/verify',   [VerificationController::class, 'verify']);
    Route::post('/resend-code', [VerificationController::class, 'resend']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [UserController::class, 'me']);
        Route::put('/me',      [UserController::class, 'update']);
        Route::delete('/user', [UserController::class, 'deleteUser']);
    });
});
