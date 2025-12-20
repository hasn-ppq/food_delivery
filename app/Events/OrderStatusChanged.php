<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
 public $order;
    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("order.{$this->order->id}"),
            new PrivateChannel("restaurant.{$this->order->restaurant_id}"),
            new PrivateChannel("user.{$this->order->user_id}"),
            $this->order->driver_id ? new PrivateChannel("driver.{$this->order->driver_id}") : null,
            
        ];
    }
     public function broadcastAs()
    {
         return 'order.updated';
    }
}
