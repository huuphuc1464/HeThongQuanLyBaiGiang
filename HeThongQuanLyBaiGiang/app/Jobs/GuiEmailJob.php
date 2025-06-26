<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class GuiEmailJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected string $toEmail;
    protected string $subject;
    protected string $body;

    public function __construct(string $toEmail, string $subject, string $body)
    {
        $this->toEmail = $toEmail;
        $this->subject = $subject;
        $this->body = $body;
    }

    public function handle(): void
    {
        Mail::send([], [], function ($message) {
            $message->to($this->toEmail)
                ->subject($this->subject)
                ->html($this->body);
        });
    }
}