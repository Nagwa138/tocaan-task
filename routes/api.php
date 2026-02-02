<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PaymentController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('logout', [AuthController::class, 'logout']);

    // Orders
    Route::apiResource('orders', OrderController::class);
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::post('orders/{order}/confirm', [OrderController::class, 'confirm']);

    // Payments
    Route::get('payments', [PaymentController::class, 'index']);
    Route::get('payments/gateways', [PaymentController::class, 'gateways']);
    Route::post('orders/{order}/payments/process', [PaymentController::class, 'process']);
});


