<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SystemApiController;

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/drinks', [SystemApiController::class, 'getDrinks']);
    Route::get('/drinks/{id}', [SystemApiController::class, 'getDrink']);
});

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/orders', [SystemApiController::class, 'getUserOrders']);
    Route::post('/orders', [SystemApiController::class, 'createOrder']);
});

Route::middleware(['auth:sanctum', 'admin', 'throttle:60,1'])->group(function () {
    Route::delete('/drinks/{id}', [SystemApiController::class, 'deleteDrink']);
    Route::get('/system/stats', [SystemApiController::class, 'getSystemStats']);
});
