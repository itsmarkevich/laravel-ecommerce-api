<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Services\Auth\Sms\SmsGateway;
use App\Services\Auth\TokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected string $apiBase = '/api/v1/auth';

    /**
     * A basic feature test example.
     */
    public function test_send_code_normalization_and_success(): void
    {
        $phone = '89991234567';
        $normalizedPhone = '+79991234567';

        $this->mock(SmsGateway::class)->shouldReceive('send')->once();

        $this->postJson("{$this->apiBase}/send-code", [
            'phone' => $phone
        ])->assertJson([
            'success' => true,
            'message' => 'Код отправлен',
        ])->assertStatus(200);

        $cachedCode = Cache::get("sms:code:{$normalizedPhone}");

        $this->assertNotNull($cachedCode);

        $this->assertEquals(6, strlen($cachedCode));
    }

    public function test_send_code_with_invalid_phone(): void
    {
        $phone = '89991234';

        $this->postJson("{$this->apiBase}/send-code", [
            'phone' => $phone
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }

    public function test_verify_code_success(): void
    {
        $phone = '+79991234567';

        $code = 123456;

        Cache::put("sms:code:{$phone}", $code, 300);

        $this->mock(TokenService::class)
            ->shouldReceive('login')
            ->once()
            ->andReturn([
                'access_token' => 'fake_token',
                'token_type' => 'bearer',
                'expires_in' => 3600
            ]);

        $this->postJson("{$this->apiBase}/verify-code", [
            'phone' => $phone,
            'code' => $code,
        ])
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'phone',
                    'email',
                ],
                'requires_profile_completion',
                'access_token',
                'token_type',
            ])
            ->assertJsonFragment([
                'phone' => $phone,
                'requires_profile_completion' => true
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('users', ['phone' => $phone]);

        $user = User::wherePhone($phone)->first();
        $this->assertNotNull($user->phone_verified_at);

        $this->assertFalse(Cache::has("sms:code:{$phone}"));
    }

    public function test_verify_code_fails_with_invalid_code(): void
    {
        $phone = '+79991234567';

        $code = 123456;

        Cache::put("sms:code:{$phone}", $code, 300);

        $this->postJson("{$this->apiBase}/verify-code", [
            'phone' => $phone,
            'code' => 333222,
        ])
            ->assertJsonFragment([
                'message' => 'Неверный код'
            ])
            ->assertStatus(401);

        $this->assertDatabaseMissing('users', ['phone' => $phone]);

        $this->assertTrue(Cache::has("sms:code:{$phone}"));
    }

    public function test_complete_user_profile_success(): void
    {
        $name = 'John';
        $email = 'johndoe19@mail.ru';

        $user = User::factory()
            ->profileCompleteRequired()
            ->create();

        $this->actingAsJWT($user);

        $this->patchJson("{$this->apiBase}/complete-profile", [
            'name' => $name,
            'email' => $email,
        ])
            ->assertJsonFragment([
                'message' => 'Профиль успешно заполнен'
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('users', ['name' => $name, 'email' => $email]);
    }

    public function test_complete_user_profile_fails_with_invalid_data(): void
    {
        $name = 'John';
        $email = 'johndoe19@mail.ru';

        //existing user
        User::factory()->create([
            'email' => $email,
        ]);

        $user = User::factory()
            ->profileCompleteRequired()
            ->create();

        $this->actingAsJWT($user);

        $this->patchJson("{$this->apiBase}/complete-profile", [
            'name' => $name,
            'email' => $email,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_refresh_token_success(): void
    {
        $token = 'fake_token';

        $mock = Mockery::mock(TokenService::class);

        $mock->shouldReceive('refresh')
            ->once()
            ->with($token)
            ->andReturn([
                'access_token' => 'new_access_token',
                'token_type' => 'bearer',
                'expires_in' => 3600
            ]);

        $this->swap(TokenService::class, $mock);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("{$this->apiBase}/refresh");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in'
            ]);
    }

    public function test_refresh_token_not_provided(): void
    {
        $token = '';

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("{$this->apiBase}/refresh")
            ->assertStatus(401);
        $response->assertJsonFragment([
            'message' => 'Token not provided'
        ]);
    }

    public function test_refresh_token_fails_with_invalid_data(): void
    {
        $token = 'fake_token';

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("{$this->apiBase}/refresh")
            ->assertStatus(401);

        $response->assertJsonFragment([
            'error' => 'Invalid token'
        ]);
    }

    public function test_token_cannot_be_refreshed(): void
    {
        $token = 'fake_token';

        $mock = Mockery::mock(TokenService::class);

        $mock->shouldReceive('refresh')
            ->once()
            ->with($token)
            ->andThrow(new TokenExpiredException());

        $this->swap(TokenService::class, $mock);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("{$this->apiBase}/refresh")
            ->assertStatus(401);

        $response->assertJsonFragment([
            'error' => 'Token cannot be refreshed'
        ]);
    }

    public function test_token_blacklisted(): void
    {
        $token = 'fake_token';

        $mock = Mockery::mock(TokenService::class);

        $mock->shouldReceive('refresh')
            ->once()
            ->with($token)
            ->andThrow(new TokenBlacklistedException);

        $this->swap(TokenService::class, $mock);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("{$this->apiBase}/refresh")
            ->assertStatus(401);

        $response->assertJsonFragment([
            'error' => 'Token blacklisted'
        ]);
    }

    public function test_user_can_logout(): void
    {

        $user = User::factory()
            ->asUser()
            ->create();

        $this->actingAsJWT($user);

        $this->mock(TokenService::class)
            ->shouldReceive('logout')
            ->once();

        $this->postJson("{$this->apiBase}/logout")
            ->assertNoContent();
    }
}
