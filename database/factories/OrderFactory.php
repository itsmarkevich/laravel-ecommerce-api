<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'delivery_type' => 'courier',
            'delivery_region' => $this->faker->word(),
            'delivery_city' => $this->faker->city(),
            'delivery_street' => $this->faker->streetName(),
            'delivery_house' => (string) $this->faker->numberBetween(1, 200),
            'delivery_entrance' => (string) $this->faker->numberBetween(1, 10),
            'delivery_apartment' => (string) $this->faker->numberBetween(1, 300),
            'delivery_postal_code' => $this->faker->postcode(),
            'description' => $this->faker->optional()->sentence(),
            'status' => 'created',
        ];
    }
}
