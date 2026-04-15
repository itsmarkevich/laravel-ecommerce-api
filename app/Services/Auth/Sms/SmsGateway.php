<?php

namespace App\Services\Auth\Sms;

interface SmsGateway
{
    public function send(string $phone, string $message): bool;
}
