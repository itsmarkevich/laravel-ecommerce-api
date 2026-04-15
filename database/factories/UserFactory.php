<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->unique()->phoneNumber(),
            'phone_verified_at' => now(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => null,
            'role' => 'user',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function asAdmin(): static
    {
        return $this->state([
            'role' => 'admin',
        ]);
    }

    public function asUser(): static
    {
        return $this->state([
            'role' => 'user',
        ]);
    }

    public function profileCompleteRequired(): static
    {
        return $this->state([
            'name' => null,
            'email' => null,
        ]);
    }

}
