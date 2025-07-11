<?php

namespace App\Services;

use App\Jobs\GuiEmailJob;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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

    public function sendEmailBcc(array $bccList, string $subject, string $body): bool
    {
        try {
            dispatch(new \App\Jobs\GuiEmailBccJob($bccList, $subject, $body));
            return true;
        } catch (\Exception $e) {
            Log::error('Lỗi gửi email BCC: ' . $e->getMessage());
            return false;
        }
    }
}
