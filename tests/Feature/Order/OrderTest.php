<?php

namespace Tests\Feature\Order;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected string $apiBase = '/api/v1/orders';

    /**
     * A basic feature test example.
     */
    public function test_user_can_create_order_from_cart(): void
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
            ->for($product)
            ->for($cart)
            ->create([
                'quantity' => 2,
            ]);

        $response = $this->actingAsJWT($user)
            ->postJson("{$this->apiBase}", [
                'delivery_type' => 'courier',
                'delivery_region' => 'Kirov region',
                'delivery_city' => 'Kirov',
                'delivery_street' => 'Lenina',
                'delivery_house' => '28',
                'delivery_entrance' => '2',
                'delivery_apartment' => '63',
                'delivery_postal_code' => '123456',
            ]);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'delivery_type',
                'description',
                'status',
                'delivery_address' => [
                    'delivery_region',
                    'delivery_city',
                    'delivery_street',
                    'delivery_house',
                    'delivery_entrance',
                    'delivery_apartment',
                    'delivery_postal_code',
                ],
                'items',
                'total_quantity',
                'total_price',
                'created_at',
            ],
        ])
            ->assertJsonFragment([
                'status' => 'created',
            ])
            ->assertStatus(201);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('order_products', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_price' => $product->price,
            'quantity' => 2,
        ]);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
        ]);
    }

    public function test_user_cannot_create_order_with_empty_cart(): void
    {
        $user = User::factory()
            ->asUser()
            ->create();

        $response = $this->actingAsJWT($user)
            ->postJson("{$this->apiBase}", [
                'delivery_type' => 'courier',
                'delivery_region' => 'Kirov region',
                'delivery_city' => 'Kirov',
                'delivery_street' => 'Lenina',
                'delivery_house' => '28',
                'delivery_entrance' => '2',
                'delivery_apartment' => '63',
                'delivery_postal_code' => '123456',
            ]);

        $response
            ->assertJsonFragment([
                'message' => 'Cart is empty.',
            ])
            ->assertStatus(422);

        $this->assertDatabaseMissing('orders', [
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_get_order_list(): void
    {
        $user = User::factory()
            ->asUser()
            ->create();

        $firstProduct = Product::factory()
            ->create();

        $secondProduct = Product::factory()
            ->create();

        $firstOrder = Order::factory()
            ->for($user)
            ->create();

        $firstOrder->products()->attach($firstProduct->id, [
            'quantity' => 1,
            'product_name' => $firstProduct->name,
            'product_price' => $firstProduct->price,
        ]);

        $firstOrder->products()->attach($secondProduct->id, [
            'quantity' => 1,
            'product_name' => $secondProduct->name,
            'product_price' => $secondProduct->price,
        ]);

        $secondOrder = Order::factory()
            ->for($user)
            ->create();

        $secondOrder->products()->attach($firstProduct->id, [
            'quantity' => 1,
            'product_name' => $firstProduct->name,
            'product_price' => $firstProduct->price,
        ]);

        $secondOrder->products()->attach($secondProduct->id, [
            'quantity' => 1,
            'product_name' => $secondProduct->name,
            'product_price' => $secondProduct->price,
        ]);

        $response = $this->actingAsJWT($user)
            ->getJson("{$this->apiBase}");

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'delivery_type',
                    'description',
                    'status',
                    'delivery_address' => [
                        'delivery_region',
                        'delivery_city',
                        'delivery_street',
                        'delivery_house',
                        'delivery_entrance',
                        'delivery_apartment',
                        'delivery_postal_code',
                    ],
                    'items',
                    'total_quantity',
                    'total_price',
                    'created_at',
                ],
            ],
        ])
            ->assertJsonCount(2, 'data')
            ->assertStatus(200);
    }

    public function test_guest_cannot_get_order_list(): void
    {
        $this->getJson("{$this->apiBase}")
            ->assertStatus(401);
    }

    public function test_user_can_get_specific_order(): void
    {
        $user = User::factory()
            ->asUser()
            ->create();

        $order = Order::factory()
            ->for($user)
            ->create();

        $product = Product::factory()->create();

        $order->products()->attach($product->id, [
            'quantity' => 2,
            'product_name' => $product->name,
            'product_price' => $product->price,
        ]);

        $response = $this->actingAsJWT($user)
            ->getJson("{$this->apiBase}/{$order->id}");

        $response->assertJsonStructure([
            'data' => [
                'id',
                'delivery_type',
                'description',
                'status',
                'delivery_address' => [
                    'delivery_region',
                    'delivery_city',
                    'delivery_street',
                    'delivery_house',
                    'delivery_entrance',
                    'delivery_apartment',
                    'delivery_postal_code',
                ],
                'items',
                'total_quantity',
                'total_price',
                'created_at',
            ],
        ])
            ->assertJsonFragment([
                'id' => $order->id,
                'status' => $order->status,
                'product_name' => $product->name,
                'quantity' => 2,
            ])
            ->assertStatus(200);
    }

    public function test_user_cannot_get_another_users_order(): void
    {
        $user = User::factory()
            ->asUser()
            ->create();

        $anotherUser = User::factory()
            ->asUser()
            ->create();

        $order = Order::factory()
            ->for($anotherUser)
            ->create();

        $response = $this->actingAsJWT($user)
            ->getJson("{$this->apiBase}/{$order->id}");

        $response->assertJsonFragment([
            'message' => 'Order not found.',
        ])
            ->assertStatus(404);
    }

    public function test_guest_cannot_get_specific_order(): void
    {
        $order = Order::factory()->create();

        $this->getJson("{$this->apiBase}/{$order->id}")
            ->assertStatus(401);
    }
}
