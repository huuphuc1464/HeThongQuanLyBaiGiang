<?php

namespace App\Events;

use App\Models\BinhLuanBaiGiang;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BinhLuanMoi implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $binhLuan;

    /**
     * Create a new event instance.
     */
    public function __construct(BinhLuanBaiGiang $binhLuan)
    {
        $this->binhLuan = $binhLuan;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('binh-luan-bai-' . $this->binhLuan->MaBai),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'binh-luan-moi';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'binhLuan' => $this->binhLuan->load('nguoiGui')->toArray(),
        ];
    }
}
