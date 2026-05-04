<?php

namespace App\Http\Controllers\V1\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Resources\Cart\CartResource;
use App\Models\CartItem;
use App\Models\User;
use App\Services\Cart\CartService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    )
    {
    }

    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $cart = $this->cartService->getOrCreateCart($user);

        return (new CartResource($cart))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function store(AddCartItemRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validated();

        $cart = $this->cartService->addItem(
            $user,
            $validated['product_id'],
            $validated['quantity'],
        );

        return (new CartResource($cart))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
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

    public function clear(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $cart = $this->cartService->clear($user);

        return (new CartResource($cart))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }
}
