<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
// use Symfony\Component\Mime\Email;

class EmailService
{
    public function sendEmail(string $toEmail, string $subject, string $body): bool
    {
        try {
            Mail::send([], [], function ($message) use ($toEmail, $subject, $body) {
                $message->to($toEmail)
                    ->subject($subject)
                    ->html($body);
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Lỗi gửi email: ' . $e->getMessage());
            return false;
        }
    }
}