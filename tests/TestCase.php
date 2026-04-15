<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function actingAsJWT($user, $guard = 'api'): static
    {
        $token = auth($guard)->login($user);
        $this->withHeader('Authorization', 'Bearer ' . $token);
        return $this;
    }
}
