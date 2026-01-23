<?php
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Models\User;
use App\Notifications\OrderStatusOwnerNotification;
use App\Models\Order;

Route::get('/test-notification', function () {

    $user = User::find(15); // غيّر ID حسب المستخدم المسجّل بالبنل
    $order = Order::first(); // أي طلب موجود

    $user->notify(
        new OrderStatusOwnerNotification($order)
    );

    return 'sent';
});



Route::get('/register', function () {
    return view('auth.register');
})->name('register.form');

Route::post('/register', [RegisterController::class, 'store'])
    ->name('register.store');




//