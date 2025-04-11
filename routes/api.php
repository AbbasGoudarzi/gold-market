<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::prefix('orders')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [OrderController::class, 'store']);
    Route::post('/{order}/cancel', [OrderController::class, 'cancel']);
});
