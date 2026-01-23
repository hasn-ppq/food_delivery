<?php

use Illuminate\Support\Facades\Broadcast;


/*
|--------------------------------------------
| Order channel
|--------------------------------------------
*/
Broadcast::channel('order.{orderId}', function ($user, $orderId) {
    return true; // أو تحقق صلاحيات
});

/*
|--------------------------------------------
| Customer channel
|--------------------------------------------
*/
Broadcast::channel('customer.{customerId}', function ($user, $customerId) {
    return (int) $user->id === (int) $customerId;
});

/*
|--------------------------------------------
| Restaurant channel
|--------------------------------------------
*/
Broadcast::channel('restaurant.{restaurantId}', function ($user, $restaurantId) {
    return $user->restaurant?->id === (int) $restaurantId;
});



Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

