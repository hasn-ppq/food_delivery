<?php

namespace App\Providers;

use App\Events\OrderStatusChanged;
use App\Listeners\SendOrderStatusNotification;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Notifications\ChannelManager;
use App\Notifications\Channels\FcmChannel;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
      Event::listen(OrderStatusChanged::class, SendOrderStatusNotification::class);
      
       $this->app->make(ChannelManager::class)
        ->extend('fcm', function ($app) {
            return $app->make(FcmChannel::class);
        });
      
    }
}
        