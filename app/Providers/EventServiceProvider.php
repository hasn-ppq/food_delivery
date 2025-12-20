<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\OrderStatusChanged;
use App\Listeners\SendDriverOrderNotification;
use App\Listeners\SendOrderStatusNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
    OrderStatusChanged::class => [
        SendRestaurantNotification::class,
        SendDriverNotification::class,
        SendUserNotification::class,
    ],
];
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
