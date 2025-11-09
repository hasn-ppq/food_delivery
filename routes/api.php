<?php
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
});

// Restaurant Owner routes
Route::middleware(['auth:sanctum', 'role:restaurant_owner'])->group(function () {
    Route::get('/restaurant/orders', [OrderController::class, 'restaurantOrders']);
});

// Driver routes
Route::middleware(['auth:sanctum', 'role:driver'])->group(function () {
    Route::get('/driver/orders', [OrderController::class, 'driverOrders']);
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus']);
});

// Admin routes
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
});