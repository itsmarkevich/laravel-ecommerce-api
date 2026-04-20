<?php

namespace App\Services\Auth;

use App\Services\Auth\Sms\SmsGateway;
use Illuminate\Support\Facades\Cache;

class PhoneVerificationService
{
    public function __construct(private SmsGateway $smsGateway) {}

    public function sendCode(string $phone): void
    {
        $code = random_int(100000, 999999);

        Cache::put(
            "sms:code:{$phone}",
            $code,
            300
        );

        $this->smsGateway->send($phone, "Ваш код подтверждения: {$code}");
    }

    public function verifyCode(string $phone, string $code): bool
    {
        $cachedCode = Cache::get("sms:code:{$phone}");

        if ($cachedCode === null || (string) $cachedCode !== $code) {
            return false;
        }

        Cache::forget("sms:code:{$phone}");

        return true;
    }
}
