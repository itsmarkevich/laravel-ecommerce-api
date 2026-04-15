<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected string $apiBase = '/api/v1/admin';

    /**
     * A basic feature test example.
     */
    public function test_admin_can_get_categories_list(): void
    {
        $admin = User::factory()
            ->asAdmin()
            ->create();

        Category::factory()
            ->count(3)
            ->create();

        $response = $this->actingAsJWT($admin)
            ->get("{$this->apiBase}/categories");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
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
            ->get("{$this->apiBase}/categories")
            ->assertStatus(403);
    }

    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()
            ->asAdmin()
            ->create();

        $category = Category::factory()
            ->make()
            ->toArray();

        $response = $this->actingAsJWT($admin)
            ->postJson("{$this->apiBase}/categories", $category);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'slug',
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => $category['name'],
        ]);
    }

    public function test_admin_cannot_create_invalid_category(): void
    {
        $admin = User::factory()
            ->asAdmin()
            ->create();

        $category = Category::factory()
            ->make([
                'name' => '',
            ])
            ->toArray();

        $this->actingAsJWT($admin)
            ->postJson("{$this->apiBase}/categories", $category)
            ->assertStatus(422);
    }

    public function test_admin_can_get_specific_category(): void
    {
        $admin = User::factory()
            ->asAdmin()
            ->create();

        $category = Category::factory()
            ->create();

        $response = $this->actingAsJWT($admin)
            ->get("{$this->apiBase}/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'slug',
                ]
            ]);
    }

    public function test_returns_404_for_nonexistent_category_on_show(): void
    {
        $admin = User::factory()
            ->asAdmin()
            ->create();

        $nonExistentId = 999999;

        $this->actingAsJWT($admin)
            ->get("{$this->apiBase}/categories/{$nonExistentId}")
            ->assertStatus(404);
    }

    public function test_admin_can_update_category(): void
    {
        $admin = User::factory()
            ->asAdmin()
            ->create();

        $category = Category::factory()
            ->create();

        $data = [
            'name' => 'new category name',
        ];

        $response = $this->actingAsJWT($admin)
            ->putJson("{$this->apiBase}/categories/{$category->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'new category name',
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'new category name',
        ]);
    }

    public function test_admin_cannot_update_category_with_invalid_data(): void
    {
        $admin = User::factory()
            ->asAdmin()
            ->create();

        $category = Category::factory()
            ->create();

        $data = [
            'name' => '',
        ];

        $this->actingAsJWT($admin)
            ->putJson("{$this->apiBase}/categories/{$category->id}", $data)
            ->assertStatus(422);
    }

    public function test_admin_cannot_update_nonexistent_category(): void
    {
        $admin = User::factory()
            ->asAdmin()
            ->create();

        $nonExistentId = 111111;

        $data = [
            'name' => 'new category name',
        ];

        $response = $this->actingAsJWT($admin)
            ->putJson("{$this->apiBase}/categories/{$nonExistentId}", $data);

        $response->assertStatus(404);
    }

    public function test_admin_can_delete_category(): void
    {
        $admin = User::factory()
            ->asAdmin()
            ->create();

        $category = Category::factory()
            ->create();

        $this->actingAsJWT($admin)
            ->deleteJson("{$this->apiBase}/categories/{$category->id}")
            ->assertStatus(204);
    }

    public function test_admin_cannot_delete_nonexistent_category(): void
    {
        $admin = User::factory()
            ->asAdmin()
            ->create();

        $nonExistentId = 999999;

        $this->actingAsJWT($admin)
            ->deleteJson("{$this->apiBase}/categories/{$nonExistentId}")
            ->assertStatus(404);
    }
}
