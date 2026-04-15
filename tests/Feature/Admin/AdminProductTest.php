<?php

namespace Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    protected string $apiBase = '/api/v1/admin';

    /**
     * A basic feature test example.
     */
    public function test_admin_can_get_products_list(): void
    {
        $admin = User::factory()
            ->asAdmin()
            ->create();

        Category::factory()
            ->has(Product::factory()->count(2))
            ->count(2)
            ->create();

        $response = $this->actingAsJWT($admin)
            ->get("{$this->apiBase}/products");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'price',
                        'weight',
                        'category' => [
                            'id',
                            'name',
                            'slug'
                        ]
                    ]
                ]
            ]);
    }

    public function test_admin_middleware_blocks_non_admins(): void
    {
        $admin = User::factory()
            ->asUser()
            ->create();

        $this->actingAsJWT($admin)
            ->get("{$this->apiBase}/products")
            ->assertStatus(403);
    }

    public function test_admin_can_create_product()
    {
        $admin = User::factory()
            ->asAdmin()
            ->create();

        $category = Category::factory()->create();

        $product = Product::factory()
            ->for($category)
            ->make()
            ->toArray();

        $response = $this->actingAsJWT($admin)
            ->postJson("{$this->apiBase}/products", $product);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'slug',
                'description',
                'price',
                'weight',
                'category' => [
                    'id',
                    'name',
                    'slug'
                ]
            ]);

        $this->assertDatabaseHas('products', [
            'name' => $product['name'],
            'category_id' => $product['category_id'],
        ]);
    }

    public function test_admin_cannot_create_invalid_product(): void
    {
        $admin = User::factory()
            ->asAdmin()
            ->create();

        $category = Category::factory()->create();

        $product = Product::factory()
            ->for($category)
            ->make([
                'name' => null,
            ])
            ->toArray();

        $response = $this->actingAsJWT($admin)
            ->postJson("{$this->apiBase}/products", $product);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'name'
                ]
            ]);
    }

    public function test_admin_can_get_specific_product(): void
    {
        $admin = User::factory()
            ->asAdmin()
            ->create();

        $product = Product::factory()
            ->for(Category::factory())
            ->create();

        $response = $this->actingAsJWT($admin)
            ->get("{$this->apiBase}/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'price',
                    'weight',
                    'category' => [
                        'id',
                        'name',
                        'slug'
                    ]
                ]
            ]);
    }

    public function test_returns_404_for_nonexistent_product_on_show(): void
    {
        $admin = User::factory()
            ->asAdmin()
            ->create();

        $nonExistentId = 999999;

        $this->actingAsJWT($admin)
            ->get("{$this->apiBase}/products/{$nonExistentId}")
            ->assertStatus(404);
    }

    public function test_admin_can_update_product()
    {
        $admin = User::factory()
            ->asAdmin()
            ->create();

        $product = Product::factory()
            ->for(Category::factory())
            ->create();

        $newCategory = Category::factory()->create();

        $data = [
            'name' => 'new name',
            'description' => 'new description',
            'price' => 600.00,
            'category_id' => $newCategory->id,
        ];

        $response = $this->actingAsJWT($admin)
            ->putJson("{$this->apiBase}/products/{$product->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'new name',
                'description' => 'new description',
                'price' => 600.00,
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'new name',
            'description' => 'new description',
            'price' => 600.00,
            'category_id' => $newCategory->id,
        ]);
    }

    public function test_admin_cannot_update_product_with_invalid_data(): void
    {
        $admin = User::factory()
            ->asAdmin()
            ->create();

        $product = Product::factory()
            ->for(Category::factory())
            ->create();

        $data = [
            'name' => '',
            'price' => 600,
            'description' => '',
            'weight' => 'one pound',
        ];

        $this->actingAsJWT($admin)
            ->putJson("{$this->apiBase}/products/{$product->id}", $data)
            ->assertStatus(422);
    }

    public function test_admin_cannot_update_nonexistent_product(): void
    {
        $admin = User::factory()
            ->asAdmin()
            ->create();

        $nonExistentId = 111111;

        $data = [
            'name' => 'new name',
            'description' => 'new description',
            'price' => 600.00,
        ];

        $this->actingAsJWT($admin)
            ->putJson("{$this->apiBase}/products/{$nonExistentId}", $data)
            ->assertStatus(404);
    }

    public function test_admin_can_delete_specific_product(): void
    {
        $admin = User::factory()
            ->asAdmin()
            ->create();

        $product = Product::factory()
            ->for(Category::factory())
            ->create();

        $this->actingAsJWT($admin)
            ->deleteJson("{$this->apiBase}/products/{$product->id}")
            ->assertStatus(204);
    }

    public function test_admin_cannot_delete_nonexistent_product(): void
    {
        $admin = User::factory()
            ->asAdmin()
            ->create();

        $nonExistentId = 111111;

        $this->actingAsJWT($admin)
            ->deleteJson("{$this->apiBase}/products/{$nonExistentId}")
            ->assertStatus(404);
    }
}
