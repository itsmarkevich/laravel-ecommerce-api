<?php

namespace App\Http\Controllers\V1\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Models\User;
use App\Services\Order\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    )
    {
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validated();

        $order = $this->orderService->createFromCart($user, $validated);

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function index(): ResourceCollection
    {
        /** @var User $user */
        $user = auth()->user();

        $orders = $this->orderService->getUserOrders($user);

        return OrderResource::collection($orders);
    }

    public function show(Order $order): OrderResource
    {
        /** @var User $user */
        $user = auth()->user();

        $order = $this->orderService->getUserOrder($user, $order);

        return new OrderResource($order);
    }
}
