<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\EstadoController;
use App\Http\Controllers\Api\MunicipioController;
use App\Http\Controllers\Api\EstacionamientoController;
use App\Http\Controllers\Api\PreguntaController;
use App\Http\Controllers\Api\StripeController;

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
    Route::get('/wallet', [WalletController::class, 'get']);

    Route::get('/estados', [EstadoController::class, 'index']);
    Route::apiResource('municipios', MunicipioController::class);
    Route::apiResource('estacionamientos/estacionamiento', EstacionamientoController::class);
    Route::post('/estacionamientos/estados', [EstacionamientoController::class, 'buscarPorEstado']);
    Route::post('/estacionamientos/cercanos', [EstacionamientoController::class, 'cercanos']);
    Route::post('/estacionamientos/buscar', [EstacionamientoController::class, 'buscarPorNombre']);

    Route::get('/preguntas-frecuentes', [PreguntaController::class, 'index']);

    Route::group([
        'prefix' => 'stripe'
    ], function () {
        Route::post('/create-customer', [StripeController::class, 'createCustomer']);
        Route::get('/create-intent-card', [StripeController::class, 'createIntentCard']);
        Route::get('/get-cards', [StripeController::class, 'getCards']);
        Route::post('/delete-card', [StripeController::class, 'deleteCard']);
        Route::post('/payment', [StripeController::class, 'payment']);
        Route::post('/attach', [StripeController::class, 'attachCard']);
        Route::post('/create-payment-intent', [StripeController::class, 'createPaymentIntent']);
    });
});
