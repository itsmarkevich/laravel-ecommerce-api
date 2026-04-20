<?php

namespace App\Services\Auth;

use App\Models\User;
use Tymon\JWTAuth\JWT;

class TokenService
{
    public function __construct(
        private readonly JWT $jwt,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function login(User $user): array
    {
        return [
            'access_token' => $this->jwt->fromUser($user),
            'token_type' => 'bearer',
        ];
    }

    public function logout(): void
    {
        $token = $this->jwt->getToken();

        if ($token !== null) {
            $this->jwt->setToken($token)->invalidate();
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function refresh(string $token): array
    {
        $newToken = $this->jwt->setToken($token)->refresh();

        return [
            'access_token' => $newToken,
            'token_type' => 'bearer',
        ];
    }
}
