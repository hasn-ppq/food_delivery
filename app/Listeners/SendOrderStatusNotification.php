<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\SendFcmNotificationJob;


class SendOrderStatusNotification
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
          $order = $event->order;

        // إرسال Job لمنع التأخير
        SendFcmNotificationJob::dispatch(
            $order->customer->fcm_token,
            "تغيير حالة الطلب",
            "تم تغيير حالة طلبك إلى: " . $order->status
        );
    }
}
