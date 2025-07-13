<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BinhLuanDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $maBinhLuan;
    public $maBai;

    /**
     * Create a new event instance.
     */
    public function __construct($maBinhLuan, $maBai)
    {
        $this->maBinhLuan = $maBinhLuan;
        $this->maBai = $maBai;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('binh-luan-bai-' . $this->maBai),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith()
    {
        return [
            'maBinhLuan' => $this->maBinhLuan,
            'maBai' => $this->maBai,
        ];
    }
}
