<?php

namespace App\Http\Resources\Order;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Order
 */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'delivery_type' => $this->delivery_type,
            'description' => $this->description,
            'status' => $this->status,
            'delivery_address' => [
                'delivery_region' => $this->delivery_region,
                'delivery_city' => $this->delivery_city,
                'delivery_street' => $this->delivery_street,
                'delivery_house' => $this->delivery_house,
                'delivery_entrance' => $this->delivery_entrance,
                'delivery_apartment' => $this->delivery_apartment,
                'delivery_postal_code' => $this->delivery_postal_code,
            ],
            'items' => OrderItemResource::collection($this->whenLoaded('products')),
            'total_quantity' => $this->products->sum(fn (Product $product): int => $product->pivot->quantity),
            'total_price' => $this->products->sum(
                fn (Product $product): float => ($product->pivot->quantity * $product->pivot->product_price),
            ),
            'created_at' => $this->created_at,
        ];
    }
}
