<?php

namespace App\Services\Auth;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenService
{
    /**
     * @return array<string, mixed>
     */
    public function login(User $user): array
    {
        return [
            'access_token' => JWTAuth::fromUser($user),
            'token_type' => 'bearer',
        ];
    }

    public function logout(): void
    {
        $token = JWTAuth::getToken();

        if ($token !== null) {
            JWTAuth::setToken($token)->invalidate();
        }
    }
    /**
     * @return array<string, mixed>
     */
    public function refresh(string $token): array
    {
        $newToken = JWTAuth::setToken($token)->refresh();

        return [
            'access_token' => $newToken,
            'token_type' => 'bearer',
        ];
    }
}
