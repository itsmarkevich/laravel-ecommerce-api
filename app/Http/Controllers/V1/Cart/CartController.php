<?php

namespace App\Http\Controllers\V1\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Resources\Cart\CartResource;
use App\Models\CartItem;
use App\Models\User;
use App\Services\Cart\CartService;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    )
    {
    }

    public function index(): CartResource
    {
        /** @var User $user */
        $user = auth()->user();
        $cart = $this->cartService->getOrCreateCart($user);
        return new CartResource($cart);
    }

    public function store(AddCartItemRequest $request): CartResource
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validated();

        $cart = $this->cartService->addItem(
            $user,
            $validated['product_id'],
            $validated['quantity'],
        );

        return new CartResource($cart);
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem): CartResource
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validated();

        $cart = $this->cartService->updateItem($user, $cartItem, $validated['quantity']);
        return new CartResource($cart);
    }

    public function destroy(CartItem $cartItem): CartResource
    {
        /** @var User $user */
        $user = auth()->user();

        $cart = $this->cartService->removeItem($user, $cartItem);

        return new CartResource($cart);
    }

    public function clear(): CartResource
    {
        /** @var User $user */
        $user = auth()->user();

        $cart = $this->cartService->clear($user);

        return new CartResource($cart);
    }
}
