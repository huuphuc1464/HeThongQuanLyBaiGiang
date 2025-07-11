<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class GuiEmailBccJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected array $bccList;
    protected string $subject;
    protected string $body;

    public function __construct(array $bccList, string $subject, string $body)
    {
        $this->bccList = $bccList;
        $this->subject = $subject;
        $this->body = $body;
    }

    public function handle(): void
    {
        Mail::send([], [], function ($message) {
            $message->subject($this->subject);
            $message->bcc($this->bccList);
            $message->html($this->body);
        });
    }
}
