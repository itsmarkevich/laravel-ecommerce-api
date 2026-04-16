<?php

namespace Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected string $apiBase = '/api/v1';

    /**
     * A basic feature test example.
     */
    public function test_can_get_menu(): void
    {
        Category::factory()
            ->has(Product::factory()->count(2))
            ->count(2)
            ->create();

        $response = $this->get("{$this->apiBase}/menu");

        $response->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                            'products' => [
                                '*' => [
                                    'id',
                                    'name',
                                    'slug',
                                    'description',
                                    'price',
                                    'weight',
                                ],
                            ],
                        ],
                    ],
                ]
            );
    }

    public function test_returns_empty_menu_when_no_categories(): void
    {
        $response = $this->get("{$this->apiBase}/menu");

        $response->assertStatus(200);
        $response->assertJson(['data' => []]);
    }

    public function test_can_get_specific_category(): void
    {
        $category = Category::factory()
            ->has(Product::factory()->count(1))
            ->create();

        $response = $this->get("{$this->apiBase}/menu/{$category->slug}");

        $response->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'name',
                        'slug',
                        'products' => [
                            '*' => [
                                'id',
                                'name',
                                'slug',
                                'description',
                                'price',
                                'weight',
                            ],
                        ],
                    ],
                ]
            )
            ->assertJsonFragment(
                [
                    'id' => $category->id,
                    'slug' => $category->slug,
                ]
            );
    }

    public function test_returns_404_for_nonexistent_category(): void
    {
        $response = $this->get("{$this->apiBase}/menu/non-existent-category-12345");

        $response->assertStatus(404);
    }

    public function test_can_get_specific_product()
    {
        $category = Category::factory()
            ->has(Product::factory()->count(1))
            ->create();

        $product = $category->products->first();

        $response = $this->get("{$this->apiBase}/menu/{$category->slug}/{$product->slug}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'price',
                    'weight',
                ],
            ])
            ->assertJsonFragment(
                [
                    'id' => $product->id,
                    'slug' => $product->slug,
                ]
            );
    }

    public function test_returns_404_for_nonexistent_product_in_existing_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->get("{$this->apiBase}/menu/{$category->slug}/non-existent-product");

        $response->assertStatus(404);
    }
}
