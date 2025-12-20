<?php
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\MealController;


// Public Auth Routes
Route::post('/auth/send-otp', [AuthController::class, 'sendOtp']);
Route::post('auth/verify-otp', [AuthController::class, 'verifyOtp']);


// Protected Routes
Route::middleware(['auth:sanctum', 'customer'])->group(function () { 
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/restaurants', [RestaurantController::class, 'index']);
    Route::get('/restaurants/{restaurant}/meals', [MealController::class, 'index']);
    Route::get('/my-orders', [OrderController::class, 'myOrders']);

});

