<?php

namespace App\Services\Order;

use App\Exceptions\Order\EmptyCartException;
use App\Exceptions\Order\OrderNotFoundException;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * @param array{
     *     delivery_type: string,
     *     delivery_region?: string|null,
     *     delivery_city?: string|null,
     *     delivery_street?: string|null,
     *     delivery_house?: string|null,
     *     delivery_entrance?: string|null,
     *     delivery_apartment?: string|null,
     *     delivery_postal_code?: string|null,
     *     description?: string|null
     * } $data
     */
    public function createFromCart(User $user, array $data): Order
    {
        $cart = $user->cart()->with('items.product')->first();

        if ($cart === null || $cart->items->isEmpty()) {
            throw new EmptyCartException();
        }

        return DB::transaction(function () use ($user, $data, $cart): Order {
            $order = Order::query()->create([
                'user_id' => $user->id,
                'delivery_type' => $data['delivery_type'],
                'delivery_region' => $data['delivery_region'] ?? null,
                'delivery_city' => $data['delivery_city'] ?? null,
                'delivery_street' => $data['delivery_street'] ?? null,
                'delivery_house' => $data['delivery_house'] ?? null,
                'delivery_entrance' => $data['delivery_entrance'] ?? null,
                'delivery_apartment' => $data['delivery_apartment'] ?? null,
                'delivery_postal_code' => $data['delivery_postal_code'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => 'created',
            ]);

            foreach ($cart->items as $cartItem) {
                $order->products()->attach($cartItem->product_id, [
                    'quantity' => $cartItem->quantity,
                    'product_name' => $cartItem->product->name,
                    'product_price' => $cartItem->product->price,
                ]);
            }

            $cart->items()->delete();

            return $order->load('products');
        });
    }

    /**
     * @return Collection<int, Order>
     */
    public function getUserOrders(User $user): Collection
    {
        return $user->orders()
            ->with('products')
            ->latest()
            ->get();
    }

    public function getUserOrder(User $user, Order $order): Order
    {
        if ($order->user_id !== $user->id) {
            throw new OrderNotFoundException();
        }

        return $order->load('products');
    }
}
