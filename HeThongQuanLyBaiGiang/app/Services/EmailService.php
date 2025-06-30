<?php

namespace App\Services;

use App\Jobs\GuiEmailJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function sendEmail(string $toEmail, string $subject, string $body): bool
    {
        try {
            GuiEmailJob::dispatch($toEmail, $subject, $body);
            return true;
        } catch (\Exception $e) {
            Log::error('Lỗi gửi email: ' . $e->getMessage());
            return false;
        }
    }
}
