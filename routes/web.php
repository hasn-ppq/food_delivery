<?php
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Models\User;

use App\Models\Order;



Route::get('/register', function () {
    return view('auth.register');
})->name('register.form');

Route::post('/register', [RegisterController::class, 'store'])
    ->name('register.store');




//