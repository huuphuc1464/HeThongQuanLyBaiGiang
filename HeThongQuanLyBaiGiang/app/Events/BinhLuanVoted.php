<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BinhLuanVoted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $maBinhLuan;
    public $soUpvote;
    public $soDownvote;

    public function __construct($maBinhLuan, $soUpvote, $soDownvote)
    {
        $this->maBinhLuan = $maBinhLuan;
        $this->soUpvote = $soUpvote;
        $this->soDownvote = $soDownvote;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('binh-luan-vote'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'maBinhLuan' => $this->maBinhLuan,
            'soUpvote' => $this->soUpvote,
            'soDownvote' => $this->soDownvote,
        ];
    }
}
