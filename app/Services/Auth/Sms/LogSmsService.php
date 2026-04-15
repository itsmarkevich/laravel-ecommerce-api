<?php

namespace App\Services\Auth\Sms;

use Illuminate\Support\Facades\Log;

class LogSmsService implements SmsGateway
{
    public function send(string $phone, string $message): bool
    {
        Log::channel('sms')->info("Sms to {$phone}: {$message}");
        return true;
    }
}
