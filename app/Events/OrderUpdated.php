<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function broadcastOn()
    {
        return new Channel('restaurant-orders');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->order->id,
            'status' => $this->order->status,
            'message' => "Order #{$this->order->id} is now {$this->order->status}!"
        ];
    }

    /**
     * This is the "Magic Fix" for React.
     * It forces the event name to be 'OrderUpdated' 
     * instead of 'App\Events\OrderUpdated'
     */
    public function broadcastAs()
    {
        return 'OrderUpdated';
    }
}