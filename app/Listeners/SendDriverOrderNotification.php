<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\OrderStatusChanged;
use App\Jobs\SendFcmNotificationJob;


class SendDriverOrderNotification
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
    public function handle(object $event): void
    {
          if ($event->order->driver) {
            SendFcmNotificationJob::dispatch(
                $event->order->driver->device_token,
                "تحديث حالة الطلب",
                "حالة الطلب أصبحت: {$event->order->status}"
            );
        }
    
    }
}
