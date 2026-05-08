<?php

namespace App\Http\Resources\Order;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 */
class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'product_id' => $this->id,
            'product_name' => $this->pivot->product_name,
            'product_price' => $this->pivot->product_price,
            'quantity' => $this->pivot->quantity,
            'total_price' => $this->pivot->quantity * $this->pivot->product_price,
        ];
    }
}
