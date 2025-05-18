<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\WalletController;

Route::post('/register',     [AuthController::class, 'register']);
Route::post('/login',        [AuthController::class, 'login']);
Route::post('/verify',       [VerificationController::class, 'verify']);
Route::post('/resend-code',  [VerificationController::class, 'resend']);

// NUEVAS RUTAS DE RECUPERACIÓN
Route::post('/password/send-code', [PasswordController::class, 'sendResetCode']);
Route::post('/password/reset',     [PasswordController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',   [AuthController::class, 'logout']);
    Route::get('/me',        [UserController::class, 'me']);
    Route::put('/me',        [UserController::class, 'update']);
    Route::delete('/user',   [UserController::class, 'deleteUser']);

    Route::put('/wallets/{id}', [WalletController::class, 'update']);
    Route::get('/wallets/{user_id}', [WalletController::class, 'show']);
});