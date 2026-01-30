<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\InventoryItemController;
use App\Http\Controllers\API\StockTransferController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('logout', [AuthController::class, 'logout']);

    Route::get('inventory', [InventoryItemController::class, 'index']);
    Route::get('warehouses/{id}/inventory', [InventoryItemController::class, 'index']);

    Route::post('stock-transfers', [StockTransferController::class, 'store']);
});


