<?php

namespace App\Events;

use App\Models\BoostingOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BoostingOrderUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Đơn hàng cày thuê đã được cập nhật
     */
    public $order;

    /**
     * Create a new event instance.
     */
    public function __construct(BoostingOrder $order)
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
            new PrivateChannel('channel-name'),
        ];
    }
} 