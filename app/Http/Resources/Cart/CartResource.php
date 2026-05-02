<?php

namespace App\Http\Resources\Cart;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Cart
 */
class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'items' => CartItemResource::collection($this->whenLoaded('items')),
            'total_quantity' => $this->items->sum('quantity'),
            'total_price' => $this->items->sum(
                fn ($item) => $item->quantity * $item->product->price
            ),
        ];
    }
}
