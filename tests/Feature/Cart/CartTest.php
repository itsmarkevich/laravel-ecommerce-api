<?php

namespace Tests\Feature\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected string $apiBase = '/api/v1/cart';

    /**
     * A basic feature test example.
     */
    public function test_user_can_get_or_create_cart(): void
    {
        $user = User::factory()
            ->asUser()
            ->create();

        $response = $this->actingAsJWT($user)
            ->getJson("{$this->apiBase}");

        $response->assertJsonStructure([
            'data' => [
                'id',
                'items',
                'total_quantity',
                'total_price',
            ],
        ])
            ->assertJsonFragment([
                'items' => [],
                'total_quantity' => 0,
                'total_price' => 0,
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_get_existing_cart_with_items(): void
    {
        $user = User::factory()
            ->asUser()
            ->create();

        $category = Category::factory()
            ->create();

        $product = Product::factory()
            ->for($category)
            ->create([
                'price' => 100,
                'type' => 'pizza',
            ]);

        $cart = Cart::factory()
            ->for($user)
            ->create();

        CartItem::factory()
            ->for($cart)
            ->for($product)
            ->create([
                'quantity' => 2,
            ]);

        $response = $this->actingAsJWT($user)
            ->getJson("{$this->apiBase}");

        $response->assertJsonStructure([
            'data' => [
                'id',
                'items' => [
                    '*' => [
                        'id',
                        'product',
                        'quantity',
                        'total_price',
                    ],
                ],
                'total_quantity',
                'total_price',
            ],
        ])
            ->assertJsonFragment([
                'quantity' => 2,
                'total_quantity' => 2,
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
        ]);
    }

    public function test_guest_cannot_get_cart(): void
    {
        $this->getJson("{$this->apiBase}")
            ->assertStatus(401);
    }

    public function test_user_can_add_item_to_cart(): void
    {
        $user = User::factory()
            ->asUser()
            ->create();

        $category = Category::factory()
            ->create();

        $product = Product::factory()
            ->for($category)
            ->create([
                'price' => 100,
                'type' => 'pizza',
            ]);

        $response = $this->actingAsJWT($user)
            ->postJson("{$this->apiBase}/items", [
                'product_id' => $product->id,
                'quantity' => 2,
            ]);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'items' => [
                    '*' => [
                        'id',
                        'product',
                        'quantity',
                        'total_price',
                    ],
                ],
                'total_quantity',
                'total_price',
            ],
        ])
            ->assertJsonFragment([
                'quantity' => 2,
                'total_quantity' => 2,
                'total_price' => 200,
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
        ])
            ->assertDatabaseHas('cart_items', [
                'product_id' => $product->id,
                'quantity' => 2,
            ]);
    }

    public function test_user_cannot_add_invalid_item_to_cart(): void
    {
        $user = User::factory()
            ->asUser()
            ->create();

        $response = $this->actingAsJWT($user)
            ->postJson("{$this->apiBase}/items", [
                'product_id' => 999999,
                'quantity' => 0,
            ]);

        $response->assertJsonValidationErrors([
            'product_id',
            'quantity',
        ])
            ->assertStatus(422);
    }

    public function test_user_cannot_exceed_the_limit(): void
    {
        $user = User::factory()
            ->asUser()
            ->create();

        $product = Product::factory()
            ->create([
                'price' => 100,
                'type' => 'pizza',
            ]);

        $response = $this->actingAsJWT($user)
            ->postJson("{$this->apiBase}/items", [
                'product_id' => $product->id,
                'quantity' => 11,
            ]);

        $response->assertJsonFragment([
            'message' => 'Cart product limit exceeded.',
        ])
            ->assertStatus(422);
    }

    public function test_user_can_update_cart_item(): void
    {
        $user = User::factory()
            ->asUser()
            ->create();

        $product = Product::factory()
            ->create([
                'price' => 100,
                'type' => 'pizza',
            ]);

        $cart = Cart::factory()
            ->for($user)
            ->create();

        $cartItem = CartItem::factory()
            ->for($cart)
            ->for($product)
            ->create([
                'quantity' => 2,
            ]);

        $response = $this->actingAsJWT($user)
            ->patchJson("{$this->apiBase}/items/{$cartItem->id}", [
                'quantity' => 4,
            ]);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'items' => [
                    '*' => [
                        'id',
                        'product',
                        'quantity',
                        'total_price',
                    ],
                ],
                'total_quantity',
                'total_price',
            ],
        ])
            ->assertStatus(200);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'product_id' => $product->id,
            'quantity' => 4,
        ]);
    }

    public function test_user_cannot_update_invalid_cart_item(): void
    {
        $user = User::factory()
            ->asUser()
            ->create();

        $anotherUser = User::factory()
            ->asUser()
            ->create();

        $cart = Cart::factory()
            ->for($anotherUser)
            ->create();

        $product = Product::factory()
            ->create();

        $cartItem = CartItem::factory()
            ->for($cart)
            ->for($product)
            ->create();

        $response = $this->actingAsJWT($user)
            ->patchJson("{$this->apiBase}/items/{$cartItem->id}", [
                'quantity' => 4,
            ]);

        $response->assertJsonFragment([
            'message' => 'Cart item not found.',
        ])
            ->assertStatus(404);
    }

    public function test_user_cannot_update_cart_item_exceed_the_limit(): void
    {
        $user = User::factory()
            ->asUser()
            ->create();

        $product = Product::factory()
            ->create([
                'price' => 100,
                'type' => 'pizza',
            ]);

        $cart = Cart::factory()
            ->for($user)
            ->create();

        $cartItem = CartItem::factory()
            ->for($cart)
            ->for($product)
            ->create([
                'quantity' => 2,
            ]);

        $response = $this->actingAsJWT($user)
            ->patchJson("{$this->apiBase}/items/{$cartItem->id}", [
                'quantity' => 11,
            ]);

        $response->assertJsonFragment([
            'message' => 'Cart product limit exceeded.',
        ])
            ->assertStatus(422);
    }

    public function test_user_can_remove_item_from_cart(): void
    {
        $user = User::factory()
            ->asUser()
            ->create();

        $product = Product::factory()
            ->create([
                'price' => 100,
                'type' => 'pizza',
            ]);

        $cart = Cart::factory()
            ->for($user)
            ->create();

        $cartItem = CartItem::factory()
            ->for($cart)
            ->for($product)
            ->create();

        $response = $this->actingAsJWT($user)
            ->deleteJson("{$this->apiBase}/items/{$cartItem->id}");

        $response->assertJsonStructure([
            'data' => [
                'id',
                'items',
                'total_quantity',
                'total_price',
            ],
        ])
            ->assertStatus(200);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_user_cannot_remove_another_users_cart_item(): void
    {
        $user = User::factory()
            ->asUser()
            ->create();

        $anotherUser = User::factory()
            ->asUser()
            ->create();

        $cart = Cart::factory()
            ->for($anotherUser)
            ->create();

        $product = Product::factory()
            ->create();

        $cartItem = CartItem::factory()
            ->for($cart)
            ->for($product)
            ->create();

        $response = $this->actingAsJWT($user)
            ->deleteJson("{$this->apiBase}/items/{$cartItem->id}");

        $response->assertStatus(404);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
        ]);
    }

    public function test_user_can_clear_cart_items(): void
    {
        $user = User::factory()
            ->asUser()
            ->create();

        $product = Product::factory()
            ->create();

        $cart = Cart::factory()
            ->for($user)
            ->create();

        $cartItem = CartItem::factory()
            ->for($cart)
            ->for($product)
            ->create();

        $response = $this->actingAsJWT($user)
            ->deleteJson("{$this->apiBase}");

        $response->assertJsonFragment([
                'items' => [],
                'total_quantity' => 0,
                'total_price' => 0,
            ])
        ->assertStatus(200);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_guest_cannot_clear_cart(): void
    {
        $response = $this->deleteJson("{$this->apiBase}/");

        $response->assertStatus(401);
    }
}
