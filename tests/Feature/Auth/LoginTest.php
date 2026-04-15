<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_user_can_authenticate(): void
    {
        $password = 'password';
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make($password)
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => $password
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                ],
                'access_token',
                'token_type'
            ]);
    }

    public function test_user_cannot_authentication_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                'message',
            ]);
    }

    public function test_user_cannot_authentication_with_missing_fields(): void
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_user_can_logdggdfgfdfdggfdout(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(204)
            ->assertNoContent();
    }
}
