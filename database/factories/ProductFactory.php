<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'Продукт ' . $this->faker->unique()->numberBetween(1, 9999);

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . uniqid(),
            'description' => fake()->text(400),
            'price' => fake()->randomFloat(2, 100, 1500),
            'weight' => fake()->numberBetween(200, 1500),
        ];
    }
}
