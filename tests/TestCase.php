<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\JWT;

abstract class TestCase extends BaseTestCase
{
    public function actingAsJWT(User $user): self
    {
        /** @var JWT $jwt */
        $jwt = app(JWT::class);

        $token = $jwt->fromUser($user);

        return $this->withHeader('Authorization', "Bearer {$token}");
    }
}
