<?php

use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\MealController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;

// =======================
// Public Auth Routes
// =======================
Route::post('/auth/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);

// =======================
// Customer Routes (Mobile App)
// =======================
Route::middleware(['auth:sanctum', 'customer'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // 🔔 حفظ device token (خاص بالموبايل)
    Route::post('/device-token', function (Request $request) {
        $request->validate([
            'device_token' => 'required|string',
        ]);

        $request->user()->update([
            'device_token' => $request->device_token,
        ]);

        return response()->json([
            'message' => 'Device token saved successfully',
        ]);
    });

    // =======================
    // Orders
    // =======================
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/my-orders', [OrderController::class, 'myOrders']);
    Route::get('/my-orders/history', [OrderController::class, 'myOrdersHistory']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);

    // =======================
    // Restaurants & Meals
    // =======================
    Route::get('/restaurants', [RestaurantController::class, 'index']);
    Route::get('/restaurants/{restaurant}/meals', [MealController::class, 'index']);
});
