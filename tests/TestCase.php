<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    public function actingAsJWT(User $user): self
    {
        $token = JWTAuth::fromUser($user);

        return $this->withHeader('Authorization', "Bearer $token");
    }
}
