<?php

namespace App\Services\Auth;

use App\Models\User;

class TokenService
{
    public function login(User $user): array
    {
        return [
            'access_token' => auth('api')->login($user),
            'token_type' => 'bearer',
        ];
    }

    public function logout(): void
    {
        auth('api')->logout();
    }

    public function refresh(string $token): array
    {

        $newToken = auth('api')->setToken($token)->refresh();

        return [
            'access_token' => $newToken,
            'token_type' => 'bearer',
        ];
    }
}
