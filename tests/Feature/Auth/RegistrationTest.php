<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_register(): void
    {
        $user = User::factory()->make();
        $password = 'password';
        $userData = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $password,
            'password_confirmation' => $password
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                ],
                'access_token',
                'token_type'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $user->email
        ]);
    }

    public function test_registration_fails_with_short_password(): void
    {
        $user = User::factory()->make();
        $password = 'pass';
        $userData = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $password,
            'password_confirmation' => $password
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'password'
                ]
            ]);

        $this->assertDatabaseMissing('users', [
            'email' => $user->email
        ]);
    }

    public function test_registration_fails_with_missing_fields(): void
    {
        $user = User::factory()->make();
        $password = 'pass';
        $userData = [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => ''
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'name',
                    'email',
                    'password',
                    'password_confirmation'
                ]
            ]);

        $this->assertDatabaseMissing('users', [
            'email' => $user->email
        ]);
    }

    public function test_registration_fails_with_invalid_email(): void
    {
        $user = User::factory()->make();
        $password = 'pass';
        $userData = [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => ''
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'email'
                ]
            ]);

        $this->assertDatabaseMissing('users', [
            'email' => $user->email
        ]);
    }
}
