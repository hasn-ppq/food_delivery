<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\SendFcmNotificationJob;

class SendRestaurantOrderNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderStatusChanged $event): void
    {
        
        $restaurant = $event->order->restaurant;

    // مثال إرسال إشعار FCM 
    if ($restaurant->device_token) {
        // dispatch job to send FCM notification
        dispatch(new SendFcmNotificationJob(
            $restaurant->device_token,
            "طلب جديد",
            "وصل طلب من رقم {$event->order->id}"
        ));
    }
}
}