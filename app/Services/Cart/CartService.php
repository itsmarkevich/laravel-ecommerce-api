<?php

namespace App\Services\Cart;

use App\Exceptions\Cart\CartItemNotFoundException;
use App\Exceptions\Cart\CartProductLimitExceededException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;

class CartService
{
    public function getOrCreateCart(User $user): Cart
    {
        $cart = Cart::query()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        return $cart->load('items.product');
    }

    public function addItem(User $user, int $productId, int $quantity): Cart
    {
        $product = Product::query()->findOrFail($productId);

        $cart = $this->getOrCreateCart($user);

        $currentQuantityByType = $cart->items
            ->filter(fn (CartItem $item) => $item->product->type === $product->type)
            ->sum('quantity');

        $limit = $product->type === 'pizza' ? 10 : 20;

        if ($currentQuantityByType + $quantity > $limit) {
            throw new CartProductLimitExceededException();
        }

        $cartItem = $cart->items
            ->firstWhere('product_id', $product->id);

        if ($cartItem instanceof CartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $quantity,
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        return $cart->load('items.product');
    }

    public function updateItem(User $user, CartItem $cartItem, int $quantity): Cart
    {
        $cartItem->load('cart', 'product');

        if ($cartItem->cart->user_id !== $user->id) {
            throw new CartItemNotFoundException();
        }

        $cart = $this->getOrCreateCart($user);

        $currentQuantityByType = $cart->items
            ->filter(fn (CartItem $item) => $item->product->type === $cartItem->product->type)
            ->sum('quantity');

        $newQuantityByType = $currentQuantityByType - $cartItem->quantity + $quantity;

        $limit = $cartItem->product->type === 'pizza' ? 10 : 20;

        if ($newQuantityByType > $limit) {
            throw new CartProductLimitExceededException();
        }

        $cartItem->update([
            'quantity' => $quantity,
        ]);

        return $cart->load('items.product');
    }

    public function removeItem(User $user, CartItem $cartItem): Cart
    {
        $cartItem->load('cart');

        if ($cartItem->cart->user_id !== $user->id) {
            throw new CartItemNotFoundException();
        }

        $cartItem->delete();

        return $this->getOrCreateCart($user);
    }

    public function clear(User $user): Cart
    {
        $cart = $this->getOrCreateCart($user);

        $cart->items()->delete();

        return $cart->load('items.product');
    }
}
